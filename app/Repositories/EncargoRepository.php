<?php

namespace App\Repositories;

use App\Models\Encargo;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EncargoRepository
 * @package App\Repositories
 * @version August 31, 2019, 11:11 am UTC
 *
 * @method Encargo findWithoutFail($id, $columns = ['*'])
 * @method Encargo find($id, $columns = ['*'])
 * @method Encargo first($columns = ['*'])
*/
class EncargoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'tel', 
        'monto_base',
        'encargo_status_id',
        'direccion_a',
        'direccion_b',
        'lat_a',
        'lat_b',
        'lng_a',
        'lng_b',
        'hacer_repartidor_a',
        'hacer_repartidor_b',
        'descripcion_a',
        'descripcion_b',
        'driver_id',
        'active',
        'assigned',
        'acceptada',
        'monto',
        'pagada',
        'distancia_puntos',
        'nombre_mandadito',
        'tel_movil_mandadito',
        'direccion_mandadito',
        'nombre_mandadito_b',
        'tel_movil_mandadito_b',
        'direccion_mandadito_b',
        'pay_mode',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Encargo::class;
    }
}
