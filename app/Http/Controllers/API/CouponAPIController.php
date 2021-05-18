<?php

namespace App\Http\Controllers\API;

use App\Criteria\Coupons\ValidCriteria;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Repositories\CouponRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class CouponController
 * @package App\Http\Controllers\API
 */

class CouponAPIController extends Controller
{
    /** @var  CouponRepository */
    private $couponRepository;

    public function __construct(CouponRepository $couponRepo)
    {
        $this->couponRepository = $couponRepo;
    }

    /**
     * Display a listing of the Coupon.
     * GET|HEAD /coupons
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->couponRepository->pushCriteria(new RequestCriteria($request));
            $this->couponRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->couponRepository->pushCriteria(new ValidCriteria());
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $coupons = $this->couponRepository->all();
        $couponsArray = $coupons->toArray();
        $couponsArrayFinal = [];
        foreach ($couponsArray as $coupon) {

            $coupon['used']++;
            if ($coupon['used'] <= $coupon['total_quantity']) {
                $registro = DB::table('coupons_users')->where('coupon_id', $coupon['id'])
                    ->where('user_id', '=', $request['user_id'])
                    ->exists();
                if ($registro) {

                    $cantidadUsadas = DB::table('coupons_users')->where('coupon_id', $coupon['id'])
                        ->where('user_id', '=', $request['user_id'])
                        ->get('used')->toArray();

                    if (isset($coupon['max_for_user'])) {
                        if ($cantidadUsadas[0]->used < $coupon['max_for_user']) {
                            $cantidadUsadas[0]->used++;
                            DB::table('coupons_users')->where('coupon_id', $coupon['id'])
                                ->where('user_id', '=', $request['user_id'])
                                ->update([
                                    "used" => $cantidadUsadas[0]->used,
                                ]);

                            Coupon::where("id", $coupon['id'])->update(["used" => $coupon['used']]);

                            $couponsArrayFinal[] = $coupon;
                        } else {

                            $couponsArrayFinal = [];
                        }
                    } else {
                        if ($cantidadUsadas[0]->used < $coupon['total_quantity']) {
                            $cantidadUsadas[0]->used++;
                            DB::table('coupons_users')->where('coupon_id', $coupon['id'])
                                ->where('user_id', '=', $request['user_id'])
                                ->update([
                                    "used" => $cantidadUsadas[0]->used,
                                    'max' => $coupon['total_quantity'],
                                ]);

                            Coupon::where("id", $coupon['id'])->update(["used" => $coupon['used']]);

                            $couponsArrayFinal[] = $coupon;
                        } else {

                            $couponsArrayFinal = [];
                        }
                    }

                } else {
                    if (isset($coupon['max_for_user'])) {
                        DB::table('coupons_users')->insert([
                            'coupon_id' => $coupon['id'],
                            'user_id' => $request['user_id'],
                            'max' => $coupon['max_for_user'],
                            'used' => 1,
                        ]);

                    } else {
                        DB::table('coupons_users')->insert([
                            'coupon_id' => $coupon['id'],
                            'user_id' => $request['user_id'],
                            'max' => $coupon['total_quantity'],
                            'used' => 1,
                        ]);

                    }
                    Coupon::where("id", $coupon['id'])->update(["used" => $coupon['used']]);

                    $couponsArrayFinal[] = $coupon;
                }
            } else {

                $couponsArrayFinal = [];
            }

        }
        return $this->sendResponse($couponsArrayFinal, 'Cupones conseguidos');
    }

    /**
     * Display the specified Coupon.
     * GET|HEAD /coupons/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Coupon $coupon */
        if (!empty($this->couponRepository)) {
            $coupon = $this->couponRepository->findWithoutFail($id);
        }

        if (empty($coupon)) {
            return $this->sendError('Coupon not found');
        }

        return $this->sendResponse($coupon->toArray(), 'Coupon retrieved successfully');
    }
}
