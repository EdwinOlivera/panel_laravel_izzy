<?php
/**
 * File name: EncargoAPIController.php
 * Last modified: 2020.05.31 at 19:34:40
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;

use App\Criteria\Encargos\EncargosOfStatusesCriteria;
use App\Criteria\Encargos\EncargosOfUserCriteria;
use App\Events\EncargosChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Encargo;
use App\Notifications\AssignedEncargo;
use App\Notifications\NewEncargo;
use App\Notifications\StatusChangedEncargos;
use App\Repositories\CartRepository;
use App\Repositories\EncargoRepository;
use App\Repositories\NotificationRepository; //Es necesario edicion especial
use App\Repositories\PaymentRepository;
use App\Repositories\ProductEncargoRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Stripe\Token;

/**
 * Class EncargoController
 * @package App\Http\Controllers\API
 */
class EncargoAPIController extends Controller
{
    /** @var  EncargoRepository */
    private $encargoRepository;
    /** @var  ProductEncargoRepository */
    private $productEncargoRepository;
    /** @var  CartRepository */
    private $cartRepository;
    /** @var  UserRepository */
    private $userRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;
    /** @var  NotificationRepository */
    private $notificationRepository;

    /**
     * EncargoAPIController constructor.
     * @param EncargoRepository $encargoRepo
     * @param ProductEncargoRepository $productEncargoRepository
     * @param CartRepository $cartRepo
     * @param PaymentRepository $paymentRepo
     * @param NotificationRepository $notificationRepo
     * @param UserRepository $userRepository
     */
    public function __construct(EncargoRepository $encargoRepo, ProductEncargoRepository $productEncargoRepository, CartRepository $cartRepo, PaymentRepository $paymentRepo, NotificationRepository $notificationRepo, UserRepository $userRepository)
    {
        $this->encargoRepository = $encargoRepo;
        $this->productEncargoRepository = $productEncargoRepository;
        $this->cartRepository = $cartRepo;
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepo;
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Encargo.
     * GET|HEAD /Encargos
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->encargoRepository->pushCriteria(new RequestCriteria($request));
            $this->encargoRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->encargoRepository->pushCriteria(new EncargosOfStatusesCriteria($request));
            $this->encargoRepository->pushCriteria(new EncargosOfUserCriteria(auth()->id()));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $encargos = $this->encargoRepository->all();

        return $this->sendResponse($encargos->toArray(), 'Encargos retrieved successfully');
        // return "Es aqui";
    }

    /**
     * Display the specified Encargo.
     * GET|HEAD /Encargos/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Encargo $encargo */
        if (!empty($this->encargoRepository)) {
            try {
                $this->encargoRepository->pushCriteria(new RequestCriteria($request));
                $this->encargoRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $encargo = $this->encargoRepository->findWithoutFail($id);
        }

        if (empty($encargo)) {
            return $this->sendError('Encargo not found');
        }

        return $this->sendResponse($encargo->toArray(), 'Encargo retrieved successfully');

    }

