<?php

namespace App\Http\Controllers;

use App\DataTables\DepartmentDataTable;
use App\Http\Requests\CreateDepartmentRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\MarketRepository;
use App\Repositories\ProductRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\SubdepartmentRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class DepartmentController extends Controller
{
    /** @var  CategoryRepository */
    private $categoryRepository;

    /** @var  DepartmentRepository */
    private $departmentRepository;

    /** @var  SubdepartmentRepository */
    private $subdepartmentRepository;

    /** @var  ProductRepository */
    private $productRepository;

    /** @var  MarketRepository */
    private $marketRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(CategoryRepository $categoryRepo,
        DepartmentRepository $departmentRepo, MarketRepository $marketRepo,
        SubdepartmentRepository $subdepartmentRepo, ProductRepository $productRepo,
        CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo) {
        parent::__construct();
        $this->categoryRepository = $categoryRepo;
        $this->departmentRepository = $departmentRepo;
        $this->subdepartmentRepository = $subdepartmentRepo;
        $this->marketRepository = $marketRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->productRepository = $productRepo;
    }

    /**
     * Display a listing of the Deparment.
     *
     * @param DepartmentDataTable $departmentDataTable
     * @return Response
     */
    public function index(DepartmentDataTable $departmentDataTable)
    {
        return $departmentDataTable->render('departments.index');
    }

    /**
     * Show the form for creating a new Deparment.
     *
     * @return Response
     */
    public function create()
    {
        $marketsSelected = [];

        if (auth()->user()->hasRole('admin')) {
            $markets = $this->marketRepository->where('type_market_id', '=', '3')->pluck('name', 'id');
        } else {
            $markets = $this->marketRepository->myActiveSupermarket()->pluck('name', 'id');
        }

        $hasCustomField = in_array($this->departmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->departmentRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('departments.create')->with('marketsSelected', $marketsSelected)->with('markets', $markets)->with("customFields", isset($html) ? $html : false);

    }

    /**
     * Store a newly created Deparment in storage.
     *
     * @param CreateDepartmentRequest $request
     *
     * @return Response
     */
    public function store(CreateDepartmentRequest $request)
    {
        $input = $request->all();
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
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.department')]));

        return redirect(route('departments.index'));
    }

    /**
     * Display the specified Department.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            Flash::error('Departemento no encontrado');

            return redirect(route('departments.index'));
        }

        return view('departments.show')->with('department', $department);
    }

    /**
     * Show the form for editing the specified Department.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {

        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.department')]));

            return redirect(route('departments.index'));
        }
        if (auth()->user()->hasRole('admin')) {
            $markets = $this->marketRepository->where('type_market_id', '=', '3')->pluck('name', 'id');
        } else {
            $markets = $this->marketRepository->myActiveSupermarket()->pluck('name', 'id');
        }
        $marketsSelected = $department->markets()->pluck('market_id')->toArray();

        $customFieldsValues = $department->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->departmentRepository->model());
        $hasCustomField = in_array($this->departmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('departments.edit')->with('marketsSelected', $marketsSelected)->with('department', $department)->with('markets', $markets)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Department in storage.
     *
     * @param  int              $id
     * @param UpdateCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCategoryRequest $request)
    {
        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            Flash::error('Departemento no encontrado');
            return redirect(route('departments.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->departmentRepository->model());
        try {
            $department = $this->departmentRepository->update($input, $id);

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
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.department')]));

        return redirect(route('departments.index'));
    }

    /**
     * Remove the specified Department from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            Flash::error('Departemento no encontrado');

            return redirect(route('departments.index'));
        }

        $this->departmentRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.department')]));

        return redirect(route('departments.index'));
    }

    /**
     * Remove Media of Deparment
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $department = $this->departmentRepository->findWithoutFail($input['id']);
        try {
            if ($department->hasMedia($input['collection'])) {
                $department->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * get the categories by Product.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getDepartmentsByMarket(Request $request)
    {

        $id = $request['idMarket'];
        $market = $this->marketRepository->findWithoutFail($id);
        $departments = [];
        if (!empty($market)) {

            $departments = $market->departments()->get();
            $idsDepartment = [];
            foreach ($departments as $department) {
                $idsDepartment[] = $department->id;
            }
            // Se verifica el estado activo del departamento para este negocio
            $checkActiveDepartment = DB::table('departments_market')->where('market_id', $id)->whereIn('department_id', $idsDepartment)->pluck('active', 'department_id')->toArray();
            foreach ($departments as $department) {
                $department->active = $checkActiveDepartment[$department->id];
            }
        }
        return $departments;
    }

    public function sortDepartments(Request $request)
    {
        if ($request->has('ids')) {
            $arr = explode(',', $request->input('ids'));

            foreach ($arr as $sortOrder => $id) {
                DB::table('departments')->where('id', '=', $id)->update([
                    "sort_id" => $sortOrder,
                ]);
            }

            return ['success' => true, 'message' => 'Updated'];
        }
    }
    public function setDeparmentsBySupermarket(Request $request)
    {
        $input = $request->all();

        $idMarket = $request['id'];

        $idCategory = DB::table('categoriesproducts')->where('market_id', '=', $idMarket)->pluck('category_id');
        $idsCategories = [];
        foreach ($idCategory as $idC) {

            $idsCategories[] = (int) $idC;
        }
        foreach ($input['categoriesProducts'] as $idPro) {

            $idsCategories[] = (int) $idPro;
        }

        $input['categoriesProducts'] = $idsCategories;
        $this->marketRepository->update($input, $idMarket);
    }
    public function createFromSupermarket($idMarket)
    {

        $marketsSelected = [$idMarket];

        if (auth()->user()->hasRole('admin')) {
            $markets = $this->marketRepository->where('type_market_id', '=', '3')->pluck('name', 'id');
        } else {
            $markets = $this->marketRepository->myActiveSupermarket()->pluck('name', 'id');
        }
        $hasCustomField = in_array($this->departmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->departmentRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('departments.createSupermarket')->with('marketsSelected', $marketsSelected)->with('idMarket', $idMarket)->with('markets', $markets)->with("customFields", isset($html) ? $html : false);

    }
    public function updateFromSupermarket($id,CreateDepartmentRequest $request)
    {

        $input = $request->all();
        $idMarket = $input['idMarket'];
        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            Flash::error('Departemento no encontrado');
            return redirect(route('departments.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->departmentRepository->model());
        try {
            $department = $this->departmentRepository->update($input, $id);

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
            Flash::error($e->getMessage());
        }
        

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.department')]));
        return redirect(route('supermarkets.editDepartmentsByMarket', $idMarket));


    }

    public function editFromSupermarket($id)
    {

        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.department')]));

            return redirect(route('departments.index'));
        }
        if (auth()->user()->hasRole('admin')) {
            $markets = $this->marketRepository->where('type_market_id', '=', '3')->pluck('name', 'id');
        } else {
            $markets = $this->marketRepository->myActiveSupermarket()->pluck('name', 'id');
        }
        $idMarket = 'null'; 
        $marketsSelected = $department->markets()->pluck('market_id')->toArray();

        $customFieldsValues = $department->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->departmentRepository->model());
        $hasCustomField = in_array($this->departmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('departments.editSupermarket')->with('idMarket', $idMarket)->with('marketsSelected', $marketsSelected)->with('department', $department)->with('markets', $markets)->with("customFields", isset($html) ? $html : false);

    }

    public function addDepartmentsFormMarket(Request $request)
    {

        $id = $request['idMarket'];
        $market = $this->marketRepository->findWithoutFail($id);
        if (!empty($market)) {
            $input = $request->all();

            $departments = $market->departments()->get();
            $idsDepartment = $input['departments'];
            foreach ($departments as $department) {
                $idsDepartment[] = $department->id;
            }
            $input['departments'] = $idsDepartment;
            $this->marketRepository->update($input, $id);
        }

    }

    public function searchDepartment(Request $request)
    {

        $department = DB::table('departments')->where('name', 'LIKE', '%' . $request->input('department', '') . '%')->get(['id', 'name as text']);

        $departmentNew = [];
        $arrayNuevo = [];
        foreach ($department as $model) {

            $arrayNuevo[] = array(
                'id' => $model->id,
                'text' => $model->text,
            );

        }
        $departmentNew = $arrayNuevo;
        return ['results' => $department];
    }

    public function removeSubDeparment(Request $request)
    {
        $input = $request->all();
        $id = $request['idC'];
        $idMarket = $request['idMarket'];
        $department = $this->departmentRepository->findWithoutFail($id);

        if (empty($department)) {
            Flash::error('Producto no encontrado.');
        }
        DB::table('categoriesproducts')->where('category_id', '=', $id)->where('market_id', '=', $idMarket)->delete();

    }
    /**
     * Store a newly created Department by Supermarkte in storage.
     *
     * @param CreateOptionRequest $request
     *
     * @return Response
     */
    public function storeFromSupermarket(CreateDepartmentRequest $request)
    {

        $input = $request->all();
        $idMarket = $input['idMarket'];
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {

            $department = $this->departmentRepository->create($input);
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        return redirect(route('supermarkets.editDepartmentsByMarket', $idMarket));

        try {

        } catch (ValidatorException $e) {

        }
        return $input;
    }

    /**
     * Remove the specified Department from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function removeFromSupermarket(Request $request)
    {
        $id = $request['idD'];
        $idMarket = $request['market_id'];
        $market = $this->marketRepository->findWithoutFail($idMarket);

        if (!empty($market)) {
         $market->departments()->detach($id);
        }
    }
     /**
     * Remove the specified Product from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function changeVisibiliFromSupermarket(Request $request)
    {
        $input = $request->all();

        $department = $this->departmentRepository->findWithoutFail($input['idD']);
        $idD = $input['idD'];
        $idMarket = $input['market_id'];
        $active = $input['active'];
        if (!empty($department)) {

            DB::table('departments_market')->where('market_id', '=', $idMarket)->where('department_id', '=', $idD)->update([
                "active" => $active,
            ]);
            return ['success' => 'true', 'department_id' => $input['idD'], 'active' => $input['active']];
        }
    }
}
