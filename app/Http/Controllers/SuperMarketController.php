<?php
/**
 * File name: SupermarketController.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers;

use App\Criteria\Markets\MarketsOfUserCriteria;
use App\Criteria\Users\DriversCriteria;
use App\Criteria\Users\ManagersClientsCriteria;
use App\Criteria\Users\ManagersCriteria;
use App\DataTables\SupermarketDataTable;
use App\Events\MarketChangedEvent;
use App\Http\Requests\CreateMarketRequest;
use App\Http\Requests\UpdateMarketRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\FieldRepository;
use App\Repositories\MarketRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TypeMarketRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class SupermarketController extends Controller
{
    /** @var  MarketRepository */
    private $marketRepository;

    /** @var  ProductRepository */
    private $productRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var FieldRepository
     */
    private $fieldRepository;

    /**
     * @var TypeMarketRepository
     */
    private $TypeMarketRepository;

    public function __construct(MarketRepository $marketRepo, CategoryRepository $categoryRepo, ProductRepository $productRepo,
        CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo,
        UserRepository $userRepo,
        FieldRepository $fieldRepository, TypeMarketRepository $typeMarketRepo) {
        parent::__construct();
        $this->marketRepository = $marketRepo;
        $this->productRepository = $productRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->userRepository = $userRepo;
        $this->categoryRepository = $categoryRepo;
        $this->fieldRepository = $fieldRepository;
        $this->typeMarketRepository = $typeMarketRepo;
    }

    /**
     * Display a listing of the Market.
     *
     * @param SupermarketDataTable $supermarketDataTable
     * @return Response
     */
    public function index(SupermarketDataTable $supermarketDataTable)
    {
        return $supermarketDataTable->render('supermarkets.index');
    }

    /**
     * Show the form for creating a new Market.
     *
     * @return Response
     */
    public function create()
    {

        $user = $this->userRepository->getByCriteria(new ManagersCriteria())->pluck('name', 'id');
        $drivers = $this->userRepository->getByCriteria(new DriversCriteria())->pluck('name', 'id');
        $field = $this->fieldRepository->pluck('name', 'id');
        $typeMarket = $this->typeMarketRepository->where('id','=','3')->pluck('name', 'id');
        $usersSelected = [];
        $driversSelected = [];
        $fieldsSelected = [];
        $hasCustomField = in_array($this->marketRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->marketRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('supermarkets.create')->with("customFields", isset($html) ? $html : false)->with("typeMarket", $typeMarket)->with("user", $user)->with("drivers", $drivers)->with("usersSelected", $usersSelected)->with("driversSelected", $driversSelected)->with('field', $field)->with('fieldsSelected', $fieldsSelected);
    }

    /**
     * Store a newly created Market in storage.
     *
     * @param CreateMarketRequest $request
     *
     * @return Response
     */
    public function store(CreateMarketRequest $request)
    {
        $input = $request->all();
        if (auth()->user()->hasRole(['manager', 'client'])) {
            $input['users'] = [auth()->id()];
        }
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->marketRepository->model());
        try {
            $market = $this->marketRepository->create($input);
            $market->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($market, 'image');
            }
            event(new MarketChangedEvent($market, $market));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.market')]));

        return redirect(route('supermarkets.index'));
    }

    /**
     * Display the specified Market.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function show($id)
    {
        $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $market = $this->marketRepository->findWithoutFail($id);

        if (empty($market)) {
            Flash::error('Market not found');

            return redirect(route('supermarkets.index'));
        }

        return view('supermarkets.show')->with('market', $market);
    }

    /**
     * Show the form for editing the specified Market.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function edit($id)
    {
        $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $market = $this->marketRepository->findWithoutFail($id);

        if (empty($market)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.market')]));
            return redirect(route('supermarkets.index'));
        }
        // if ($market['active'] == 0) {
        //     $user = $this->userRepository->getByCriteria(new ManagersClientsCriteria())->pluck('name', 'id');
        // } else {
        // }
        $user = $this->userRepository->getByCriteria(new ManagersCriteria())->pluck('name', 'id');

        $drivers = $this->userRepository->getByCriteria(new DriversCriteria())->pluck('name', 'id');
        $field = $this->fieldRepository->pluck('name', 'id');

        $usersSelected = $market->users()->pluck('users.id')->toArray();
        $driversSelected = $market->drivers()->pluck('users.id')->toArray();
        $fieldsSelected = $market->fields()->pluck('fields.id')->toArray();

        $customFieldsValues = $market->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->marketRepository->model());
        $hasCustomField = in_array($this->marketRepository->model(), setting('custom_field_models', []));
        $products = $this->productRepository->where('market_id', $market->id)->orderBy('sort_id', 'asc')->get(['id', 'name', 'sort_id', 'price', 'featured', 'category_id']);
        $categoryProductsID = $market->categoriesProducts()->pluck('category_id')->toArray();

        $categoriesProducts = [];
        $arrayDeIDCategorias = $categoryProductsID;

        foreach ($products as $product) {

            $categoriesSelected = $product->categories()->pluck('categories.id')->toArray();
            if (!in_array($product->category_id, $arrayDeIDCategorias)) {
                $arrayDeIDCategorias[] = $product->category_id;
            }
            foreach ($categoriesSelected as $category) {
                if (!in_array($category, $arrayDeIDCategorias)) {
                    $arrayDeIDCategorias[] = $category;
                }
            }

        }
        $categoriesProducts = $this->categoryRepository->whereIn('id', $arrayDeIDCategorias)->orderBy('sort_id', 'asc')->get();
        $categoriesSelected = $this->categoryRepository->whereIn('id', $arrayDeIDCategorias)->pluck('id')->toArray();

        $arrayIdCategorias = array('categoriesProducts' => $categoriesSelected);
        $this->marketRepository->update($arrayIdCategorias, $market->id);
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }


        $typeMarket = $this->typeMarketRepository->where('id','=',$market->type_market_id)->pluck('name', 'id');

        return view('supermarkets.edit')->with('market', $market)->with("typeMarket", $typeMarket)->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("drivers", $drivers)->with("usersSelected", $usersSelected)->with("driversSelected", $driversSelected)->with('field', $field)->with('fieldsSelected', $fieldsSelected);
    }

    /**
     * Show the form for editing the specified Market with products.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function editMarketComplete($id)
    {
        $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $market = $this->marketRepository->findWithoutFail($id);

        if (empty($market)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.market')]));
            return redirect(route('supermarkets.index'));
        }
        // if ($market['active'] == 0) {
        //     $user = $this->userRepository->getByCriteria(new ManagersClientsCriteria())->pluck('name', 'id');
        // } else {
        // }
        $user = $this->userRepository->getByCriteria(new ManagersCriteria())->pluck('name', 'id');

        $categoryProductsIDRaw = DB::table('categoriesproducts')->where('market_id', $market->id)->pluck('active', 'category_id');
        $categoryProductsID = $market->categoriesProducts()->pluck('category_id')->toArray();

        $categoriesProducts = [];
        

        $categoriesProducts = $this->categoryRepository->whereIn('id', $categoryProductsID)->orderBy('sort_id', 'asc')->get();

        $categories = [];
        foreach ($categoriesProducts as $category) {
            if (isset($categoryProductsIDRaw[$category->id])) {
                $category->active = $categoryProductsIDRaw[$category->id];
                $categories[] = $category;
            }
        }
        $categoriesProducts = $categories;


        return view('supermarkets.edit_market_complete')->with('market', $market)->with('categoriesProducts', $categoriesProducts);
    }

    /**
     * Update the specified Market in storage.
     *
     * @param int $id
     * @param UpdateMarketRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateMarketRequest $request)
    {
        $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $oldMarket = $this->marketRepository->findWithoutFail($id);

        if (empty($oldMarket)) {
            Flash::error('Market not found');
            return redirect(route('supermarkets.index'));
        }
        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->marketRepository->model());
        try {

            $market = $this->marketRepository->update($input, $id);
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($market, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $market->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
            event(new MarketChangedEvent($market, $oldMarket));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.market')]));

        return redirect(route('supermarkets.index'));
    }

    /**
     * Remove the specified Market from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function destroy($id)
    {
        if (!env('APP_DEMO', false)) {
            $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
            $market = $this->marketRepository->findWithoutFail($id);

            if (empty($market)) {
                Flash::error('Market not found');

                return redirect(route('supermarkets.index'));
            }

            $this->marketRepository->delete($id);

            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.market')]));
        } else {
            Flash::warning('Esta app solo es una demo, no puede modifcar esta sección ');
        }
        return redirect(route('supermarkets.index'));
    }

    /**
     * Remove Media of Market
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $market = $this->marketRepository->findWithoutFail($input['id']);
        try {
            if ($market->hasMedia($input['collection'])) {
                $market->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function updateOrderProductList(Request $request)
    {
        if ($request->has('ids')) {
            $arr = explode(',', $request->input('ids'));

            foreach ($arr as $sortOrder => $id) {

                DB::table('products')->where('id', '=', $id)->update([
                    "sort_id" => $sortOrder,
                ]);

            }
            return ['success' => true, 'message' => 'Updated'];
        }
    }
    /**
     * Show the form for editing the specified Market with products.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function editDepartmentsByMarket($id)
    {
        $this->marketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $market = $this->marketRepository->findWithoutFail($id);

        if (empty($market)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.market')]));
            return redirect(route('supermarkets.index'));
        }
        // if ($market['active'] == 0) {
        //     $user = $this->userRepository->getByCriteria(new ManagersClientsCriteria())->pluck('name', 'id');
        // } else {
        // }
        $user = $this->userRepository->getByCriteria(new ManagersCriteria())->pluck('name', 'id');

        $categoryProductsIDRaw = DB::table('categoriesproducts')->where('market_id', $market->id)->pluck('active', 'category_id');
        $categoryProductsID = $market->categoriesProducts()->pluck('category_id')->toArray();

        $categoriesProducts = [];
        

        $categoriesProducts = $this->categoryRepository->whereIn('id', $categoryProductsID)->orderBy('sort_id', 'asc')->get();

        $categories = [];
        foreach ($categoriesProducts as $category) {
            if (isset($categoryProductsIDRaw[$category->id])) {
                $category->active = $categoryProductsIDRaw[$category->id];
                $categories[] = $category;
            }
        }
        $categoriesProducts = $categories;


        return view('supermarkets.edit_departments')->with('market', $market)->with('categoriesProducts', $categoriesProducts);
    }
}
