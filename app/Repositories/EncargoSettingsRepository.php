<?php

namespace App\Repositories;

use App\Models\EncargoSetting;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EncargoSettingRepository
 * @package App\Repositories
 * @version August 31, 2019, 11:11 am UTC
 *
 * @method Encargo findWithoutFail($id, $columns = ['*'])
 * @method Encargo find($id, $columns = ['*'])
 * @method Encargo first($columns = ['*'])
*/
class EncargoSettingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'monto_base',
        'monto_extra',
        'rango_minimo',
        'comision_repartidor',
        'comision_repartidor',
        'habil_rang_extra',
        
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EncargoSetting::class;
    }
}
