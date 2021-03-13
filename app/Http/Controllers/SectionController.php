<?php

namespace App\Http\Controllers;

use App\DataTables\SectionDataTable;
use App\Http\Requests\CreateSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\MarketRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SectionRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class SectionController extends Controller
{
    /** @var  CategoryRepository */
    private $categoryRepository;

    /** @var  SectionRepository */
    private $sectionRepository;

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
        SectionRepository $sectionRepo,
        MarketRepository $marketRepo,
        ProductRepository $productRepo, CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo) {
        parent::__construct();
        $this->departmentRepository = $departmentRepo;
        $this->sectionRepository = $sectionRepo;

        $this->categoryRepository = $departmentRepo;
        $this->marketRepository = $marketRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->productRepository = $productRepo;
    }

    /**
     * Display a listing of the Deparment.
     *
     * @param SectionDataTable $sectionDataTable
     * @return Response
     */
    public function index(SectionDataTable $sectionDataTable)
    {
        return $sectionDataTable->render('sections.index');
    }

    /**
     * Show the form for creating a new Deparment.
     *
     * @return Response
     */
    public function create()
    {
        $marketsSelected = [];

        $markets = $this->marketRepository->where('type_market_id', '=', '2')->pluck('name', 'id');
        $hasCustomField = in_array($this->sectionRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('sections.create')->with('markets', $markets)->with('marketsSelected', $marketsSelected)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created Deparment in storage.
     *
     * @param CreateSectionRequest $request
     *
     * @return Response
     */
    public function store(CreateSectionRequest $request)
    {

        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
        try {

            $section = $this->sectionRepository->create($input);

            $section->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($section, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.section')]));

        return redirect(route('sections.index'));
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
        $section = $this->sectionRepository->findWithoutFail($id);

        if (empty($section)) {
            Flash::error('Sección no encontrado');
            return redirect(route('sections.index'));
        }

        return view('sections.show')->with('section', $section);
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

        $section = $this->sectionRepository->findWithoutFail($id);

        if (empty($section)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.section')]));

            return redirect(route('sections.index'));
        }

        $marketsSelected = $section->markets()->pluck('market_id')->toArray();
        $markets = $this->marketRepository->where('type_market_id', '=', '2')->pluck('name', 'id');

        $customFieldsValues = $section->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
        $hasCustomField = in_array($this->sectionRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('sections.edit')->with('subdepartment', $section)->with('marketsSelected', $marketsSelected)->with('markets', $markets)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Department in storage.
     *
     * @param  int              $id
     * @param UpdateSectionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSectionRequest $request)
    {
        $section = $this->sectionRepository->findWithoutFail($id);

        if (empty($section)) {
            Flash::error('Subdepartemento no encontrado');
            return redirect(route('sections.index'));
        }
        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
        try {
            $section = $this->sectionRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($section, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $section->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.section')]));

        return redirect(route('sections.index'));
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

        $section = $this->sectionRepository->findWithoutFail($id);

        if (empty($section)) {
            Flash::error('Sección no encontrado');

            return redirect(route('sections.index'));
        }

        $this->sectionRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.section')]));

        return redirect(route('sections.index'));
    }

    /**
     * Remove Media of Deparment
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        // Funcion sin usar
        $input = $request->all();
        $section = $this->sectionRepository->findWithoutFail($input['id']);
        try {
            if ($section->hasMedia($input['collection'])) {
                $section->getFirstMedia($input['collection'])->delete();
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
    public function createFromConvenienceStores($idMarket)
    {
        $marketsSelected = [$idMarket];

        $markets = $this->marketRepository->where('type_market_id', '=', '2')->pluck('name', 'id');
        $hasCustomField = in_array($this->sectionRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('sections.createSupermarket')->with('markets', $markets)->with('marketsSelected', $marketsSelected)->with("customFields", isset($html) ? $html : false);
    }
    /**
     * Update the specified Department in storage.
     *
     * @param  int              $id
     * @param UpdateSectionRequest $request
     *
     * @return Response
     */
    public function updateFromConvenienceStores($id, UpdateSectionRequest $request)
    {
        $section = $this->sectionRepository->findWithoutFail($id);
        $input = $request->all();
        $idMarket = $input['idMarket'];

        if (empty($section)) {
            Flash::error('Subdepartemento no encontrado');
            return redirect(route('supermarkets.editDepartmentsByMarket', $idMarket));
        }
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
        try {
            $section = $this->sectionRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($section, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $section->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.subdepartment')]));

        return redirect(route('supermarkets.editDepartmentsByMarket', $idMarket));
    }
    public function getSectionsByMarket(Request $request)
    {

        $idMarket = $request['idMarket'];

        $sections = [];
        $market = $this->marketRepository->findWithoutFail($idMarket);
        if (!empty($market)) {
            $sections = $market->sections()->orderBy('sort_id', 'asc')->get();
            $idsSections = [];
            foreach ($sections as $section) {
                $idsSections[] = $section->id;
            }
            // Se verifica el estado activo del departamento para este negocio
            $checkActiveSections = DB::table('sections_markets')->where('market_id', $idMarket)->whereIn('section_id', $idsSections)->pluck('active', 'section_id')->toArray();
            foreach ($sections as $section) {
                if ($section->active) {
                    $section->active = $checkActiveSections[$section->id];
                }

            }
        }

        return $sections;
    }
/**
 * Show the form for editing the specified Department.
 *
 * @param  int $id
 *
 * @return Response
 */
    public function editFromConvenienceStores($id)
    {

        $section = $this->sectionRepository->findWithoutFail($id);

        if (empty($section)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.section')]));

            return redirect(route('sections.index'));
        }

        $marketsSelected = $section->markets()->pluck('market_id')->toArray();
        $markets = $this->marketRepository->where('type_market_id', '=', '2')->pluck('name', 'id');

        $customFieldsValues = $section->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
        $hasCustomField = in_array($this->sectionRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('sections.editSupermarket')->with('section', $section)->with('marketsSelected', $marketsSelected)->with('markets', $markets)->with("customFields", isset($html) ? $html : false);
    }
    public function sortSection(Request $request)
    {
        if ($request->has('ids')) {
            $arr = explode(',', $request->input('ids'));

            foreach ($arr as $sortOrder => $id) {
                DB::table('sections')->where('id', '=', $id)->update([
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
    public function createDirectFromConvenienceStores(CreateSectionRequest $request)
    {

        $input = $request->all();
        try {
            $section = $this->sectionRepository->create($input);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

    }
    public function updateDirectFromConvenienceStores(UpdateSectionRequest $request)
    {

        $input = $request->all();

        try {

            $idSection = $input['idS'];
            $idMarket = $input['idMarket'];

            $department = DB::table('sections_markets')->where('section_id', '=', $idSection)
                ->where('market_id', '=', $idMarket)->update([
                'active' => $input['activeHalf'],
            ]);
            $section = $this->sectionRepository->findWithoutFail($idSection);
            if (!$section->active) {
                $input['active'] = $input['activeHalf'];
            }
            $section = $this->sectionRepository->update($input, $idSection);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function addSectionFromConvenienceStores(Request $request)
    {
        $idMarket = $request['idMarket'];
        $market = $this->marketRepository->findWithoutFail($idMarket);
        if (!empty($market)) {
            $input = $request->all();

            $idsSections = $market->sections()->get(['id'])->toArray();

            $idP = $request['idP'];
            $idMarket = $request['idMarket'];
            $idsSections = DB::table('sections_markets')->where('market_id', '=', $idMarket)->pluck('section_id')->toArray();

            foreach ($input['sections'] as $idSec) {
                
                $idsSections[] = (int) $idSec;
            }
            $input['sections'] = $idsSections;
            $this->marketRepository->update($input, $idMarket);
        }

    }

    public function addProductFromConvenienceStores(Request $request)
    {

        $input = $request->all();
        $id = $input['idSect'];
        $idMarket = $input['idMarket'];

        $section = $this->sectionRepository->findWithoutFail($id);
        if (!empty($section)) {
            $input = $request->all();
            $products = $section->products()->get();

            $idsProducts = $input['products'];
            foreach ($products as $product) {
                $idsProducts[] = $product->id;
            }
            $input['products'] = $idsProducts;
            $this->sectionRepository->update($input, $id);
            $checkActiveProduct = DB::table('section_product')
            ->whereIn('product_id', $idsProducts)
            ->where('section_id', $id)
            ->update([
                "market_id" => $idMarket,
            ]);

        }

    }
    public function searchSections(Request $request)
    {

        $section = DB::table('sections')->where('name', 'LIKE', '%' . $request->input('section', '') . '%')->get(['id', 'name as text']);

        $departmentNew = [];
        $arrayNuevo = [];
        foreach ($section as $model) {

            $arrayNuevo[] = array(
                'id' => $model->id,
                'text' => $model->text,
            );

        }
        $departmentNew = $arrayNuevo;
        return ['results' => $section];
    }

    /**
     * Store a newly created Department by Supermarkte in storage.
     *
     * @param CreateOptionRequest $request
     *
     * @return Response
     */
    public function storeFromConvenienceStores(CreateSectionRequest $request)
    {
        $input = $request->all();
        $idsMarkets = $input['markets'];
        $idMarket = $input['idMarket'];
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
        try {
            $section = $this->sectionRepository->create($input);

            $section->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($section, 'image');
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
    public function removeFromeConvenienceStores(Request $request)
    {
        $idSection = $request['idS'];
        $idMarket = $request['idMarket'];
        $section = $this->sectionRepository->findWithoutFail($idSection);

        if (!empty($section)) {

            $section->markets()->detach($idMarket);
        }
    }

}
