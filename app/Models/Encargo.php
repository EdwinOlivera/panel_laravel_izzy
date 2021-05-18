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
 * Class Order
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
class Encargo extends Model
{
    
    public $table = 'encargos';


    public $fillable = [
        'user_id',
        'encargo_status_id',
        'payment_id',
        'delivery_address_id',
        'active',
        'tel',
        'driver_id',
        'direccion_a',
        'pagada',
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
        'assigned',
        'accepted',
        'monto',
        'cant_dist_extra',
        'pendiente',
        'wallet_saved',
        'monto_base',
        'monto_extra',
        'distan_max',
        'distancia_puntos',
        'dentro_rango',
        'key_image',
        'fecha_modi',
        'nombre_mandadito',
        'tel_movil_mandadito',
        'direccion_mandadito',
        'nombre_mandadito_b',
        'tel_movil_mandadito_b',
        'direccion_mandadito_b',
        'order_number_fac',
        'urls_img_a'
        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'encargo_status_id' => 'integer',
        'lat_a' => 'double',
        'lat_b' => 'double',
        'lng_a' => 'double',
        'lng_b' => 'double',
        'monto_extra' => 'double',
        'key_image'=>'string',
        'fecha_modi'=>'string',
        'order_number_fac'=>'string',
        'cant_dist_extra' => 'double',
        'distan_max' => 'double',
        'direccion_a' => 'string',
        'direccion_b' => 'string',
        'descripcion_b' => 'string',
        'descripcion_a' => 'string',
        'hacer_repartidor_b' => 'string',
        'hacer_repartidor_a' => 'string',
        'status' => 'string',
        'payment_id' => 'integer',
        'pay_mode' => 'integer',
        'delivery_address_id' => 'integer',
        'delivery_fee'=>'double',
        'distancia_puntos'=>'double',
        'active'=>'boolean',
        'pagada'=>'boolean',
        'dentro_rango'=>'boolean',
        'driver_id' => 'integer',
        'assigned' => 'boolean',
        
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
      
    ];

    /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        'custom_fields',
        
        
    ];

    public function customFieldsValues()
    {
        return $this->morphMany('App\Models\CustomFieldValue', 'customizable');
    }

    public function getCustomFieldsAttribute()
    {
        $hasCustomField = in_array(static::class,setting('custom_field_models',[]));
        if (!$hasCustomField){
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields','custom_fields.id','=','custom_field_values.custom_field_id')
            ->where('custom_fields.in_table','=',true)
            ->get()->toArray();

        return convertToAssoc($array,'name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function driver()
    {
        return $this->belongsTo(\App\Models\User::class, 'driver_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function encargoStatus()
    {
        return $this->belongsTo(\App\Models\EncargosStatus::class, 'encargo_status_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function productEncargos()
    {
        return $this->hasMany(\App\Models\ProductEncargo::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'product_orders');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function payment()
    {
        return $this->belongsTo(\App\Models\Payment::class, 'payment_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function deliveryAddress()
    {
        return $this->belongsTo(\App\Models\DeliveryAddress::class, 'delivery_address_id', 'id');
    }
    
}