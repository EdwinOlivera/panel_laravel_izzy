<?php
/**
 * File name: TypeMarketController.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers;

use App\Criteria\Markets\MarketsOfUserCriteria;
use App\Criteria\Users\ManagersClientsCriteria;
use App\Criteria\Users\ManagersCriteria;
use App\DataTables\MarketDataTable;
use App\DataTables\TypeMarketDataTable;
use App\Http\Requests\CreateTypeMarketRequest;
use App\Http\Requests\UpdateTypeMarketRequest;
use App\Models\TypeMarket;
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

class TypeMarketController extends Controller
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
        $this->TypeMarketRepository = $marketRepo;
        $this->productRepository = $productRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->userRepository = $userRepo;
        $this->categoryRepository = $categoryRepo;
        $this->fieldRepository = $fieldRepository;
        $this->TypeMarketRepository = $typeMarketRepo;
    }

    /**
     * Display a listing of the Market.
     *
     * @param TypeMarketDataTable $typeMarketDataTable
     * @return Response
     */
    public function index(TypeMarketDataTable $typeMarketDataTable)
    {
        return $typeMarketDataTable->render('types_market.index');
    }

    /**
     * Show the form for creating a new Market.
     *
     * @return Response
     */
    public function create()
    {

        $hasCustomField = in_array($this->customFieldRepository->model(), setting('custom_field_models', []));
        $typeMarket = new TypeMarket;
        $typeMarket->enable = true;

        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->TypeMarketRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('types_market.create')->with("customFields", isset($html) ? $html : false)->with('typeMarket',$typeMarket);
    }

    /**
     * Store a newly created Market in storage.
     *
     * @param CreateTypeMarketRequest $request
     *
     * @return Response
     */
    public function store(CreateTypeMarketRequest $request)
    {
        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->TypeMarketRepository->model());
        try {
            $typeMarket = $this->TypeMarketRepository->create($input);
            $typeMarket->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($typeMarket, 'image');
            }

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.type_market')]));

        return redirect(route('typesMarket.index'));
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
        $this->TypeMarketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $typeMarket = $this->TypeMarketRepository->findWithoutFail($id);

        if (empty($typeMarket)) {
            Flash::error('Tipo no encontrado');

            return redirect(route('typesMarket.index'));
        }

        return view('types_market.show')->with('market', $typeMarket);
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
        $typeMarket = $this->TypeMarketRepository->findWithoutFail($id);

        if (empty($typeMarket)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.type_market')]));
            return redirect(route('typesMarket.index'));
        }

        return view('types_market.edit')->with('typeMarket', $typeMarket);
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
        $this->TypeMarketRepository->pushCriteria(new MarketsOfUserCriteria(auth()->id()));
        $typeMarket = $this->TypeMarketRepository->findWithoutFail($id);

        if (empty($typeMarket)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.type_market')]));
            return redirect(route('typesMarket.index'));
        }
        if ($typeMarket['active'] == 0) {
            $user = $this->userRepository->getByCriteria(new ManagersClientsCriteria())->pluck('name', 'id');
        } else {
            $user = $this->userRepository->getByCriteria(new ManagersCriteria())->pluck('name', 'id');
        }

        // $products = $this->productRepository->where('market_id', $typeMarket->id)->orderBy('sort_id', 'asc')->get(['id', 'category_id']);
        $categoryProductsIDRaw = DB::table('categoriesproducts')->where('market_id', $typeMarket->id)->pluck('active', 'category_id');
        $categoryProductsID = $typeMarket->categoriesProducts()->pluck('category_id')->toArray();

        $categoriesProducts = [];
        // $arrayDeIDCategorias = $categoryProductsID;

        // foreach ($products as $product) {

        //     $categoriesSelected = $product->categories()->pluck('categories.id')->toArray();
        //     if (!in_array($product->category_id, $arrayDeIDCategorias)) {
        //         $arrayDeIDCategorias[] = $product->category_id;

        //     }

        //     foreach ($categoriesSelected as $category) {
        //         if (!in_array($category, $arrayDeIDCategorias)) {
        //             $arrayDeIDCategorias[] = $category;
        //         }
        //     }
        // }

        $categoriesProducts = $this->categoryRepository->whereIn('id', $categoryProductsID)->orderBy('sort_id', 'asc')->get();

        // return $arrayDeIDCategorias;
        // Listado de categorias que se muestran en el Select 2 ya seleccionadas
        // $categoriesSelected = $this->categoryRepository->whereIn('id', $arrayDeIDCategorias)->pluck('id')->toArray();
        // Esta seccion es para actualizar la Tabla donde se registran las categorias que esten asociadas a al menos 1 producto del establecimiento
        // $arrayIdCategorias = array('categoriesProducts' => $categoriesSelected);
        // $this->TypeMarketRepository->update($arrayIdCategorias, $typeMarket->id);

        $categories = [];
        foreach ($categoriesProducts as $category) {
            if (isset($categoryProductsIDRaw[$category->id])) {
                $category->active = $categoryProductsIDRaw[$category->id];
                $categories[] = $category;
            }
        }
        $categoriesProducts = $categories;

        // $products = [];

        // $products = DB::table('products')->where('market_id', $id)->pluck('id');
        // if (isset($products[0])) {

        //     $verficarID = DB::table('option_groups')->where('id_producto', $products[0])->first();
        //     if (isset($verficarID->market_id)) {

        //         // return $verficarID->market_id;
        //     } else {
        //         foreach ($products as $product) {
        //             $products = DB::table('option_groups')->where('id_producto', $product)->update([
        //                 "market_id" => $id,
        //             ]);

        //         }

        //     }
        // } else {

        // }

        // $products = $this->productRepository->where('market_id', '=', $typeMarket->id)->orderBy('sort_id', 'asc')->pluck('name', 'id')->toArray();

        return view('types_market.edit_market_complete')->with('market', $typeMarket)->with('categoriesProducts', $categoriesProducts);
    }

    /**
     * Update the specified Market in storage.
     *
     * @param int $id
     * @param UpdateTypeMarketRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateTypeMarketRequest $request)
    {
        $oldTypeMarket = $this->TypeMarketRepository->findWithoutFail($id);

        if (empty($oldTypeMarket)) {
            Flash::error('Tipo no encontrado');
            return redirect(route('typesMarket.index'));
        }
        $input = $request->all();

        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->TypeMarketRepository->model());
        try {

            $typeMarket = $this->TypeMarketRepository->update($input, $id);
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($typeMarket, 'image');
            }

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.type_market')]));

        return redirect(route('typesMarket.index'));
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
            $typeMarket = $this->TypeMarketRepository->findWithoutFail($id);

            if (empty($typeMarket)) {
                Flash::error('Tipo no encontrado');

                return redirect(route('typesMarket.index'));
            }

            $this->TypeMarketRepository->delete($id);

            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.type_market')]));
        } else {
            Flash::warning('Esta app solo es una demo, no puede modifcar esta sección ');
        }
        return redirect(route('typesMarket.index'));
    }

    /**
     * Remove Media of Market
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $typeMarket = $this->TypeMarketRepository->findWithoutFail($input['id']);
        try {
            if ($typeMarket->hasMedia($input['collection'])) {
                $typeMarket->getFirstMedia($input['collection'])->delete();
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
                // $product = $this->productRepository->find($id);
                // $product->sort_id = $sortOrder;
                // $product->save();
                DB::table('products')->where('id', '=', $id)->update([
                    "sort_id" => $sortOrder,
                ]);

            }
            return ['success' => true, 'message' => 'Updated'];
        }
    }
}
