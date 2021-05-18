<?php

namespace App\Repositories;

use App\Models\Polygon;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PolygonRepository
 * @package App\Repositories
 * @version December 6, 2019, 1:57 pm UTC
 *
 * @method Polygon findWithoutFail($id, $columns = ['*'])
 * @method Polygon find($id, $columns = ['*'])
 * @method Polygon first($columns = ['*'])
 */
class PolygonRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Polygon::class;
    }
   
}
