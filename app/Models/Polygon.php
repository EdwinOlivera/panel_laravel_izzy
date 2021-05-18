<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Polygon
 * @package App\Models
 * @version December 6, 2019, 1:57 pm UTC
 *
 * @property \App\Models\User user
 * @property string description
 * @property string address
 * @property string latitude
 * @property string longitude
 * @property boolean is_default
 * @property integer user_id
 */
class Polygon extends Model
{

    public $table = 'polygons';

    public $fillable = [
        'polygon_zone_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    // /**
    //  * Validation rules
    //  *
    //  * @var array
    //  */
    // public static $rules = [

    // ];

     /**
     * New Attributes
     *
     * @var array
     */
    protected $appends = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function latlng()
    {
        return $this->HasMany(\App\Models\LatLongPolygon::class, 'polygon_id')->orderBy('id');
    }

}