    /**
     * Store a newly created Encargo in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $payment = $request->only('payment');
        // return $payment ;
        if (isset($payment['payment']) && $payment['payment']['method']) {
            // if ($payment['payment']['method'] == "Credit Card (Stripe Gateway)") {
            //     return $this->stripPayment($request);
            // }
            if ($payment['payment']['method'] == "Tarjeta") {
                return $this->paymentFac($request);
            } else if ($payment['payment']['method'] == "Efectivo") {
                return $this->cashPaymentEncargo($request);
            } else {
                return $this->cashPayment($request);
            }
        } else {
            return 'No entro en las opciones debido a que no existe la propiedad $payment["payment"]';
            // return $request->all();

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
                    $encargo = $this->encargoRepository->create(
                        $request->only('user_id', 'encargo_status_id', 'tax', 'hint')
                    );
                } else {
                    $encargo = $this->encargoRepository->create(
                        $request->only('user_id', 'encargo_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
                    );
                }
                foreach ($input['products'] as $productEncargo) {
                    $productEncargo['encargo_id'] = $encargo->id;
                    $amount += $productEncargo['price'] * $productEncargo['quantity'];
                    $this->productEncargoRepository->create($productEncargo);
                }
                $amount += $encargo->delivery_fee;
                $amountWithTax = $amount + ($amount * $encargo->tax / 100);
                $charge = $user->charge((int) ($amountWithTax * 100), ['source' => $stripeToken]);
                $payment = $this->paymentRepository->create([
                    "user_id" => $input['user_id'],
                    "description" => trans("lang.payment_encargo_done"),
                    "price" => $amountWithTax,
                    "status" => $charge->status, // $charge->status
                    "method" => $input['payment']['method'],
                ]);
                $this->encargoRepository->update(['payment_id' => $payment->id], $encargo->id);

                $this->cartRepository->deleteWhere(['user_id' => $encargo->user_id]);

                Notification::send($encargo->productEncargos[0]->product->market->users, new NewEncargo($encargo));
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($encargo->toArray(), __('lang.saved_successfully', ['operator' => __('lang.encargo')]));
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
            $encargo = $this->encargoRepository->create(
                $request->only('user_id', 'encargo_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
            );
            Log::info($input['products']);
            foreach ($input['products'] as $productEncargo) {
                $productEncargo['encargo_id'] = $encargo->id;
                $amount += $productEncargo['price'] * $productEncargo['quantity'];
                $this->productEncargoRepository->create($productEncargo);
            }
            $amount += $encargo->delivery_fee;
            $amountWithTax = $amount + ($amount * $encargo->tax / 100);
            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'],
                "description" => trans("lang.payment_encargo_waiting"),
                "price" => $amountWithTax,
                "status" => 'Waiting for Client',
                "method" => $input['payment']['method'],
            ]);

            $this->encargoRepository->update(['payment_id' => $payment->id], $encargo->id);

            $this->cartRepository->deleteWhere(['user_id' => $encargo->user_id]);

            Notification::send($encargo->productEncargos[0]->product->market->users, new NewEncargo($encargo));

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($encargo->toArray(), __('lang.saved_successfully', ['operator' => __('lang.encargo')]));
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function cashPaymentEncargo(Request $request)
    {
        $input = $request->all();

        try {
            $encargo = $this->encargoRepository
                ->create(
                    $request->only(
                        'user_id',
                        'tel',
                        'monto_base',
                        'encargo_status_id',
                        'active',
                        'direccion_a',
                        'lat_a',
                        'lng_a',
                        'hacer_repartidor_a',
                        'descripcion_a',
                        'direccion_b',
                        'lat_b',
                        'lng_b',
                        'descripcion_b',
                        'hacer_repartidor_b',
                        'pay_mode',
                        'monto',
                        'monto_extra',
                        'cant_dist_extra',
                        'distan_max',
                        'distancia_puntos',
                        'pagada',
                        'dentro_rango',
                        'key_image',
                        'fecha_modi',
                        'nombre_mandadito',
                        'tel_movil_mandadito',
                        'direccion_mandadito',
                        'nombre_mandadito_b',
                        'tel_movil_mandadito_b',
                        'direccion_mandadito_b',
                        'order_number_fac'
                    )
                );

            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'],
                "description" => trans("lang.payment_order_waiting"),
                "price" => $input['monto'],
                "status" => 'Waiting for Client',
                "method" => $input['payment']['method'],
            ]);

            $this->encargoRepository->update(['payment_id' => $payment->id], $encargo->id);
            Flash::success('Nuevo encargo creado. Es necesario que fuera asignado');

            // $this->cartRepository->deleteWhere(['user_id' => $encargo->user_id]);

            // Notification::send($this->userRepository, new NewEncargo($encargo));

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($encargo->toArray(), __('lang.saved_successfully', ['operator' => __('encargo')]));
    }

    /**
     * Update the specified Encargo in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {

        $oldEncargo = $this->encargoRepository->findWithoutFail($id);
        if (empty($oldEncargo)) {
            return $this->sendError('Encargo not found');
        }
        $oldStatus = $oldEncargo->payment->status;
        $input = $request->all();

        try {
            $encargo = $this->encargoRepository->update($input, $id);
            if (isset($input['active'])) {
                $encargo['active'] = $input['active'];
            }
            if (isset($input['encargo_status_id']) && $input['encargo_status_id'] == 5 && !empty($encargo)) {
                $this->paymentRepository->update(['status' => 'Paid'], $encargo['payment_id']);
            }
            event(new EncargosChangedEvent($oldStatus, $encargo));

            if (setting('enable_notifications', false)) {
                if (isset($input['encargo_status_id']) && $input['encargo_status_id'] != $oldEncargo->encargo_status_id) {
                    Notification::send([$encargo->user], new StatusChangedEncargos($encargo));
                }

                if (isset($input['driver_id']) && ($input['driver_id'] != $oldEncargo['driver_id'])) {
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new AssignedEncargo($encargo));
                    }
                }
            }

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($encargo->toArray(), __('lang.saved_successfully', ['operator' => __('encargo')]));
    }
    private function paymentFac(Request $request)
    {
        $paymentTmp = $request->only('payment');

        $input = $request->all();

        try {
            $encargo = $this->encargoRepository
                ->create(
                    $request->only(
                        'user_id',
                        'tel',
                        'monto_base',
                        'encargo_status_id',
                        'active',
                        'direccion_a',
                        'lat_a',
                        'lng_a',
                        'hacer_repartidor_a',
                        'descripcion_a',
                        'direccion_b',
                        'lat_b',
                        'lng_b',
                        'descripcion_b',
                        'hacer_repartidor_b',
                        'pay_mode',
                        'monto',
                        'monto_extra',
                        'cant_dist_extra',
                        'distan_max',
                        'distancia_puntos',
                        'pagada',
                        'dentro_rango',
                        'key_image',
                        'fecha_modi',
                        'nombre_mandadito',
                        'tel_movil_mandadito',
                        'direccion_mandadito',
                        'nombre_mandadito_b',
                        'tel_movil_mandadito_b',
                        'direccion_mandadito_b',
                        'order_number_fac'
                    )
                );
            // return $encargo;

            // return $request->all();

            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'],
                "description" => trans("lang.payment_order_waiting"),
                "price" => $input['monto'],
                "status" => 'Paid',
                "method" => $input['payment']['method'],
            ]);
            $this->encargoRepository->update(['payment_id' => $payment->id], $encargo->id);
            Flash::success('Nuevo encargo creado. Es necesario que sea asignado');

            // $this->cartRepository->deleteWhere(['user_id' => $encargo->user_id]);

            // Notification::send($this->userRepository, new NewEncargo($encargo));

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($encargo->toArray(), __('lang.saved_successfully', ['operator' => __('encargo')]));
    }
}
