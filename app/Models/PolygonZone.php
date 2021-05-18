<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class PolygonZone
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
class PolygonZone extends Model
{

    public $table = 'polygon_zone';

    public $fillable = [
        'nombre',
        'creador',
        'editor',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nombre' => 'string',
        'creador' => 'string',
        'editor' => 'string',
        'active' => 'boolean',

    ];

    // /**
    //  * Validation rules
    //  *
    //  * @var array
    //  */
    // public static $rules = [

    // ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function polygons()
    {
        return $this->HasMany(\App\Models\Polygon::class, 'polygon_zone_id')->orderBy('id');
    }

}
