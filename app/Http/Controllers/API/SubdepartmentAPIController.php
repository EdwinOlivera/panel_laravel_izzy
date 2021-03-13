<?php
/**
 *
 * File name: SubdepartmentAPIController.php
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
 * Class SubdeparmentController
 * @package App\Http\Controllers\API
 */

class SubdepartmentAPIController extends Controller
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
            $this->subdepartmentRepository->pushCriteria(new RequestCriteria($request));
            $this->subdepartmentRepository->pushCriteria(new LimitOffsetCriteria($request));

            $deparments = $this->subdepartmentRepository->all();

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
        /** @var Department $subdepartment */
        if (!empty($this->subdepartmentRepository)) {
            try {
                $this->subdepartmentRepository->pushCriteria(new RequestCriteria($request));
                $this->subdepartmentRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);
            if (isset($subdepartment)) {

                $subdepartmentArray = $subdepartment->toArray();
                $idsProducts = [];
                $productsFilter = [];
                if (isset($subdepartmentArray['products'])) {

                    foreach ($subdepartmentArray['products'] as $product) {
                        if (!in_array($product['id'], $idsProducts)) {

                            $idsProducts[] = $product['id'];
                        }
                    }

                    $checkActiveProducts = DB::table('subdepartments_products')->where('subdepartment_id', $id)->whereIn('product_id', $idsProducts)->pluck('active', 'product_id')->toArray();
                    foreach ($subdepartmentArray['products'] as $product) {
                        if ($checkActiveProducts[$product['id']]) {

                            $productsFilter[] = $product;
                        }

                    }
                }
                $subdepartmentArray['products'] = $productsFilter;
            } else {
                return $this->sendResponse([], 'Subdepartmento enviado exitosamente');

            }

        }

        if (empty($subdepartment)) {
            return $this->sendError('Subdepartmento no encontrado');
        }

        return $this->sendResponse($subdepartmentArray, 'Department enviado exitosamente');
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
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
        try {
            $subdepartment = $this->subdepartmentRepository->create($input);
            $subdepartment->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($subdepartment, 'image');
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($subdepartment->toArray(), __('lang.saved_successfully', ['operator' => __('lang.deparment')]));
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
        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);

        if (empty($subdepartment)) {
            return $this->sendError('Departmento no encontrado');
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
        try {
            $subdepartment = $this->subdepartmentRepository->update($input, $id);
            $input['users'] = isset($input['users']) ? $input['users'] : [];
            $input['drivers'] = isset($input['drivers']) ? $input['drivers'] : [];
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($subdepartment, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $subdepartment->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($subdepartment->toArray(), __('lang.updated_successfully', ['operator' => __('lang.deparment')]));
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
        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);

        if (empty($subdepartment)) {
            return $this->sendError('Departmento no encontrado');
        }

        $subdepartment = $this->subdepartmentRepository->delete($id);

        return $this->sendResponse($subdepartment, __('lang.deleted_successfully', ['operator' => __('lang.deparment')]));
    }
}
