<?php

/**
 * File name: FACController.php
 * Last modified: 2020.09.12
 * Author: Edwin Olivera
 * Copyright (c) 2020
 */

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FACController extends ParentOrderController
{

    public function __init()
    { }

    public function Sign($passwd, $facId, $acquirerId, $orderNumber, $amount, $currency)
    {
        $stringtohash =
            $passwd . $facId . $acquirerId . $orderNumber . $amount . $currency;
        $hash = sha1($stringtohash, true);
        $signature = base64_encode($hash);
        return $signature;
    }

    public function conversionDeDatos(Request $request)
    {

        $response = [];

        $orderNumber = "facAPP" . substr(md5(uniqid()), 0, 12);

        $response['orderNumber'] = $orderNumber;
        // fac_merchant_password
        $fac_merchant_password = DB::table('app_settings')->where('key', 'fac_merchant_password')->first();
        $fac_merchant_id = DB::table('app_settings')->where('key', 'fac_merchant_id')->first();
        $base_url_fac = DB::table('app_settings')->where('key', 'base_url_fac')->first();
        $url_transaction_modification = DB::table('app_settings')->where('key', 'url_transaction_modification')->first();
        $base_url_fac_3d_secure = DB::table('app_settings')->where('key', 'base_url_fac_3d_secure')->first();
        $TransactionModification = DB::table('app_settings')->where('key', 'url_transaction_modification')->first();

        $facId = $fac_merchant_id->value;

        $password = $fac_merchant_password->value;

        // Acquirer is always 464748
        $acquirerId = '464748';

        // // 12 chars, always, no decimal place
        $amount = $request['amount'];

        // // 840 = USD, put your currency code here,  340 = HNL
        $currency = $request['currency_code_number'];

        $stringtohash = $password . $facId . $acquirerId . $orderNumber . $amount . $currency;
        $hash = sha1($stringtohash, true);
        $signature = base64_encode($hash);
        // url_transaction_modification
        $response['transaction_modification'] = $url_transaction_modification->value;
        $response['stringtohash'] = $stringtohash;
        $response['amount'] = $amount;
        $response['password'] = $password;
        $response['facId'] = $facId;
        $response['urlFAC'] = $base_url_fac->value;
        $response['url_transaction_modification'] = $TransactionModification->value;
        $response['urlFAC3DS'] = $base_url_fac_3d_secure->value;
        $response['signature'] = $signature;
        $response['empresa'] = 'LION_DELIVERY';
        $response['Hora'] = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];

        return $response;
    }
}
