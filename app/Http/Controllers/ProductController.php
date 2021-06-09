<?php

/**
 * File name: ProductController.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers;

use App\Criteria\Markets\MarketsOfUserCriteria;
use App\Criteria\Products\ProductsOfUserCriteria;
use App\DataTables\ProductDataTable;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\MarketRepository;
use App\Repositories\OptionGroupRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SectionRepository;
use App\Repositories\SubdepartmentRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class ProductController extends Controller
{
    /** @var  ProductRepository */
    private $productRepository;

    /** @var  SectionRepository */
    private $sectionRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /** @var  SubdepartmentRepository */
    private $subdepartmentRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;
    /**
     * @var MarketRepository
     */
    private $marketRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /** @var  OptionGroupRepository */
    private $optionGroupRepository;

    public function __construct(
        ProductRepository $productRepo,
        CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo,
        MarketRepository $marketRepo,
        SectionRepository $sectionRepo,
        CategoryRepository $categoryRepo,
        SubdepartmentRepository $subdepartmentRepo,
        OptionGroupRepository $optionGroupRepo
    ) {
        parent::__construct();
        $this->sectionRepository = $sectionRepo;
        $this->productRepository = $productRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->subdepartmentRepository = $subdepartmentRepo;
        $this->uploadRepository = $uploadRepo;
        $this->marketRepository = $marketRepo;
        $this->categoryRepository = $categoryRepo;
        $this->optionGroupRepository = $optionGroupRepo;
    }

    /**
     * Display a listing of the Product.
     *
     * @param ProductDataTable $productDataTable
     * @return Response
     */
    public function index(ProductDataTable $productDataTable)
    {
        return $productDataTable->render('products.index');
    }

    /**
     * Show the form for creating a new Product.
     *
     * @return Response
     */
    public function create()
    {

        $category = $this->categoryRepository->pluck('name', 'id');
        if (auth()->user()->hasRole('admin')) {
            $market = $this->marketRepository->pluck('name', 'id');
        } else {
            $market = $this->marketRepository->myActiveMarkets()->pluck('name', 'id');
        }
        $categoriesSelected = [];

        $isNormal = true;
        $subparments = [];

        $hasCustomField = in_array($this->productRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
            $html = generateCustomField($customFields);
        }
        $disponible = 1;
        return view('products.create')->with("customFields", isset($html) ? $html : false)->with("disponible", $disponible)->with("market", $market)->with("category", $category)->with("categoriesSelected", $categoriesSelected)->with('isNormal', $isNormal)->with('subparments', $subparments);
    }

    /**
     * Store a newly created Product in storage.
     *
     * @param CreateProductRequest $request
     *
     * @return Response
     */
    public function store(CreateProductRequest $request)
    {
        $input = $request->all();
        $discountPrice = $input['discount_price'];
        if ($discountPrice > 0) {
            $input['promotion'] = '1';
        }

        $input['featured'] = '1';
        if (isset($input['categories']) && ($input['categories'])) {
            $input['category_id'] = $input['categories'][0];
        }
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {
            $product = $this->productRepository->create($input);
            $product->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.product')]));

        return redirect(route('products.index'));
    }

    /**
     * Display the specified Product.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function show($id)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');

            return redirect(route('products.index'));
        }

        return view('products.show')->with('product', $product);
    }

    /**
     * Show the form for editing the specified Product.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function edit($id)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.product')]));
            return redirect(route('products.index'));
        }

        $categoriesSelected = $product->categories()->pluck('categories.id')->toArray();

        $category = $this->categoryRepository->pluck('name', 'id');
        if (auth()->user()->hasRole('admin')) {
            $market = $this->marketRepository->pluck('name', 'id');
        } else {
            $market = $this->marketRepository->myMarkets()->pluck('name', 'id');
        }
        $customFieldsValues = $product->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        $hasCustomField = in_array($this->productRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
        $isNormal = true;
        $subparments = [];
        $marketTemp = $this->marketRepository->where('id', '=', $product->market_id)->get()->toArray();

        if ($marketTemp[0]['type_market_id'] == '3') {
            $isNormal = false;
            $subparments = $this->subdepartmentRepository->where('market_id', $marketTemp[0]['id'])->pluck('name', 'id');
        }
        return view('products.edit')->with('product', $product)->with("customFields", isset($html) ? $html : false)->with("market", $market)->with("categoriesSelected", $categoriesSelected)->with("category", $category)->with('isNormal', $isNormal)->with('subparments', $subparments);
    }

    /**
     * Update the specified Product in storage.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateProductRequest $request)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
            return redirect(route('products.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {
            $product = $this->productRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $product->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.product')]));

        return redirect(route('products.index'));
    }

    /**
     * Remove the specified Product from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        if (!env('APP_DEMO', false)) {
            $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
            $product = $this->productRepository->findWithoutFail($id);

            if (empty($product)) {
                Flash::error('Producto no encontrado.');

                return redirect(route('products.index'));
            }

            $this->productRepository->delete($id);

            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.product')]));
        } else {
            Flash::warning('Esta app solo es una demo, no puede modifcar esta sección ');
        }
        return redirect(route('products.index'));
    }

    /**
     * Remove the specified Product from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroyAlt(Request $request)
    {
        $input = $request->all();

        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($input['id']);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
        }

        $this->productRepository->delete($input['id']);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.product')]));
    }
    /**
     * Remove the specified Product from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function removeProductsFromCategory(Request $request)
    {
        $input = $request->all();
        $product = $this->productRepository->findWithoutFail($input['id']);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
        }
        DB::table('product_categories')->where('product_id', '=', $input['id'])->where('category_id', '=', $input['idC'])->delete();
        // DB::table('products')->where('id', '=', $input['id'])->update([
        //     "category_id" => '0',
        // ]);
        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.product')]));
    }

    /**
     * Remove the specified Product from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function removeProductsFromSubdepartment(Request $request)
    {
        $input = $request->all();
        $product = $this->productRepository->findWithoutFail($input['id']);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
        }
        DB::table('subdepartments_products')->where('product_id', '=', $input['id'])->where('subdepartment_id', '=', $input['idS'])->delete();
        // DB::table('products')->where('id', '=', $input['id'])->update([
        //     "category_id" => '0',
        // ]);
        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.product')]));
    }

    /**
     * Remove the specified Product from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function removeProductsFromSection(Request $request)
    {
        $input = $request->all();
        $product = $this->productRepository->findWithoutFail($input['id']);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
        }
        DB::table('section_product')->where('product_id', '=', $input['id'])->where('section_id', '=', $input['idSection'])->where('market_id', '=', $input['idMarket'])->delete();
        // DB::table('products')->where('id', '=', $input['id'])->update([
        //     "category_id" => '0',
        // ]);
        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.product')]));
    }
    /**
     * Remove the specified Product from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function cambiarDisponibilidad(Request $request)
    {
        $input = $request->all();

        $product = $this->productRepository->findWithoutFail($input['id']);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
        }
        DB::table('product_categories')->where('category_id', '=', $input['idC'])->where('product_id', '=', $input['id'])->update([
            "active" => $input['featured'],
        ]);

        return ['success' => 'true', 'productID' => $input['id'], 'featured' => $input['featured']];
    }

    /**
     * Remove the specified Product from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function changeVisibiliFromSubdepartment(Request $request)
    {
        $input = $request->all();

        $product = $this->productRepository->findWithoutFail($input['idP']);
        $idP = $input['idP'];
        $idSd = $input['idSd'];
        $active = $input['featured'];
        if (!empty($product)) {

            DB::table('subdepartments_products')->where('subdepartment_id', '=', $idSd)->where('product_id', '=', $idP)->update([
                "active" => $active,
            ]);
            return ['success' => 'true', 'productID' => $input['idP'], 'featured' => $input['featured']];
        }
    }

    /**
     * Remove Media of Product
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $product = $this->productRepository->findWithoutFail($input['id']);
        try {
            if ($product->hasMedia($input['collection'])) {
                $product->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Update the specified Product in storage usening modal.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateModal(Request $request)
    {
        $id = $request['id'];
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            Flash::error('Error al editar.');
        } else {
            $input = $request->all();
            try {
                $product = $this->productRepository->update($input, $id);
            } catch (ValidatorException $e) {
                Flash::error($e->getMessage());
            }
        }
    }

    /**
     * get the categories and group of options.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getCategoriesAndOptionGroup(Request $request)
    {
        $id = $request['id'];
        $product = $this->productRepository->findWithoutFail($id);
        // Conseguimos todas las categorias asociadas al producto
        $categoriesSelected = $product->categories()->pluck('categories.id')->toArray();
        $categoriesArray = [];
        foreach ($categoriesSelected as $categoryID) {
            $categoriesArray[] = $this->categoryRepository->findWithoutFail($categoryID);
        }

        $optionGroupProduct = $this->optionGroupRepository->where('id_producto', $id);
    }

    /**
     * get the producto by Category.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getProductByCategory(Request $request)
    {
        $id = $request['id'];
        $idMarket = $request['idMarket'];

        $idProducts = DB::table('product_categories')->where('category_id', '=', $id)->get('product_id as id');

        $idProdutsMarket = DB::table('products')->where('market_id', '=', $idMarket)->get('id')->toArray();
        $idProductsMarketsFilter = [];
        if (count($idProducts) == 0) {
            $idProdutsMarket = DB::table('products')->where('category_id', '=', $id)->where('market_id', '=', $idMarket)->get('id')->toArray();
            $idProductsMarketsFilter = $idProdutsMarket;
        } else {
            if (count($idProducts) > 0) {
                foreach ($idProducts as $idProduct => $product_id) {

                    if (in_array($product_id, $idProdutsMarket)) {
                        $idProductsMarketsFilter[] = $product_id;
                    }
                }
            }
        }
        $ProductsFound = [];
        $idFilter = [];

        foreach ($idProductsMarketsFilter as $idP) {
            $idFilter[] = $idP->id;
        }
        $productIDRaw = DB::table('product_categories')->where('category_id', $id)->pluck('active', 'product_id');
        $ProductsFound = DB::table('products')->whereIn('id', $idFilter)->limit(100)->orderBy('sort_id', 'asc')->get();
        $products = [];
        foreach ($ProductsFound as $product) {
            if (isset($productIDRaw[$product->id])) {
                $product->activeGlobal = $product->featured;
                $product->featured = $productIDRaw[$product->id];
                $products[] = $product;
            }
        }

        return $products;
    }

    public function searchProductsFromMarket(Request $request)
    {
        $products = DB::table('products')->where('market_id', '=', $request->input('idMarket', ''))->where('name', 'LIKE', '%' . $request->input('palabra', '') . '%')->get(['id', 'name as text', 'market_id']);
        return ['results' => $products];
    }
    /**
     * get the producto by Category.
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getProductBySubdepartment(Request $request)
    {
        $id = $request['id'];
        $subparments = $this->subdepartmentRepository->findWithoutFail($id);
        if (!empty($subparments)) {
            $products = $subparments->products()->get(['id']);
            $idsProducts = [];
            foreach ($products as $product) {
                $idsProducts[] = $product->id;
            }
            $products = DB::table('products')->whereIn('id', $idsProducts)->orderBy('sort_id', 'asc')->get();
            $checkActiveProduct = DB::table('subdepartments_products')->whereIn('product_id', $idsProducts)->where('subdepartment_id', $id)->pluck('active', 'product_id')->toArray();
            foreach ($products as $product) {
                $product->featured = $checkActiveProduct[$product->id];
            }
        }
        return $products;
    }

    /**
     * get the producto by Category.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getProductBySection(Request $request)
    {
        $idSection = $request['id'];
        $idMarket = $request['idMarket'];
        $section = $this->sectionRepository->findWithoutFail($idSection);
        if (!empty($section)) {
            $products = $section->products()->get();

            $idsProducts = [];
            foreach ($products as $product) {
                $idsProducts[] = $product->id;
            }
            $products = DB::table('products')->where('market_id', '=', $idMarket)->whereIn('id', $idsProducts)->orderBy('sort_id', 'asc')->get();
            $checkActiveProduct = DB::table('section_product')->whereIn('product_id', $idsProducts)->where('section_id', $idSection)->where('market_id', $idMarket)->pluck('active', 'product_id')->toArray();
            return $products;
            foreach ($products as $product) {
                $product->featured = $checkActiveProduct[$product->id];
            }
        }
        return $products;
    }

    /**
     * Show the form for creating a new Product.
     *
     * @return Response
     */
    public function createFromMarket($idMarket)
    {

        $category = $this->categoryRepository->pluck('name', 'id');

        $categoriesSelected = [];

        if (auth()->user()->hasRole('admin')) {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
        } else {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);

            $market = array($marketTemp->id => $marketTemp->name);
            // $market = $this->marketRepository->myActiveMarkets()->pluck('name', 'id');
        }

        $isNormal = true;
        $subparments = [];
        $subparmentsSelected = [];
        if ($marketTemp->type_market_id == '3') {
            $isNormal = false;
            $idsSubDepartment = DB::table('subdepartments_departments')->where('market_id', $marketTemp->id)->pluck('subdepartment_id');
            $subparments = DB::table('subdepartments')->pluck('name', 'id');
        }

        $hasCustomField = in_array($this->productRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
            $html = generateCustomField($customFields);
        }
        $disponible = 1;
        return view('products.createMarket')->with("customFields", isset($html) ? $html : false)->with('disponible', $disponible)->with('idMarket', $idMarket)->with("market", $market)->with("category", $category)->with("categoriesSelected", $categoriesSelected)->with('isNormal', $isNormal)->with('subparments', $subparments);
    }

    /**
     * Store a newly created Product in storage.
     *
     * @param CreateProductRequest $request
     *
     * @return Response
     */

    public function storeFromMarket(CreateProductRequest $request)
    {
        $input = $request->all();
        $input['featured'] = '1';

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {
            if (isset($input['categories']) && ($input['categories'])) {
                $input['category_id'] = $input['categories'][0];
            }
            $product = $this->productRepository->create($input);

            $product->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $market = $this->marketRepository->findWithoutFail($product->market_id);

        if (empty($market)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.market')]));
            return redirect(route('markets.index'));
        }

        return redirect(route('markets.editMarketComplete', $market->id));
    }

    /**
     * Show the form for editing the specified Product.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function editFromMarket($id)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.product')]));
            return redirect(route('products.index'));
        }
        $idMarket = $product->market_id;
        $categoriesSelected = $product->categories()->pluck('categories.id')->toArray();

        $category = $this->categoryRepository->pluck('name', 'id');
        if (auth()->user()->hasRole('admin')) {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
        } else {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
            // $market = $this->marketRepository->myActiveMarkets()->pluck('name', 'id');
        }
        $isNormal = true;
        $subparments = [];
        $subparmentsSelected = [];
        if ($marketTemp->type_market_id == '3') {
            $isNormal = false;
            $idsSubDepartment = DB::table('subdepartments_departments')->where('market_id', $marketTemp->id)->pluck('subdepartment_id');
            $subparments = DB::table('subdepartments')->pluck('name', 'id');
        }
        $customFieldsValues = $product->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        $hasCustomField = in_array($this->productRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('products.editMarket')->with('product', $product)->with('idMarket', $marketTemp->id)->with("customFields", isset($html) ? $html : false)->with("market", $market)->with("categoriesSelected", $categoriesSelected)->with("category", $category)->with('isNormal', $isNormal)->with('subparments', $subparments);
    }

    /**
     * Update the specified Product in storage.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateFromMarket($id, UpdateProductRequest $request)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
            return redirect(route('products.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {
            $product = $this->productRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $product->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.product')]));

        return redirect(route('markets.editMarketComplete', $product->market_id));
    }

    /**
     * Show the form for creating a new Product.
     *
     * @return Response
     */
    public function createFromSupermarket($idMarket)
    {

        $category = $this->categoryRepository->pluck('name', 'id');

        $categoriesSelected = [];

        if (auth()->user()->hasRole('admin')) {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
        } else {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
        }

        $isNormal = true;
        $subparments = [];
        $subparmentsSelected = [];
        if ($marketTemp->type_market_id == '3') {
            $isNormal = false;
            $idsSubDepartment = DB::table('subdepartments_departments')->where('market_id', $marketTemp->id)->pluck('subdepartment_id');
            $subparments = DB::table('subdepartments')->pluck('name', 'id');
        }

        $hasCustomField = in_array($this->productRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
            $html = generateCustomField($customFields);
        }
        $disponible = 1;
        return view('products.createSupermarket')->with("customFields", isset($html) ? $html : false)->with('disponible', $disponible)->with('idMarket', $idMarket)->with("market", $market)->with("category", $category)->with("categoriesSelected", $categoriesSelected)->with('isNormal', $isNormal)->with('subparmentsSelected', $subparmentsSelected)->with('subparments', $subparments);
    }

    /**
     * Show the form for creating a new Product.
     *
     * @return Response
     */
    public function createFromConvenienceStore($idMarket)
    {

        $category = $this->categoryRepository->pluck('name', 'id');

        $categoriesSelected = [];

        if (auth()->user()->hasRole('admin')) {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
        } else {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
        }

        $isNormal = true;
        $sections = [];
        $sectionsSelected = [];
        if ($marketTemp->type_market_id == '2') {
            $isNormal = false;
            $sections = $marketTemp->sections()->orderBy('sort_id', 'asc')->pluck('name', 'id');
        }

        $hasCustomField = in_array($this->productRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
            $html = generateCustomField($customFields);
        }
        $disponible = 1;
        return view('products.createConvenienceStores')->with("customFields", isset($html) ? $html : false)->with('disponible', $disponible)->with('idMarket', $idMarket)->with("market", $market)->with("category", $category)->with("categoriesSelected", $categoriesSelected)->with('isNormal', $isNormal)->with('sectionsSelected', $sectionsSelected)->with('sections', $sections);
    }
    /**
     * Store a newly created Product in storage.
     *
     * @param CreateProductRequest $request
     *
     * @return Response
     */

    public function storeFromConvenienceStore(CreateProductRequest $request)
    {
        $input = $request->all();
        $input['featured'] = '1';

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {
            if (isset($input['categories']) && ($input['categories'])) {
                $input['category_id'] = $input['categories'][0];
            }

            $product = $this->productRepository->create($input);
            $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
            $market = $this->marketRepository->findWithoutFail($product->market_id);
            if (isset($input['sections'])) {
                DB::table('section_product')->where('product_id', '=', $product->id)->where('market_id', '=', '0')->whereIn('section_id', $input['sections'])->update([
                    "market_id" => $market->id,
                ]);
            }

            $product->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }



        if (empty($market)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.market')]));
            return redirect(route('markets.index'));
        }

        return redirect(route('convenience_stores.editSectionsByConvenienceStore', $market->id));
    }
    /**
     * Store a newly created Product in storage.
     *
     * @param CreateProductRequest $request
     *
     * @return Response
     */

    public function storeFromSupermarket(CreateProductRequest $request)
    {
        $input = $request->all();
        $input['featured'] = '1';

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {
            if (isset($input['categories']) && ($input['categories'])) {
                $input['category_id'] = $input['categories'][0];
            }
            $product = $this->productRepository->create($input);

            $product->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $market = $this->marketRepository->findWithoutFail($product->market_id);

        if (empty($market)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.market')]));
            return redirect(route('markets.index'));
        }

        return redirect(route('supermarkets.editDepartmentsByMarket', $market->id));
    }

    /**
     * Show the form for editing the specified Product.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function editFromSupemarket($id)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.product')]));
            return redirect(route('products.index'));
        }
        $idMarket = $product->market_id;
        $categoriesSelected = $product->categories()->pluck('categories.id')->toArray();

        $category = $this->categoryRepository->pluck('name', 'id');
        if (auth()->user()->hasRole('admin')) {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
        } else {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
            // $market = $this->marketRepository->myActiveMarkets()->pluck('name', 'id');
        }
        $isNormal = true;
        $subparments = [];
        $subparmentsSelected = $product->subdepartments()->pluck('subdepartments.id')->toArray();
        if ($marketTemp->type_market_id == '3') {
            $isNormal = false;
            $idsSubDepartment = DB::table('subdepartments_departments')->where('market_id', $marketTemp->id)->pluck('subdepartment_id');
            $subparments = DB::table('subdepartments')->pluck('name', 'id');
        }
        $customFieldsValues = $product->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        $hasCustomField = in_array($this->productRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('products.editSupermarket')->with('product', $product)->with('idMarket', $marketTemp->id)->with("customFields", isset($html) ? $html : false)->with("market", $market)->with("categoriesSelected", $categoriesSelected)->with("category", $category)->with('isNormal', $isNormal)->with('subparmentsSelected', $subparmentsSelected)->with('subparments', $subparments);
    }

    /**
     * Show the form for editing the specified Product.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function editFromConvenienceStore($id)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.product')]));
            return redirect(route('products.index'));
        }
        $idMarket = $product->market_id;
        $categoriesSelected = $product->categories()->pluck('categories.id')->toArray();

        $category = $this->categoryRepository->pluck('name', 'id');
        if (auth()->user()->hasRole('admin')) {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
        } else {
            $marketTemp = $this->marketRepository->findWithoutFail($idMarket);
            $market = array($marketTemp->id => $marketTemp->name);
            // $market = $this->marketRepository->myActiveMarkets()->pluck('name', 'id');
        }
        $sectionsSelected = $product->sections()->pluck('sections.id')->toArray();
        $isNormal = true;
        $subparments = [];
        if ($marketTemp->type_market_id == '2') {
            $isNormal = false;
            $sections = $marketTemp->sections()->orderBy('sort_id', 'asc')->pluck('name', 'id');
        }
        $customFieldsValues = $product->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        $hasCustomField = in_array($this->productRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('products.editConvenienceStores')->with('product', $product)->with('idMarket', $marketTemp->id)->with("customFields", isset($html) ? $html : false)->with("market", $market)->with("categoriesSelected", $categoriesSelected)->with("category", $category)->with('isNormal', $isNormal)->with('sectionsSelected', $sectionsSelected)->with('sections', $sections);
    }


    /**
     * Update the specified Product in storage.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateFromSupermarket($id, UpdateProductRequest $request)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
            return redirect(route('products.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {
            $product = $this->productRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $product->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.product')]));

        return redirect(route('supermarkets.editDepartmentsByMarket', $product->market_id));
    }


    /**
     * Update the specified Product in storage.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateFromConvenienceStore($id, UpdateProductRequest $request)
    {
        $this->productRepository->pushCriteria(new ProductsOfUserCriteria(auth()->id()));
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            Flash::error('Producto no encontrado.');
            return redirect(route('products.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->productRepository->model());
        try {
            $product = $this->productRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($product, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $product->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.product')]));

        return redirect(route('convenience_stores.editSectionsByConvenienceStore', $product->market_id));
    }
}
