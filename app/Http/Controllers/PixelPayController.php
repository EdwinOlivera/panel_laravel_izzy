<?php
/**
 * File name: PixelPayController.php
 * Last modified: 2020.09.12 
 * Author: Edwin Olivera
 * Copyright (c) 2020
 */

namespace App\Http\Controllers;

use Flash;
use DB;
use App\Invoice;
use App\IPNStatus;
use App\Item;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\NewOrder;
use App\Repositories\CartRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProductOrderRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;

class PixelPayController extends ParentOrderController
{
    /**
     * @var ExpressCheckout
     */
    protected $provider;

    public function __init()
    {

    }

    public function index()
    {
        // return view('welcome');
    }
    public function conversionDeDatos(Request $request){
        
        //Censeguir los datos importantes para pixelpay
        $pixelpay_secret=DB::table('app_settings')->where('key','pixelpay_secret')->first();
        $pixelpay_key = DB::table('app_settings')->where('key', 'pixelpay_key')->first();
        //Consigue la ultima orden para determinar el ID de la proxima orden 
        $last_order            = DB::table('orders')->orderBy('id')->limit('1')->get('id');
        $request['last_order'] = $last_order->last();
 
        $request['pixelpay_secret'] = $pixelpay_secret;
        $request['pixelpay_key']    = $pixelpay_key;
        
        return  $request->all();
    }

    public function cancelPixelPay(Request $request)
    {
        
        return view('pixelpay.cancel');

    }

    public function completePixelPay(Request $request)
    {
        $order_id_self            = $request['order_id_self'];
        $pixelpay_secret          =DB::table('app_settings')->where('key','pixelpay_secret')->first();
        $pixelpay_key             = DB::table('app_settings')->where('key', 'pixelpay_key')->first();
        $datosConcatenados        = $order_id_self.'|'.$pixelpay_key->value.'|'.$pixelpay_secret->value;
        $md5Convertido            = md5($datosConcatenados);
        $request['md5Convertido'] = $md5Convertido;

        if (strcmp($request['md5Convertido'], $request['paymentHash']) === 0){
            $request['paymentHashIdenticos'] = true;
        }else{
            $request['paymentHashIdenticos'] = false;

        }
        // return $request->all();
        // return redirect(route('pixelpay.complete'));
        return view('pixelpay.complete');

    }
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getExpressCheckout(Request $request)
    {
        $str = 'This is an encoded string';
        base64_encode($str);
        $requestData = json_decode($request->getContent(), true);
        $requestData["Campo"] = base64_encode($str);
        $request["Campo"] = $requestData["Campo"];
        return  $request->all();

       
    }

    
}

