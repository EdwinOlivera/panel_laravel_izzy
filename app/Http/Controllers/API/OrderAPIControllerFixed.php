<?php
/**
 * File name: OrderAPIControllerFixed.php
 * Last modified: 2020.05.31 at 19:34:40
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;

use App\Criteria\Orders\OrdersOfStatusesCriteria;
use App\Criteria\Orders\OrdersOfUserCriteria;
use App\Events\OrderChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\AcepptedAndAssignedOrder;
use App\Notifications\AssignedOrder;
use App\Notifications\NewOrder;
use App\Notifications\NotifyDriver;
use App\Notifications\OrderAccepted;
use App\Notifications\OrderNoAccept;
use App\Notifications\OrderRejected;
use App\Notifications\StatusChangedOrder;
use App\Repositories\CartRepository;
use App\Repositories\MarketRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OptionGroupRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProductOrderRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Stripe\Token;

/**
 * Class OrderController
 * @package App\Http\Controllers\API
 */
class OrderAPIControllerFixed extends Controller
{
    /** @var  OrderRepository */
    private $orderRepository;

    /** @var  MarketRepository */
    private $marketRepository;

    /** @var  OptionRepository */
    private $optionRepository;

    /** @var  OptionGroupRepository */
    private $optionGroupRepository;

    /** @var  ProductOrderRepository */
    private $productOrderRepository;

    /** @var  CartRepository */
    private $cartRepository;

    /** @var  UserRepository */
    private $userRepository;

    /** @var  PaymentRepository */
    private $paymentRepository;

    /** @var  NotificationRepository */
    private $notificationRepository;

    /**
     * OrderAPIControllerFixed constructor.
     * @param OrderRepository $orderRepo
     * @param ProductOrderRepository $productOrderRepository
     * @param CartRepository $cartRepo
     * @param PaymentRepository $paymentRepo
     * @param NotificationRepository $notificationRepo
     * @param UserRepository $userRepository
     */
    public function __construct(OrderRepository $orderRepo, MarketRepository $marketRepo, OptionGroupRepository $optionGroupRepo, OptionRepository $optionRepo, ProductOrderRepository $productOrderRepository, CartRepository $cartRepo, PaymentRepository $paymentRepo, NotificationRepository $notificationRepo, UserRepository $userRepository)
    {
        $this->marketRepository = $marketRepo;
        $this->orderRepository = $orderRepo;
        $this->optionRepository = $optionRepo;
        $this->optionGroupRepository = $optionGroupRepo;
        $this->productOrderRepository = $productOrderRepository;
        $this->cartRepository = $cartRepo;
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepo;
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Order.
     * GET|HEAD /orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfStatusesCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $orders = $this->orderRepository->all();
        $ordersArray = $orders->toArray();
        $ordersArrayFinal = [];

        foreach ($ordersArray as $orderSingle) {
            if (isset($orderSingle['product_orders'])) {
                $productsOrders = $orderSingle['product_orders'];
                $productOrderArrayFinal = [];
                foreach ($productsOrders as $productOrder) {
                    $idsOptions = [];
                    $idsOptionGroups = [];
                    $optionsFinal = [];
                    if (isset($productOrder['optiongroups'])) {

                        $productOrder['product']['optiongroups'] = $productOrder['optiongroups'];
                        // foreach ($productOrder['product']['optiongroups'] as $optionGroups) {
                        //     if (!in_array($optionGroups['id'], $idsOptionGroups)) {
                        //         $idsOptionGroups[] = $optionGroups['id'];
                        //     }
                        // }
                        // $optionGroupsRaw = DB::table('option_groups')->whereIn('id', $idsOptionGroups)->get()->toArray();
                        // $productOrder['product']['optiongroups'] =$optionGroupsRaw;
                    }
                    if (isset($productOrder['options'])) {

                        $options = $productOrder['options'];
                    } else {
                        $options = [];
                    }
                    $idProductOrder = $productOrder['id'];

                    foreach ($options as $option) {
                        $idsOptions[] = $option['id'];
                    }
                    $quantityOption = DB::table('product_order_options')->whereIn('option_id', $idsOptions)->where('product_order_id', '=', $idProductOrder)->pluck('quantity', 'option_group_id');
                    foreach ($options as $option) {
                        if (isset($quantityOption[$option['option_group_id']])) {

                            $option['quantity'] = $quantityOption[$option['option_group_id']];
                            $optionsFinal[] = $option;
                        }

                    }
                    $productOrder['options'] = $optionsFinal;
                    $productOrderArrayFinal[] = $productOrder;
                }
                $orderSingle['product_orders'] = $productOrderArrayFinal;

            } else {

            }

            $ordersArrayFinal[] = $orderSingle;
        }
        return $this->sendResponse($ordersArrayFinal, 'Ordenes Conseguidas successfully');
    }

