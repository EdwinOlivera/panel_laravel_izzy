<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CartController
 * @package App\Http\Controllers\API
 */

class CartAPIController extends Controller
{
    /** @var  CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepo)
    {
        $this->cartRepository = $cartRepo;
    }

    /**
     * Display a listing of the Cart.
     * GET|HEAD /carts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        try {
            $this->cartRepository->pushCriteria(new RequestCriteria($request));
            $this->cartRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $carts = $this->cartRepository->all();
        $cartsArray = $carts->toArray();
        $cartsArrayFinal = [];
        foreach ($cartsArray as $cartSingle) {
            $idProduct = $cartSingle['product']['id'];
            $options = $cartSingle['options'];
            // return $cartSingle;
            $idsOptions = [];
            $optionsFinal = [];
            foreach ($options as $option) {
                $idsOptions[] = $option['id'];
            }
            // $algo = DB::table('cart_options')->where('option_id', 4294967295)->pluck('quantity', 'option_group_id');
            // return $algo;
            // return $cartSingle['id'];
            $quantityOption = DB::table('cart_options')->whereIn('option_id', $idsOptions)->where('cart_id', '=', $cartSingle['id'])->pluck('quantity', 'option_group_id');
            // return $quantityOption;
            foreach ($options as $option) {
                if (isset($quantityOption[$option['option_group_id']])) {
                    $option['quantity'] = $quantityOption[$option['option_group_id']];
                    $optionsFinal[] = $option;
                }else{
                    $quantitysingleOption = DB::table('cart_options')->where('option_id', $option['id'])->where('cart_id', '=', $cartSingle['id'])->pluck('quantity');
                    $option['quantity'] = $quantitysingleOption[0];
                    $optionsFinal[] = $option;
                }
            }
            $cartSingle['options'] = $optionsFinal;
            $cartsArrayFinal[] = $cartSingle;

        }
        return $this->sendResponse($cartsArrayFinal, 'Carritos enviados exitosamente');
    }

    /**
     * Display a listing of the Cart.
     * GET|HEAD /carts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(Request $request)
    {
        try {
            $this->cartRepository->pushCriteria(new RequestCriteria($request));
            $this->cartRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $count = $this->cartRepository->count();

        return $this->sendResponse($count, 'Count retrieved successfully');
    }

    /**
     * Display the specified Cart.
     * GET|HEAD /carts/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Cart $cart */
        if (!empty($this->cartRepository)) {
            $cart = $this->cartRepository->findWithoutFail($id);
        }

        if (empty($cart)) {
            return $this->sendError('Cart not found');
        }

