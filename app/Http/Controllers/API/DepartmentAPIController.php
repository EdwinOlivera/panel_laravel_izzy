<?php
/**
 * File name: DepartmentAPIController.php
 * Last modified: 2020.05.04 at 09:04:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Market;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\MarketRepository;
use App\Repositories\SubdepartmentRepository;
use App\Repositories\UploadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class DeparmentController
 * @package App\Http\Controllers\API
 */

class DepartmentAPIController extends Controller
{
    /** @var  MarketRepository */
    private $marketRepository;

    /** @var  DepartmentRepository */
    private $departmentRepository;

    /** @var  SubdepartmentRepository */
    private $subdepartmentRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(MarketRepository $marketRepo,
        DepartmentRepository $departmentRepo,
        SubdepartmentRepository $subdepartmentRepo,
        CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo) {
        parent::__construct();
        $this->marketRepository = $marketRepo;
        $this->departmentRepository = $departmentRepo;
        $this->subdepartmentRepository = $subdepartmentRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;

    }

    /**
     * Display a listing of the Department.
     * GET|HEAD /markets
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->departmentRepository->pushCriteria(new RequestCriteria($request));
            $this->departmentRepository->pushCriteria(new LimitOffsetCriteria($request));

            $deparments = $this->departmentRepository->all();

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($deparments->toArray(), 'Markets enviados successfully');
    }

    /**
     * Display the specified Department.
     * GET|HEAD /markets/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Department $department */
        if (!empty($this->departmentRepository)) {
            try {
                $this->departmentRepository->pushCriteria(new RequestCriteria($request));
                $this->departmentRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $department = $this->departmentRepository->findWithoutFail($id);
            $departmentArray = $department->toArray();
            $idsSubdeparment = [];
            foreach ($departmentArray['subdepartments'] as $subdepartment) {
                if (!in_array($subdepartment['id'], $idsSubdeparment)) {

                    $idsSubdeparment[] = $subdepartment['id'];
                }
            }

            $checkActiveSubdepartment = DB::table('subdepartments_departments')->where('department_id', $id)->whereIn('subdepartment_id', $idsSubdeparment)->pluck('active', 'subdepartment_id')->toArray();
            $subdepartmentsFilter = [];
            foreach ($departmentArray['subdepartments'] as $subdepartment) {
                if($checkActiveSubdepartment[$subdepartment['id']]){

                    $subdepartmentsFilter [] = $subdepartment;
                }
                
            }
            $departmentArray['subdepartments'] = $subdepartmentsFilter;

        }

        if (empty($department)) {
            return $this->sendError('Departmento no encontrado');
        }

        return $this->sendResponse($departmentArray, 'Department enviado exitosamente');
    }

    /**
     * Store a newly created Department in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        if (auth()->user()->hasRole('manager')) {
            $input['users'] = [auth()->id()];
        }
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->departmentRepository->model());
        try {
            $department = $this->departmentRepository->create($input);
            $department->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($department, 'image');
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($department->toArray(), __('lang.saved_successfully', ['operator' => __('lang.deparment')]));
    }

    /**
     * Update the specified Department in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            return $this->sendError('Departmento no encontrado');
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->departmentRepository->model());
        try {
            $department = $this->departmentRepository->update($input, $id);
            $input['users'] = isset($input['users']) ? $input['users'] : [];
            $input['drivers'] = isset($input['drivers']) ? $input['drivers'] : [];
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($department, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $department->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($department->toArray(), __('lang.updated_successfully', ['operator' => __('lang.deparment')]));
    }

    /**
     * Remove the specified Department from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            return $this->sendError('Departmento no encontrado');
        }

        $department = $this->departmentRepository->delete($id);

        return $this->sendResponse($department, __('lang.deleted_successfully', ['operator' => __('lang.deparment')]));
    }
}
