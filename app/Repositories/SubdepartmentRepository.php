<?php

namespace App\Repositories;

use App\Models\Subdepartment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SubdepartmentRepository
 * @package App\Repositories
 * @version April 11, 2020, 1:57 pm UTC
 *
 * @method Subdepartment findWithoutFail($id, $columns = ['*'])
 * @method Subdepartment find($id, $columns = ['*'])
 * @method Subdepartment first($columns = ['*'])
*/
class SubdepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'market_id',
        'active',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Subdepartment::class;
    }
}
