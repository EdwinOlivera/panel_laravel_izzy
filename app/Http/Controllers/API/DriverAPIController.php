<?php

namespace App\Http\Controllers\API;

use App\Criteria\Drivers\NearCriteriaDriver;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Repositories\DriverRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class DriverController
 * @package App\Http\Controllers\API
 */

class DriverAPIController extends Controller
{
    /** @var  DriverRepository */
    private $driverRepository;

    public function __construct(DriverRepository $driverRepo)
    {
        $this->driverRepository = $driverRepo;
    }

    /**
     * Display a listing of the Driver.
     * GET|HEAD /drivers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->driverRepository->pushCriteria(new RequestCriteria($request));
            $this->driverRepository->pushCriteria(new LimitOffsetCriteria($request));
            if ($request->has(['myLon', 'myLat', 'areaLon', 'areaLat'])) {
                $this->driverRepository->pushCriteria(new NearCriteriaDriver($request));
            }

            if ($request->has(['myLon', 'myLat', 'longPuntoA', 'latPuntoA'])) {
                $this->driverRepository->pushCriteria(new NearCriteriaDriver($request));
            }
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $drivers = $this->driverRepository->all();

        return $this->sendResponse($drivers->toArray(), 'Drivers retrieved successfully');
    }

    /**
     * Display the specified Driver.
     * GET|HEAD /drivers/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Driver $driver */
        if (!empty($this->driverRepository)) {
            $driver = $this->driverRepository->findWithoutFail($id);
        }

        if (empty($driver)) {
            return $this->sendError('Driver not found');
        }

        return $this->sendResponse($driver->toArray(), 'Driver retrieved successfully');
    }
}
