<?php

namespace App\Criteria\Encargos;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class EncargosOfStatusesCriteria.
 *
 * @package namespace App\Criteria\Encargos;
 */
class EncargosOfStatusesCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    private $request;

    /**
     * EncargosOfStatusesCriteria constructor.
     * @param array $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if (!$this->request->has('statuses')) 
        {
            return $model;
        } else {
            $statuses = $this->request->get('statuses');
            if (in_array('0', $statuses)) { 
                // means all statuses
                return $model;
            }
            return $model->whereIn('encargo_status_id', $this->request->get('statuses', []));
        }
    }
}
