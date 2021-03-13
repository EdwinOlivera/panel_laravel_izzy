<?php

namespace App\Http\Controllers\API;


use App\Models\EncargosStatus;
use App\Repositories\EncargosStatusRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;

/**
 * Class EncargoStatusController
 * @package App\Http\Controllers\API
 */

class EncargoStatusAPIController extends Controller
{
    /** @var  EncargosStatusRepository */
    private $encargoStatusRepository;

    public function __construct(EncargosStatusRepository $encargoStatusRepo)
    {
        $this->encargoStatusRepository = $encargoStatusRepo;
    }

    /**
     * Display a listing of the EncargosStatus.
     * GET|HEAD /encargoStatuses
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->encargoStatusRepository->pushCriteria(new RequestCriteria($request));
            $this->encargoStatusRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $encargoStatuses = $this->encargoStatusRepository->all();

        return $this->sendResponse($encargoStatuses->toArray(), 'Encargo Statuses retrieved successfully');
    }

    /**
     * Display the specified EncargosStatus.
     * GET|HEAD /encargoStatuses/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var EncargosStatus $encargoStatus */
        if (!empty($this->encargoStatusRepository)) {
            $encargoStatus = $this->encargoStatusRepository->findWithoutFail($id);
        }

        if (empty($encargoStatus)) {
            return $this->sendError('Encargo Status not found');
        }

        return $this->sendResponse($encargoStatus->toArray(), 'Encargo Status retrieved successfully');
    }
}