    /**
     * Display the specified Order.
     * GET|HEAD /orders/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Order $order */
        if (!empty($this->orderRepository)) {
            try {
                $this->orderRepository->pushCriteria(new RequestCriteria($request));
                $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $order = $this->orderRepository->findWithoutFail($id);

        }

        if (empty($order)) {
            // return $this->sendError('Orden especifica no encontrada');
            return $this->sendResponse([], 'Orden especifica no encontrada');

        } else {
            $orderArray = $order->toArray();

            $ordersArrayFinal = [];

            if (isset($orderArray['product_orders'])) {

                $productsOrders = $orderArray['product_orders'];

                $productOrderArrayFinal = [];
                foreach ($productsOrders as $productOrder) {
                    $idsOptionGroups = [];

                    if (isset($productOrder['options'])) {
                        $options = $productOrder['options'];

                    } else {
                        $options = [];
                    }
                    if (!empty($productOrder['optiongroups'])) {
                        $productOrder['product']['optiongroups'] = $productOrder['optiongroups'];
                        foreach ($productOrder['product']['optiongroups'] as $optionGroups) {
                            if (!in_array($optionGroups['id'], $idsOptionGroups)) {
                                $idsOptionGroups[] = $optionGroups['id'];
                            }
                        }

                        $optionGroupsRaw = DB::table('option_groups')->whereIn('id', $idsOptionGroups)->get()->toArray();
                        $productOrder['product']['optiongroups'] = $optionGroupsRaw;
                    }

                    $idsOptions = [];
                    $idsOptionGroups = [];
                    $optionsFinal = [];

                    $idProductOrder = $productOrder['id'];

                    foreach ($options as $option) {
                        $idsOptions[] = (int) $option['id'];
                    }
                    $quantityOption = DB::table('product_order_options')->whereIn('option_id', $idsOptions)->where('unique_identify', '=', $productOrder['unique_identify'])->where('product_order_id', '=', $idProductOrder)->pluck('quantity', 'option_group_id');
                    $idOptionsGroup = DB::table('product_order_options')->where('product_order_id', '=', $idProductOrder)->pluck('option_group_id')->toArray();
                    $optionsFinal = [];

                    foreach ($idOptionsGroup as $idOG) {
                        $optionByOptionsGroup = DB::table('options_by_options_groups')->where('option_group_id', '=', $idOG)->pluck('option_group_id', 'option_id')->toArray();

                        foreach ($options as $option) {
                            if (isset($optionByOptionsGroup[$option['id']])) {
                                $option['option_group_id'] = $optionByOptionsGroup[$option['id']];
                                $option['quantity'] = $quantityOption[$option['option_group_id']];
                                $optionsFinal[] = $option;
                            }
                        }
                    }

                    $productOrder['options'] = $optionsFinal;
                    if (isset($productOrder['options'])) {
                        $productOrder['product']['options'] = $productOrder['options'];
                    }
                    $productOrderArrayFinal[] = $productOrder;
                    $orderArray['product_orders'] = $productOrderArrayFinal;

                    if (isset($request['crono'])) {
                        if ($request['crono']) {
                            $tiempoAContar = 00;
                            if (isset($request['desdeNotificaciones'])) {
                                $tiempoAContar = 30;
                            }

                            $timeNow = Carbon::now();
                            $timeUpdate = $order->updated_at;

                            $minutosActuales = $timeNow->format('i');
                            $segundosActuales = $timeNow->format('s');

                            $minutosPasados = $timeUpdate->format('i');
                            $segundosPasados = $timeUpdate->format('i');

                            if ($minutosActuales == $minutosPasados) {
                                if ($segundosActuales > $segundosPasados) {
                                    $tiempoAContar = $segundosActuales - $segundosPasados;
                                    if ($tiempoAContar >= 40) {
                                        $tiempoAContar = 0;
                                    } else {
                                        $tiempoAContar = 33 - $tiempoAContar;
                                    }
                                }
                            } else if ($minutosActuales - 1 == $minutosPasados) {
                                if ($minutosActuales > $minutosPasados) {
                                    if ($segundosActuales <= $segundosPasados) {
                                        $tiempoAContar = $segundosPasados - $segundosActuales;
                                        if ($tiempoAContar <= 30) {
                                            $tiempoAContar = 0;
                                        } else {
                                            $tiempoAContar = $tiempoAContar - 30;
                                        }
                                    } else {
                                        $tiempoAContar = 1;
                                    }
                                }
                            } else {
                                if (isset($request['desdeNotificaciones'])) {
                                    if (!$orderArray['driver_accept']) {
                                        $tiempoAContar = 10;
                                    } else {

                                        $tiempoAContar = 1;
                                    }
                                } else {

                                    $tiempoAContar = 0;
                                }
                            }
                            if ($orderArray['driver_accept']) {
                                $tiempoAContar = 1;
                            }
                            $orderArray['timer'] = $tiempoAContar;
                        }
                    }

                }
            }

        }
        if (isset($request['test'])) {
            $orderArray['sono'] =  true;
            // return $order->productOrders[0]->product->market->users;
            // Pruebas rapida de notificaciones para los gerentes
            Notification::send($order->productOrders[0]->product->market->users, new NewOrder($order));
        }
        return $this->sendResponse($orderArray, 'Orden especifica conseguida');

    }

