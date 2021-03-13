<?php

namespace App\Repositories;

use App\Models\BucketSubdepartment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BucketSubdepartmentRepository
 * @package App\Repositories
 * @version April 11, 2020, 1:57 pm UTC
 *
 * @method BucketSubdepartment findWithoutFail($id, $columns = ['*'])
 * @method BucketSubdepartment find($id, $columns = ['*'])
 * @method BucketSubdepartment first($columns = ['*'])
*/
class BucketSubdepartmentRepository extends BaseRepository
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
        return BucketSubdepartment::class;
    }
}
