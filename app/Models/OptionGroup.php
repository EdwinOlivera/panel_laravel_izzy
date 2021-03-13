<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @property \App\Models\Market market
 * Class OptionGroup
 * @package App\Models
 * @version April 6, 2020, 10:47 am UTC
 *
 * @property integer market_id
 * @property string name
 */
class OptionGroup extends Model
{

    public $table = 'option_groups';

    public $fillable = [
        'name',
        'cant_selectable',
        'multi',
        'market_id',
        'force_select',
        'name_admin',
        'id_producto',
        'active',

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'cant_selectable' => 'string',
        'muti' => 'string',
        'active' => 'boolean',
        'force_select' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'name_admin' => 'required',
        'cant_selectable' => 'required',

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
        $hasCustomField = in_array(static::class, setting('custom_field_models', []));
        if (!$hasCustomField) {
            return [];
        }
        $array = $this->customFieldsValues()
            ->join('custom_fields', 'custom_fields.id', '=', 'custom_field_values.custom_field_id')
            ->where('custom_fields.in_table', '=', true)
            ->get()->toArray();

        return convertToAssoc($array, 'name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function optionGroupsList()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'option_group_market_products');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     **/
    public function optionsList()
    {
        return $this->belongsToMany(\App\Models\Option::class, 'options_by_options_groups');
    }
}