    /**
     * Store a newly created Order in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $payment = $request->only('payment');
        if (isset($payment['payment']) && $payment['payment']['method']) {
            if ($payment['payment']['method'] == "Credit Card (Stripe Gateway)") {
                return $this->stripPayment($request);
            } else if ($payment['payment']['method'] == "Tarjeta") {
                return $this->paymentFac($request);
            } else {
                return $this->cashPayment($request);

            }
        } else {
            return 'No se guardo la orden';
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function stripPayment(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        try {
            $user = $this->userRepository->findWithoutFail($input['user_id']);
            if (empty($user)) {
                return $this->sendError('User not found');
            }
            $stripeToken = Token::create(array(
                "card" => array(
                    "number" => $input['stripe_number'],
                    "exp_month" => $input['stripe_exp_month'],
                    "exp_year" => $input['stripe_exp_year'],
                    "cvc" => $input['stripe_cvc'],
                    "name" => $user->name,
                ),
            ));
            if ($stripeToken->created > 0) {
                if (empty($input['delivery_address_id'])) {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'hint')
                    );
                } else {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
                    );
                }
                foreach ($input['products'] as $productOrder) {
                    $productOrder['order_id'] = $order->id;
                    $amount += $productOrder['price'] * $productOrder['quantity'];
                    $this->productOrderRepository->create($productOrder);
                }
                $amount += $order->delivery_fee;
                $amountWithTax = $amount + ($amount * $order->tax / 100);
                $charge = $user->charge((int) ($amountWithTax * 100), ['source' => $stripeToken]);
                $payment = $this->paymentRepository->create([
                    "user_id" => $input['user_id'],
                    "description" => trans("lang.payment_order_done"),
                    "price" => $amountWithTax,
                    "status" => $charge->status, // $charge->status
                    "method" => $input['payment']['method'],
                ]);
                $this->orderRepository->update(['payment_id' => $payment->id], $order->id);

                $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

                Notification::send($order->productOrders[0]->product->market->users, new NewOrder($order));
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function cashPayment(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        try {

            $order = $this->orderRepository->create(
                $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint', 'orden_programada', 'fecha_entrega', 'hora_entrega', 'descripcion', 'order_number_fac',
                    'fecha_creacion',
                    'enable_factura',
                    'nombre_user',
                    'id_factura',
                    'instructionProduct',
                    'instructionGeneral',
                    'rtn_user',
                    'mapa_fechas_cambio', 'carts','discount')
            );
            Log::info($input['products']);
            foreach ($input['products'] as $productOrder) {
                $productOrder['order_id'] = $order->id;
                $amount += $productOrder['price'] * $productOrder['quantity'];
                $this->productOrderRepository->create($productOrder);
            }
            $amount += $order->delivery_fee;
            $amountWithTax = $amount + ($amount * $order->tax / 100);
            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'],
                "description" => trans("lang.payment_order_waiting"),
                "price" => $amountWithTax,
                "status" => 'Waiting for Client',
                "method" => $input['payment']['method'],
            ]);

            $this->orderRepository->update(['payment_id' => $payment->id], $order->id);
            // NUEVO
            if (isset($input['products'])) {
                $quantityArray = [];
                $idsProductsOrder = DB::table('product_orders')->where('order_id', '=', $order->id)->pluck('id')->toArray();
                foreach ($idsProductsOrder as $idProductOrder) {
                    foreach ($input['products'] as $productOrder) {
                        $registroProductOrder = DB::table('product_orders')->where('id', '=', $idProductOrder)->where('product_id', '=', $productOrder['product_id'])->where('unique_identify', '=', $productOrder['unique_identify'])->where('order_id', '=', $order->id)->exists();

                        if ($registroProductOrder) {
                            if (isset($productOrder['map_quantity_options'])) {
                                $mapQuantity = $productOrder['map_quantity_options'];
                                // Convert JSON string to Array
                                $quantityArray = json_decode($mapQuantity, true);
                                if (isset($productOrder['optionGroups']) && isset($productOrder['options'])) {
                                    $arrayOptionGroups = $productOrder['optionGroups'];

                                    $arrayOption = $productOrder['options'];
                                    // $arrayOption = DB::table('product_order_options')->where('product_order_id', '=', $idProductOrder)
                                    //     ->pluck('option_id')->toArray();

                                    $options = [];
                                    $optionsGroups = [];

                                    foreach ($arrayOption as $id) {
                                        $options[] = (int) $id;
                                    }
                                    foreach ($arrayOptionGroups as $id) {
                                        $optionsGroups[] = (int) $id;
                                    }
                                    foreach ($optionsGroups as $idGroup) {
                                        $idsOptions = [];

                                        $idsOptions = DB::table('options_by_options_groups')->whereIn('option_id', $options)->where('option_group_id', '=', $idGroup)->pluck('option_id');
                                        foreach ($idsOptions as $idOption) {

                                            if (in_array($idOption, $options)) {

                                                $registro = DB::table('product_order_options')->where('product_order_id', '=', $idProductOrder)
                                                    ->where('option_id', '=', $idOption)
                                                    ->where('option_group_id', '=', '0')->exists();

                                                if ($registro) {
                                                    DB::table('product_order_options')->where('product_order_id', '=', $idProductOrder)
                                                        ->where('option_id', '=', $idOption)
                                                        ->where('option_group_id', '=', '0')
                                                        ->limit(1)
                                                        ->update([
                                                            "option_group_id" => $idGroup,
                                                            'quantity' => $quantityArray[$idOption],
                                                            'unique_identify' => $productOrder['unique_identify'],
                                                        ]);
                                                } else {
                                                    DB::table('product_order_options')->insert([
                                                        'option_id' => $idOption,
                                                        'product_order_id' => $idProductOrder,
                                                        'option_group_id' => $idGroup,
                                                        'quantity' => $quantityArray[$idOption],
                                                        'unique_identify' => $productOrder['unique_identify'],
                                                    ]);

                                                }

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }

            $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

            Notification::send($order->productOrders[0]->product->market->users, new NewOrder($order));

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }
    private function paymentFac(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        try {
            $order = $this->orderRepository->create(
                $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint', 'orden_programada', 'fecha_entrega', 'hora_entrega', 'descripcion', 'order_number_fac',
                    'fecha_creacion',
                    'enable_factura',
                    'nombre_user',
                    'id_factura',
                    'instructionProduct',
                    'instructionGeneral',
                    'rtn_user',
                    'mapa_fechas_cambio', 'carts','discount')
            );
            Log::info($input['products']);
            foreach ($input['products'] as $productOrder) {
                $productOrder['order_id'] = $order->id;
                $amount += $productOrder['price'] * $productOrder['quantity'];
                $this->productOrderRepository->create($productOrder);
            }
            $amount += $order->delivery_fee;
            $amountWithTax = $amount + ($amount * $order->tax / 100);
            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'],
                "description" => trans("lang.payment_order_done"),
                "price" => $amountWithTax,
                "status" => 'Paid',
                "method" => $input['payment']['method'],
            ]);

            $this->orderRepository->update(['payment_id' => $payment->id], $order->id);
            // NUEVO
            if (isset($input['products'])) {
                $quantityArray = [];
                $idsProductsOrder = DB::table('product_orders')->where('order_id', '=', $order->id)->pluck('id')->toArray();
                foreach ($idsProductsOrder as $idProductOrder) {
                    foreach ($input['products'] as $productOrder) {
                        $registroProductOrder = DB::table('product_orders')->where('id', '=', $idProductOrder)->where('product_id', '=', $productOrder['product_id'])->where('unique_identify', '=', $productOrder['unique_identify'])->where('order_id', '=', $order->id)->exists();

                        if ($registroProductOrder) {
                            if (isset($productOrder['map_quantity_options'])) {
                                $mapQuantity = $productOrder['map_quantity_options'];
                                // Convert JSON string to Array
                                $quantityArray = json_decode($mapQuantity, true);
                                if (isset($productOrder['optionGroups']) && isset($productOrder['options'])) {
                                    $arrayOptionGroups = $productOrder['optionGroups'];

                                    $arrayOption = $productOrder['options'];
                                    $options = [];
                                    $optionsGroups = [];

                                    foreach ($arrayOption as $id) {
                                        $options[] = (int) $id;
                                    }
                                    foreach ($arrayOptionGroups as $id) {
                                        $optionsGroups[] = (int) $id;
                                    }
                                    foreach ($optionsGroups as $idGroup) {
                                        $idsOptions = [];

                                        $idsOptions = DB::table('options_by_options_groups')->whereIn('option_id', $options)->where('option_group_id', '=', $idGroup)->pluck('option_id');
                                        foreach ($idsOptions as $idOption) {

                                            if (in_array($idOption, $options)) {

                                                $registro = DB::table('product_order_options')->where('product_order_id', '=', $idProductOrder)
                                                    ->where('option_id', '=', $idOption)
                                                    ->where('option_group_id', '=', '0')->exists();

                                                if ($registro) {
                                                    DB::table('product_order_options')->where('product_order_id', '=', $idProductOrder)
                                                        ->where('option_id', '=', $idOption)
                                                        ->where('option_group_id', '=', '0')
                                                        ->limit(1)
                                                        ->update([
                                                            "option_group_id" => $idGroup,
                                                            'quantity' => $quantityArray[$idOption],
                                                            'unique_identify' => $productOrder['unique_identify'],
                                                        ]);

                                                } else {
                                                    DB::table('product_order_options')->insert([
                                                        'option_id' => $idOption,
                                                        'product_order_id' => $idProductOrder,
                                                        'option_group_id' => $idGroup,
                                                        'quantity' => $quantityArray[$idOption],
                                                        'unique_identify' => $productOrder['unique_identify'],
                                                    ]);

                                                }

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }

            $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

            Notification::send($order->productOrders[0]->product->market->users, new NewOrder($order));

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }

    /**
     * Update the specified Order in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $oldOrder = $this->orderRepository->findWithoutFail($id);
        if (empty($oldOrder)) {
            return $this->sendError('Orden a actualizar no fue encontrada');
        }
        $oldStatus = $oldOrder->payment->status;
        $input = $request->all();

        try {

            if (isset($input['driver_id']) && !isset($input['order_rechazada_manual'])) {
                $arrayDriverId = [$input['driver_id']];
                $str_json = json_encode($arrayDriverId);
                $input['driver_array'] = $str_json;
            }

            $order = $this->orderRepository->update($input, $id);
            if (isset($input['order_status_id']) && $input['order_status_id'] == 4 && !empty($order)) {
                $this->paymentRepository->update(['status' => 'Paid'], $order['payment_id']);
            }
            event(new OrderChangedEvent($oldStatus, $order));

            if (setting('enable_notifications', false)) {

                if (isset($input['acepptedAndAssignedOrder'])) {
                    if ($input['acepptedAndAssignedOrder']) {
                        Notification::send([$order->user], new AcepptedAndAssignedOrder($order));
                    }
                } else {
                    if (isset($input['order_status_id']) && $input['order_status_id'] != $oldOrder->order_status_id) {
                        Notification::send([$order->user], new StatusChangedOrder($order));
                    }
                    if (isset($input['accepted']) && $input['accepted'] != $oldOrder->accepted) {
                        if ($input['accepted'] == true) {
                            Notification::send([$order->user], new OrderAccepted($order));
                        }
                    }
                }

                if (isset($input['NotifyDriver']) && isset($input['driver_id'])) {
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new NotifyDriver($order));
                    }
                }

                if (isset($input['explanatory_message']) && $input['explanatory_message'] != null) {
                    Notification::send([$order->user], new OrderRejected($order));
                }

                if (isset($input['test'])) {
                    // Esto es para hacer pruebas rapidas de notificaciones
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new AssignedOrder($order));
                    }
                }

                if (isset($input['driver_id']) && ($input['driver_id'] != $oldOrder['driver_id'])) {
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new AssignedOrder($order));
                    }
                }
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }
    public function revisarOrden(Request $request)
    {
        $input = $request->all();
        if (isset($input['market_id']) && isset($input['order_id'])) {
            $idMarket = $input['market_id'];
            $idOrder = $input['order_id'];
            $market = $this->marketRepository->findWithoutFail($idMarket);
            if (!empty($market)) {

                $IdsDrivers = $market->drivers()->where('accept_delivery', '=', '1')->pluck('id');
                $order = $this->orderRepository->findWithoutFail($idOrder);
                if (!empty($order)) {
                    $orderArray = $order->toArray();

                    if (isset($orderArray['driver_accept']) && isset($orderArray['driver_array']) && !$orderArray['driver_accept'] && !$orderArray['driver_accept']) {
                        // $arreglo = explode('[', $orderArray['driver_array']);
                        // $arreglo = explode(']', $arreglo[1]);
                        // $DriverArrayOrder = explode(',', $arreglo[0]);

                        $DriverArrayOrder = json_decode($orderArray['driver_array'], true);
                        $IdsDriverArrayOrder = [];
                        foreach ($DriverArrayOrder as $DriversId) {
                            $IdsDriverArrayOrder[] = (int) $DriversId;
                        }
                        $OrdenAceptada = false;
                        foreach ($IdsDrivers as $idDriver) {
                            sleep(32);
                            if (!$OrdenAceptada) {
                                $order = $this->orderRepository->findWithoutFail($idOrder);
                                $orderArray = $order->toArray();
                                if (!$orderArray['driver_accept']) {
                                    if (!in_array($idDriver, $IdsDriverArrayOrder)) {

                                        $IdsDriverArrayOrder[] = $idDriver;
                                        $arrayDriverId = $IdsDriverArrayOrder;
                                        $str_json = json_encode($arrayDriverId);
                                        $input['driver_array'] = $str_json;
                                        $input['driver_id'] = $idDriver;
                                        $order = $this->orderRepository->update($input, $idOrder);
                                        $driver = $this->userRepository->findWithoutFail($idDriver);
                                        if (!empty($driver)) {
                                            Notification::send([$driver], new AssignedOrder($order));
                                        }

                                    }
                                } else {
                                    $OrdenAceptada = true;
                                }
                            }

                        }

                    } else {
                        return $this->sendResponse([], 'No se entro');

                    }
                } else {
                    return $this->sendResponse([], 'Orden No encotrada');
                }
            } else {
                return $this->sendResponse([], 'Establecimiento no encontrado');

            }

            sleep(42);
            $request['whith'] = "user;productOrders;productOrders.product;productOrders.options;productOrders.optiongroups;orderStatus;deliveryAddress;payment";
            $this->orderRepository->pushCriteria(new RequestCriteria($request));

            $order = $this->orderRepository->findWithoutFail($idOrder);
            $orderArray = $order->toArray();
            if (!$orderArray['driver_accept']) {

                $usersAdmin = $this->userRepository->where('isAdmin', '=', '1')->get();

                Notification::send($usersAdmin, new OrderNoAccept($order));
                $input['driver_id'] = 0;
                $input['nobody_accepted'] = 1;
                $order = $this->orderRepository->update($input, $idOrder);
                //
            }
            return $this->sendResponse([], 'Orden verificada completamente');
        } else {
            return $this->sendResponse([], 'Orden No verificada');

        }
    }

    public function checkStatusOrder(Request $request)
    {
        $input = $request->all();
        if (isset($input['order_id'])) {
            $idOrder = $input['order_id'];
            $statusFinal = true;
            $order = $this->orderRepository->findWithoutFail($idOrder);
            if ($order->driver_accept) {
                $statusFinal = true;
            } else {
                $statusFinal = false;
            }

            return $this->sendResponse(['status' => $statusFinal], 'Orden Revisada');
        } else {
            return $this->sendError('No se especifico el ID de la orden');

        }
    }

    public function checkStatusOrderUser(Request $request)
    {
        $input = $request->all();
        if (isset($input['order_id'])) {
            $idOrder = $input['order_id'];
            $statusFinal = true;
            $order = $this->orderRepository->findWithoutFail($idOrder);
            if ($order->driver_accept || $order->accept) {
                $statusFinal = true;
            } else {
                $statusFinal = false;
            }

            return $this->sendResponse(['status' => $statusFinal], 'Orden Revisada');
        } else {
            return $this->sendError('No se especifico el ID de la orden');

        }
    }

}
