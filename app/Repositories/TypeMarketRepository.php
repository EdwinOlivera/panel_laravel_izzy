<?php

namespace App\Repositories;

use App\Models\TypeMarket;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TypeMarketRepository
 * @package App\Repositories
 * @version Febrary 16, 2021, 9:57 pm UTC-06
 *
 * @method Field findWithoutFail($id, $columns = ['*'])
 * @method Field find($id, $columns = ['*'])
 * @method Field first($columns = ['*'])
*/
class TypeMarketRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'enable'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TypeMarket::class;
    }
}
