<?php

namespace App\Repositories;

use App\Models\PolygonZone;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PolygonZoneRepository
 * @package App\Repositories
 * @version December 6, 2019, 1:57 pm UTC
 *
 * @method PolygonZone findWithoutFail($id, $columns = ['*'])
 * @method PolygonZone find($id, $columns = ['*'])
 * @method PolygonZone first($columns = ['*'])
 */
class PolygonZoneRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nombre',
        'editor',
        'creador',

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PolygonZone::class;
    }
   
}
