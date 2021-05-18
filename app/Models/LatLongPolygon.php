<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class LatLongPolygon
 * @package App\Models
 * @version December 6, 2019, 1:57 pm UTC
 *
 */
class LatLongPolygon extends Model
{

    public $table = 'latlong_polygon';

    public $fillable = [
        'latitude',
        'longitude',
        'polygon_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',

    ];

    // /**
    //  * Validation rules
    //  *
    //  * @var array
    //  */
    // public static $rules = [

    // ];

}
