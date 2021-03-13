<?php
/**
 * File name: Order.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Models;

use Eloquent as Model;

/**
 * Class Encargo
 * @package App\Models
 * @version August 31, 2019, 11:11 am UTC
 *
 * @property \App\Models\User user
 * @property \App\Models\DeliveryAddress deliveryAddress
 * @property \App\Models\Payment payment
 * @property \App\Models\OrderStatus orderStatus
 * @property \App\Models\ProductOrder[] productOrders
 * @property integer user_id
 * @property integer encargo_status_id
 * @property integer payment_id
 * @property double tax
 * @property double delivery_fee
 * @property string id
 * @property int delivery_address_id
 * @property string hint
 */
class EncargoSetting extends Model
{
    
    // // public $table = 'encargo_settings';


    // public $fillable = [
    //     'monto_base', 
    //     'monto_extra', 
    //     'rango_minimo', 
    //     'comision_repartidor', 
    //     'comision_repartidor',
    //     'habil_rang_extra',
    // ];

    // /**
    //  * The attributes that should be casted to native types.
    //  *
    //  * @var array
    //  */
    // protected $casts = [

    //     'monto_base' =>'double',
    //     'monto_extra' =>'double',
    //     'rango_minimo' =>'interger',
    //     'comision_repartidor' =>'double',
    
    // ];

    // /**
    //  * Validation rules
    //  *
    //  * @var array
    //  */
    // public static $rules = [
        
    // ];

    // /**
    //  * New Attributes
    //  *
    //  * @var array
    //  */
    // protected $appends = [
    //     'custom_fields',
        
    // ];

    // public function customFieldsValues()
    // {
    //     return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    // }

    // public function getCustomFieldsAttribute()
    // {
    //     $hasCustomField = in_array(static::class,setting('custom_field_models',[]));
    //     if (!$hasCustomField){
    //         return [];
    //     }
    //     $array = $this->customFieldsValues()
    //         ->join('custom_fields','custom_fields.id','=','custom_field_values.custom_field_id')
    //         ->where('custom_fields.in_table','=',true)
    //         ->get()->toArray();

    //     return convertToAssoc($array,'name');
    // }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  **/
    // public function user()
    // {
    //     return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    // }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  **/
    // public function driver()
    // {
    //     return $this->belongsTo(\App\Models\User::class, 'driver_id', 'id');
    // }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  **/
    // public function encargoStatus()
    // {
    //     return $this->belongsTo(\App\Models\EncargosStatus::class, 'encargo_status_id', 'id');
    // }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  **/
    // public function productEncargos()
    // {
    //     return $this->hasMany(\App\Models\ProductEncargo::class);
    // }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    //  **/
    // public function products()
    // {
    //     return $this->belongsToMany(\App\Models\Product::class, 'product_orders');
    // }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  **/
    // public function payment()
    // {
    //     return $this->belongsTo(\App\Models\Payment::class, 'payment_id', 'id');
    // }

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  **/
    // public function deliveryAddress()
    // {
    //     return $this->belongsTo(\App\Models\DeliveryAddress::class, 'delivery_address_id', 'id');
    // }
    
}