        return $this->sendResponse($cart->toArray(), 'Cart retrieved successfully');
    }
    /**
     * Store a newly created Cart in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        // return $input;
        try {
            if (isset($input['reset']) && $input['reset'] == '1') {
                // delete all items in the cart of current user
                $this->cartRepository->deleteWhere(['user_id' => $input['user_id']]);
            }
            $cart = $this->cartRepository->create($input);

            if (isset($input['optionGroups']) && isset($input['options'])) {
                $mapQuantity = $input['map_quantity_options'];

                // Convert JSON string to Array
                $quantityArray = json_decode($mapQuantity, true);
                $array = $input['optionGroups'];
                $arrayOption = $input['options'];
                $options = [];
                $optionsGroups = [];

                foreach ($arrayOption as $id) {
                    $options[] = (int) $id;
                }
                foreach ($array as $id) {
                    $optionsGroups[] = (int) $id;
                }
                $idGroupSelect = '0';
                $idsOptions = [];
                foreach ($optionsGroups as $idGroup) {
                    $idsOptions = DB::table('options_by_options_groups')->where('option_group_id', '=', $idGroup)->pluck('option_id');
                    foreach ($idsOptions as $idOption) {
                        if (in_array($idOption, $options)) {

                            $idGroupSelect = $idGroup;
                            $registro = DB::table('cart_options')->where('option_id', '=', $idOption)->where('cart_id', '=', $cart->id)->where('option_group_id', '=', '0')->exists();
                            if ($registro) {
                                if (isset($input['map_quantity_options'])) {

                                    DB::table('cart_options')->where('option_id', '=', $idOption)->where('cart_id', '=', $cart->id)->where('option_group_id', '=', '0')
                                        ->limit(1)
                                        ->update([
                                            "option_group_id" => $idGroupSelect,
                                            'quantity' => $quantityArray[$idOption],
                                        ]);

                                }
                            } else {
                                if (isset($input['map_quantity_options'])) {
                                    // print_r($quantityArray);        // Dump all data of the Array
                                    DB::table('cart_options')->insert([
                                        'option_id' => $idOption,
                                        'cart_id' => $cart->id,
                                        'option_group_id' => $idGroupSelect,
                                        'quantity' => $quantityArray[$idOption],

                                    ]);
                                }
                            }
                        }
                    }
                    $idGroupSelect = '0';
                }

            }

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($cart->toArray(), __('lang.saved_successfully', ['operator' => __('lang.cart')]));
    }

    /**
     * Update the specified Cart in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $cart = $this->cartRepository->findWithoutFail($id);

        if (empty($cart)) {
            return $this->sendError('Cart not found');
        }
        $input = $request->all();

        try {
            //    $input['options'] = isset($input['options']) ? $input['options'] : [];
            $cart = $this->cartRepository->update($input, $id);
            if (isset($input['optionGroups']) && isset($input['options'])) {
                // $arrayPre = explode("[", $input['optionGroups']);
                // $arrayPos = explode("]", $arrayPre[1]);
                // $array = explode(",", $arrayPos[0]);
                $array = $input['optionGroups'];

                // $arrayPreOption = explode("[", $input['options']);
                // $arrayPosOption = explode("]", $arrayPreOption[1]);
                // $arrayOption = explode(",", $arrayPosOption[0]);
                $arrayOption = $input['options'];
                $options = [];
                $optionsGroups = [];

                foreach ($arrayOption as $id) {
                    $options[] = (int) $id;
                }
                foreach ($array as $id) {
                    $optionsGroups[] = (int) $id;
                }
                $idGroupSelect = '0';
                $idsOptions = [];
                foreach ($optionsGroups as $idGroup) {
                    $idsOptions = DB::table('options_by_options_groups')->where('option_group_id', '=', $idGroup)->pluck('option_id');
                    foreach ($idsOptions as $idOption) {
                        if (in_array($idOption, $options)) {

                            $idGroupSelect = $idGroup;
                            $registro = DB::table('cart_options')->where('option_id', '=', $idOption)->where('cart_id', '=', $id)->where('option_group_id', '=', $idGroupSelect)->exists();
                            if ($registro) {

                                DB::table('cart_options')->where('option_id', '=', $idOption)->where('cart_id', '=', $id)->where('option_group_id', '=', $idGroupSelect)
                                    ->limit(1)
                                    ->update([
                                        "option_group_id" => $idGroupSelect,
                                    ]);
                            } else {
                                DB::table('cart_options')->insert([
                                    'option_id' => $idOption,
                                    'cart_id' => $id,
                                    'option_group_id' => $idGroupSelect,
                                ]);
                            }
                        }
                    }
                    $idGroupSelect = '0';
                }

            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($cart->toArray(), __('lang.saved_successfully', ['operator' => __('lang.cart')]));
    }

    /**
     * Remove the specified Favorite from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $cart = $this->cartRepository->findWithoutFail($id);

        if (empty($cart)) {
            return $this->sendError('Cart not found');

        }

        $cart = $this->cartRepository->delete($id);

        return $this->sendResponse($cart, __('lang.deleted_successfully', ['operator' => __('lang.cart')]));

    }

    
    /**
     * Remueve todos los carritos especifico de una usuaroo.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAll($id)
    {

        $this->cartRepository->deleteWhere(['user_id' => $id]);

        return $this->sendResponse([], __('Borrados todos los ', ['operator' => __('lang.cart')]));

    }

}
