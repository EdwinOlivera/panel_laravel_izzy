<?php

namespace App\Http\Controllers;

use App\DataTables\SubdepartmentDataTable;
use App\Http\Requests\CreateSubdepartmentRequest;
use App\Http\Requests\UpdateSubdepartmentRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\MarketRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SubdepartmentRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class SubdepartmentController extends Controller
{
    /** @var  CategoryRepository */
    private $categoryRepository;

    /** @var  SubdepartmentRepository */
    private $subdepartmentRepository;

    /** @var  DepartmentRepository */
    private $departmentRepository;

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

    public function __construct(CategoryRepository $categoryRepo, DepartmentRepository $departmentRepo,
        SubdepartmentRepository $subdepartmentRepo,
        MarketRepository $marketRepo,
        ProductRepository $productRepo, CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo) {
        parent::__construct();
        $this->departmentRepository = $departmentRepo;
        $this->subdepartmentRepository = $subdepartmentRepo;

        $this->categoryRepository = $departmentRepo;
        $this->marketRepository = $marketRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->productRepository = $productRepo;
    }

    /**
     * Display a listing of the Deparment.
     *
     * @param SubdepartmentDataTable $subdepartmentDataTable
     * @return Response
     */
    public function index(SubdepartmentDataTable $subdepartmentDataTable)
    {
        return $subdepartmentDataTable->render('subdepartments.index');
    }

    /**
     * Show the form for creating a new Deparment.
     *
     * @return Response
     */
    public function create()
    {
        $departmentsSelected = [];

        $departments = $this->departmentRepository->pluck('name', 'id');
        $hasCustomField = in_array($this->subdepartmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('subdepartments.create')->with('departments', $departments)->with('departmentsSelected', $departmentsSelected)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created Deparment in storage.
     *
     * @param CreateSubdepartmentRequest $request
     *
     * @return Response
     */
    public function store(CreateSubdepartmentRequest $request)
    {

        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
        try {

            $subdepartment = $this->subdepartmentRepository->create($input);
            foreach ($input['departments'] as $idDepartment) {
                $marketsIds = DB::table('departments_market')->where('department_id', $idDepartment)->get(['market_id']);
                foreach ($marketsIds as $marketId) {
                    $checkValues = DB::table('subdepartments_departments')
                        ->where('subdepartment_id', $subdepartment->id)
                        ->where('department_id', $idDepartment)
                        ->where('market_id', '0')->get(['market_id']);
                    if (count($checkValues) > 0) {
                        DB::table('subdepartments_departments')
                            ->where('subdepartment_id', $subdepartment->id)
                            ->where('department_id', $idDepartment)
                            ->update([
                                'market_id' => $marketId->market_id,
                            ]);
                    } else {
                        DB::table('subdepartments_departments')->insert([
                            'subdepartment_id' => $subdepartment->id,
                            'department_id' => $idDepartment,
                            'market_id' => $marketId->market_id,
                        ]);
                    }

                }
            }
            $subdepartment->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($subdepartment, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.department')]));

        return redirect(route('subdepartments.index'));
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
        // Funcion sin usar
        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);

        if (empty($subdepartment)) {
            Flash::error('Departemento no encontrado');

            return redirect(route('subdepartments.index'));
        }

        return view('subdepartments.show')->with('department', $subdepartment);
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

        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);

        if (empty($subdepartment)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.department')]));

            return redirect(route('subdepartments.index'));
        }

        $departmentsSelected = $subdepartment->departments()->pluck('department_id')->toArray();
        $departments = $this->departmentRepository->pluck('name', 'id');

        $customFieldsValues = $subdepartment->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
        $hasCustomField = in_array($this->subdepartmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('subdepartments.edit')->with('subdepartment', $subdepartment)->with('departmentsSelected', $departmentsSelected)->with('departments', $departments)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Department in storage.
     *
     * @param  int              $id
     * @param UpdateSubdepartmentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSubdepartmentRequest $request)
    {
        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);

        if (empty($subdepartment)) {
            Flash::error('Subdepartemento no encontrado');
            return redirect(route('subdepartments.index'));
        }
        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
        try {
            $subdepartment = $this->subdepartmentRepository->update($input, $id);

            foreach ($input['departments'] as $idDepartment) {
                $marketsIds = DB::table('departments_market')->where('department_id', $idDepartment)->get(['market_id']);
                foreach ($marketsIds as $marketId) {
                    $checkValues = DB::table('subdepartments_departments')
                        ->where('subdepartment_id', $id)
                        ->where('department_id', $idDepartment)
                        ->where('market_id', $marketId->market_id)->get(['market_id']);
                    if (count($checkValues) == 0) {
                        $checkValuesZero = DB::table('subdepartments_departments')
                            ->where('subdepartment_id', $subdepartment->id)
                            ->where('department_id', $idDepartment)
                            ->where('market_id', '0')->get(['market_id']);

                        if (count($checkValuesZero) == 0) {
                            DB::table('subdepartments_departments')->insert([
                                'subdepartment_id' => $id,
                                'department_id' => $idDepartment,
                                'market_id' => $marketId->market_id,
                            ]);
                        } else {
                            DB::table('subdepartments_departments')
                                ->where('subdepartment_id', $subdepartment->id)
                                ->where('department_id', $idDepartment)
                                ->update([
                                    'market_id' => $marketId->market_id,
                                ]);
                        }
                    }

                }

            }
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
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.department')]));

        return redirect(route('subdepartments.index'));
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

        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);

        if (empty($subdepartment)) {
            Flash::error('Departemento no encontrado');

            return redirect(route('subdepartments.index'));
        }

        $this->subdepartmentRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.department')]));

        return redirect(route('subdepartments.index'));
    }

    /**
     * Remove Media of Deparment
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        // Funcion sin usar
        $input = $request->all();
        $subdepartment = $this->subdepartmentRepository->findWithoutFail($input['id']);
        try {
            if ($subdepartment->hasMedia($input['collection'])) {
                $subdepartment->getFirstMedia($input['collection'])->delete();
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

    /**
     * Show the form for creating a new Deparment.
     *
     * @return Response
     */
    public function createFromSupermarket($idDeparment)
    {
        $departmentsSelected = [$idDeparment];

        $departments = $this->departmentRepository->pluck('name', 'id');
        $hasCustomField = in_array($this->subdepartmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('subdepartments.createSupermarket')->with('departments', $departments)->with('departmentsSelected', $departmentsSelected)->with("customFields", isset($html) ? $html : false);
    }
    /**
     * Update the specified Department in storage.
     *
     * @param  int              $id
     * @param UpdateSubdepartmentRequest $request
     *
     * @return Response
     */
    public function updateFromSupermarket($id, UpdateSubdepartmentRequest $request)
    {
        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);
        $input = $request->all();
        $idMarket = $input['idMarket'];

        if (empty($subdepartment)) {
            Flash::error('Subdepartemento no encontrado');
            return redirect(route('supermarkets.editDepartmentsByMarket', $idMarket));
        }
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
        try {
            $subdepartment = $this->subdepartmentRepository->update($input, $id);
            $idsDepartments = $input['departments'];

            foreach ($idsDepartments as $idDepartment) {

                DB::table('subdepartments_departments')
                    ->where('subdepartment_id', $subdepartment->id)
                    ->where('department_id', $idDepartment)
                    ->update([
                        'market_id' => $idMarket,
                    ]);
            }

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
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.subdepartment')]));

        return redirect(route('supermarkets.editDepartmentsByMarket', $idMarket));
    }
    public function getSubdepartmentByDepartment(Request $request)
    {

        $idMarket = $request['idMarket'];
        $idD = $request['idD'];
        $subdepartments = [];
        $department = $this->departmentRepository->findWithoutFail($idD);
        $market = $this->marketRepository->findWithoutFail($idMarket);
        if (!empty($market) && !empty($department)) {
            $subdepartments = $department->subdepartments()->where('market_id', '=', $idMarket)->get();
            $idsSudepartment = [];
            foreach ($subdepartments as $subdepartment) {
                $idsSudepartment[] = $subdepartment->id;
            }
            // Se verifica el estado activo del departamento para este negocio
            $checkActiveSubdepartment = DB::table('subdepartments_departments')->where('department_id', $idD)->whereIn('subdepartment_id', $idsSudepartment)->pluck('active', 'subdepartment_id')->toArray();
            foreach ($subdepartments as $subdepartment) {
                $subdepartment->active = $checkActiveSubdepartment[$subdepartment->id];
            }
        }

        return $subdepartments;
    }
/**
 * Show the form for editing the specified Department.
 *
 * @param  int $id
 *
 * @return Response
 */
    public function editFromSupermarket($id)
    {

        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);

        if (empty($subdepartment)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.department')]));

            return redirect(route('subdepartments.index'));
        }

        $departmentsSelected = $subdepartment->departments()->pluck('department_id')->toArray();
        $departments = $this->departmentRepository->pluck('name', 'id');

        $customFieldsValues = $subdepartment->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
        $hasCustomField = in_array($this->subdepartmentRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('subdepartments.editSupermarket')->with('subdepartment', $subdepartment)->with('departmentsSelected', $departmentsSelected)->with('departments', $departments)->with("customFields", isset($html) ? $html : false);
    }
    public function sortSubdepartments(Request $request)
    {
        if ($request->has('ids')) {
            $arr = explode(',', $request->input('ids'));

            foreach ($arr as $sortOrder => $id) {
                DB::table('subdepartments')->where('id', '=', $id)->update([
                    "sort_id" => $sortOrder,
                ]);
            }

            return ['success' => true, 'message' => 'Updated'];
        }
    }
    public function setSubdeparmentsByDepartment(Request $request)
    {
        // Funcion sin usar
        $input = $request->all();

        $idMarket = $request['id'];
        $idD = $request['idDepartment'];

        $idSubdepartment = DB::table('subdepartments')->where('department_id', '=', $idD)->where('market_id', '=', $idMarket)->pluck('id');
        $idsSubDepartement = [];
        foreach ($idSubdepartment as $idC) {

            $idsSubDepartement[] = (int) $idC;
        }
        foreach ($input['categoriesProducts'] as $idPro) {

            $idsSubDepartement[] = (int) $idPro;
        }

        $input['categoriesProducts'] = $idsSubDepartement;
        $this->marketRepository->update($input, $idMarket);
    }
    public function createFromDepartment(CreateSubdepartmentRequest $request)
    {

        $input = $request->all();
        try {
            $subdepartment = $this->subdepartmentRepository->create($input);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

    }
    public function updateFromDepartment(UpdateSubdepartmentRequest $request)
    {

        $input = $request->all();

        try {

            $id = $input['idSd'];
            $idDeparment = $input['department_id'];

            $department = DB::table('subdepartments_departments')->where('subdepartment_id', '=', $id)
                ->where('department_id', '=', $idDeparment)->update([
                'active' => $input['active'],
            ]);
            $input['active'] = '1';
            $subdepartment = $this->subdepartmentRepository->update($input, $id);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function addSubdeparmentsFormDepartment(Request $request)
    {

        $idD = $request['idD'];
        $id = $request['idMarket'];
        $department = $this->departmentRepository->findWithoutFail($idD);
        if (!empty($department)) {
            $input = $request->all();
            $subdepartments = $department->subdepartments()->where('market_id', '=', $id)->get();

            $idsSubdepartment = $input['subdepartments'];
            foreach ($subdepartments as $subdepartment) {
                $idsSubdepartment[] = $subdepartment->id;
            }
            $input['subdepartments'] = $idsSubdepartment;
            $this->departmentRepository->update($input, $idD);
            DB::table('subdepartments_departments')->whereIn('subdepartment_id', $idsSubdepartment)
                ->where('department_id', '=', $idD)->update([
                'market_id' => $id,
            ]);
        }

    }

    public function addProductFormMarket(Request $request)
    {

        $input = $request->all();
        $id = $input['idSd'];

        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);
        if (!empty($subdepartment)) {
            $input = $request->all();
            $subdepartments = $subdepartment->products()->get();

            $idsProducts = $input['products'];
            foreach ($subdepartments as $product) {
                $idsProducts[] = $product->id;
            }
            $input['products'] = $idsProducts;
            $this->subdepartmentRepository->update($input, $id);
        }

    }
    public function searchSubdepartment(Request $request)
    {

        $subdepartment = DB::table('subdepartments')->where('name', 'LIKE', '%' . $request->input('subdepartment', '') . '%')->get(['id', 'name as text']);

        $departmentNew = [];
        $arrayNuevo = [];
        foreach ($subdepartment as $model) {

            $arrayNuevo[] = array(
                'id' => $model->id,
                'text' => $model->text,
            );

        }
        $departmentNew = $arrayNuevo;
        return ['results' => $subdepartment];
    }

    public function removeSubDeparment(Request $request)
    {
        $input = $request->all();
        $id = $request['idC'];
        $idMarket = $request['idMarket'];
        $subdepartment = $this->subdepartmentRepository->findWithoutFail($id);

        if (empty($subdepartment)) {
            Flash::error('Producto no encontrado.');
        }

    }
    /**
     * Store a newly created Department by Supermarkte in storage.
     *
     * @param CreateOptionRequest $request
     *
     * @return Response
     */
    public function storeFromDepartment(CreateSubdepartmentRequest $request)
    {
        $input = $request->all();
        $idsDepartments = $input['departments'];
        $idMarket = $input['idMarket'];
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->subdepartmentRepository->model());
        try {
            $subdepartment = $this->subdepartmentRepository->create($input);

            foreach ($idsDepartments as $idDepartment) {

                DB::table('subdepartments_departments')
                    ->where('subdepartment_id', $subdepartment->id)
                    ->where('department_id', $idDepartment)
                    ->update([
                        'market_id' => $idMarket,
                    ]);
            }
            $subdepartment->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($subdepartment, 'image');
            }

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.subdepartment')]));

        return redirect(route('supermarkets.editDepartmentsByMarket', $idMarket));
    }

    /**
     * Remove the specified Department from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function removeFromDepartment(Request $request)
    {
        $idSd = $request['idS'];
        $idD = $request['idD'];
        $idMarket = $request['market_id'];
        $department = $this->departmentRepository->findWithoutFail($idD);

        if (!empty($department)) {

            // $department->subdepartments()->where('market_id', '=', $idMarket)->detach($idSd);
            $idsSubdepartement = $department->subdepartments()->where('market_id', '=', $idMarket)->pluck('id')->toArray();
            DB::table('subdepartments_departments')->where('market_id', '=', $idMarket)->whereIn('subdepartment_id', $idsSubdepartement)->delete();

            return $idsSubdepartement;
        }
    }

}
