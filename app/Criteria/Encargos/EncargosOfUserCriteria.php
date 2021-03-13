<?php
/**
 * File name: EncargosOfUserCriteria.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Criteria\Encargos;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class EncargosOfUserCriteria.
 *
 * @package namespace App\Criteria\Encargos;
 */
class EncargosOfUserCriteria implements CriteriaInterface
{
    /**
     * @var User
     */
    private $userId;

    /**
     * EncargosOfUserCriteria constructor.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model;
        } else if (auth()->user()->hasRole('manager')) {
            return $model->newQuery()->where('encargos.user_id', $this->userId)
            ->groupBy('encargos.id');

        } else if (auth()->user()->hasRole('client')) {
            return $model->where('encargos.user_id', $this->userId)
                ->groupBy('encargos.id');
        } else if (auth()->user()->hasRole('driver')) {
            return $model->newQuery()->where('encargos.driver_id', $this->userId)
                ->groupBy('encargos.id');
        } else {
            return $model;
        }
    }
}
