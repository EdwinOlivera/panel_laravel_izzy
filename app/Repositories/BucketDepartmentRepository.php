<?php

namespace App\Repositories;

use App\Models\BucketDepartment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BucketDepartmentRepository
 * @package App\Repositories
 * @version April 11, 2020, 1:57 pm UTC
 *
 * @method BucketDepartment findWithoutFail($id, $columns = ['*'])
 * @method BucketDepartment find($id, $columns = ['*'])
 * @method BucketDepartment first($columns = ['*'])
*/
class BucketDepartmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'description',
        'active',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BucketDepartment::class;
    }
}
