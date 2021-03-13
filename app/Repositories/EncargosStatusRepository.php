<?php

namespace App\Repositories;

use App\Models\EncargosStatus;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EncargosStatusRepository
 * @package App\Repositories
 * @version August 29, 2019, 9:38 pm UTC
 *
 * @method EncargosStatus findWithoutFail($id, $columns = ['*'])
 * @method EncargosStatus find($id, $columns = ['*'])
 * @method EncargosStatus first($columns = ['*'])
*/
class EncargosStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EncargosStatus::class;
    }
}
