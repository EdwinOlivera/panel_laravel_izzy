<?php

namespace App\Repositories;

use App\Models\Section;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SubdepartmentRepository
 * @package App\Repositories
 * @version April 11, 2020, 1:57 pm UTC
 *
 * @method Section findWithoutFail($id, $columns = ['*'])
 * @method Section find($id, $columns = ['*'])
 * @method Section first($columns = ['*'])
*/
class SectionRepository extends BaseRepository
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
        return Section::class;
    }
}
