<?php
/**
 * File name: ProductAPIController.php
 * Last modified: 2020.05.04 at 09:04:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;

use App\Criteria\Products\NearCriteria;
use App\Criteria\Products\ProductsOfCategoriesCriteria;
use App\Criteria\Products\ProductsOfFieldsCriteria;
use App\Criteria\Products\TrendingWeekCriteria;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\CustomFieldRepository;
use App\Repositories\OptionGroupRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UploadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class ProductController
 * @package App\Http\Controllers\API
 */
class ProductAPIController extends Controller
{
    /** @var  ProductRepository */
    private $productRepository;

    /** @var  OptionGroupRepository */
    private $optionGroupRepository;

    /** @var  OptionRepository */
    private $optionRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;
    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(ProductRepository $productRepo, OptionRepository $optionRepo, OptionGroupRepository $optionGroupRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->productRepository = $productRepo;
        $this->optionRepository = $optionRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->optionGroupRepository = $optionGroupRepo;

    }

    /**
     * Display a listing of the Product.
     * GET|HEAD /products
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->productRepository->pushCriteria(new RequestCriteria($request));
            $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->productRepository->pushCriteria(new ProductsOfFieldsCriteria($request));
            if ($request->get('trending', null) == 'week') {
                $this->productRepository->pushCriteria(new TrendingWeekCriteria($request));
            } else {
                $this->productRepository->pushCriteria(new NearCriteria($request));
            }

            $products = $this->productRepository->all();

            $productsArray = $products->toArray();
            $productsFinal = [];
            $valueActiveProduct = [];
            foreach ($productsArray as $product) {

                $valueActiveProduct = DB::table('product_categories')->where('product_id', '=', $product['id'])->get('active');
                if ($valueActiveProduct[0]->active) {
                    $productsFinal[] = $product;
                }

            }
            $productsArray = $productsFinal;
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($productsArray, 'Productos enviados successfully');
    }

    /**
     * Display a listing of the Product.
     * GET|HEAD /products/categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(Request $request)
    {
        $idsCategories = [];
        if (isset($request['categories'])) {
            foreach ($request['categories'] as $idCategory) {
                if ((int) $idCategory != 0) {
                    $idsCategories[] = (int) $idCategory;
                }
            }
        }
        $marketId = '0';
        if (isset($request['marketid'])) {
            $marketId = $request['marketid'];
        }
        $promo = '0';
        if (isset($request['promo'])) {
            $promo = $request['promo'];
        }
        try {
            if (count($idsCategories) != 0) {
                $request['categories'] = ['0'];
            }
            $this->productRepository->pushCriteria(new RequestCriteria($request));
            $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->productRepository->pushCriteria(new ProductsOfFieldsCriteria($request));
            $this->productRepository->pushCriteria(new ProductsOfCategoriesCriteria($request));

            $products = $this->productRepository->all();

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        // return $this->sendResponse($products->toArray(), 'Products retrieved successfully');
        $productsFinal = [];

        $productsArray = $products->toArray();
        if ($promo) {
            return $this->sendResponse($productsArray, 'Promos enviados');

        } else {

            if (count($idsCategories) > 0) {
                $idMarket = $marketId;
                $valueActiveCategory = DB::table('categoriesproducts')->where('market_id', '=', $idMarket)->whereIn('category_id', $idsCategories)->pluck('active', 'category_id');
                $idsProducts = DB::table('products')->where('market_id', '=', $idMarket)->get(['featured', 'id'])->toArray();
                $algo = [];
                foreach ($idsProducts as $idP) {
                    if ($idP->featured) {
                        $algo[] = $idP->id;
                    }
                }
                $datosProductosRaw = DB::table('product_categories')->whereIn('category_id', $idsCategories)->whereIn('product_id', $algo)->where('active', '1')->get();
                $idsProducts = [];
                foreach ($datosProductosRaw as $idPR) {
                    if ($idPR->active) {
                        $idsProducts[] = $idPR->product_id;
                    }
                }
                $productsFilter = $this->productRepository->whereIn('id', $idsProducts)->get();
                $productsFinal = $productsFilter;

            } else if (count($productsArray) != 0) {

                $idMarket = $productsArray[0]['market_id'];
                $valueActiveCategory = DB::table('categoriesproducts')->where('market_id', '=', $idMarket)->pluck('active', 'category_id');
                $idsCategory = [];
                foreach ($valueActiveCategory as $categoryID => $id) {
                    $idsCategory[] = $categoryID;
                }
                $productsTmp = [];
                foreach ($productsArray as $product) {
                    $valueActiveProduct = DB::table('product_categories')->whereIn('category_id', $idsCategory)->where('product_id', '=', $product['id'])->pluck('active');
                    foreach ($valueActiveProduct as $value) {
                        if ($value) {
                            $productsTmp[] = $product;
                        }
                    }
                }

                $productsFinal = $productsTmp;

            }
        }
        return $this->sendResponse($productsFinal, 'Productos filtrados enviados');
    }

    /**
     * Display the specified Product.
     * GET|HEAD /products/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Product $product */
        $valueActiveProduct = DB::table('product_categories')->where('product_id', '=', $id)->get('active');

        if ($valueActiveProduct[0]->active) {
            if (!empty($this->productRepository)) {
                try {
                    $this->productRepository->pushCriteria(new RequestCriteria($request));
                    $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));
                } catch (RepositoryException $e) {
                    return $this->sendError($e->getMessage());
                }
                // Desde aqui se puede conseguir las opciones y grupos de opciones
                // "option_groups"
                $product = $this->productRepository->findWithoutFail($id);
            }

            if (empty($product)) {
                return $this->sendError('Product not found');
            }
            $productArray = $product->toArray();

            $valueActiveOptionsGroup = [];
            $idOptionsGroup = DB::table('option_group_market_products')->where('product_id', '=', $productArray['id'])->pluck('option_group_id')->toArray();
            $optionsGroupRaw = $this->optionGroupRepository->whereIn('id', $idOptionsGroup)->orderBy('sort_id')->get()->toArray();

            $optionsFinal = [];
            $optionsGroupFinal = [];
            $optionsGroupArray = $optionsGroupRaw;
            $valueActiveOptionsGroup = DB::table('option_group_market_products')->where('product_id', '=', $productArray['id'])->pluck('active', 'option_group_id')->toArray();

            foreach ($optionsGroupArray as $optionsGroup) {
                if (isset($valueActiveOptionsGroup[$optionsGroup['id']])) {
                    if ($valueActiveOptionsGroup[$optionsGroup['id']]) {
                        $optionsGroup['active'] = true;
                        $optionsGroupFinal[] = $optionsGroup;
                    }
                }
            }
            $algo = [];
            $idsOptions = [];
            foreach ($idOptionsGroup as $idOG) {
                $objestOptions = DB::table('options_by_options_groups')->where('option_group_id', '=', $idOG)->get()->toArray();
                foreach ($objestOptions as $valueOption) {
                    if ($valueOption->active) {
                        $idsOptions[] = $valueOption->option_id;
                    }

                }
            }
            
            $optionsRaw = $this->optionRepository->whereIn('id', $idsOptions)->orderBy('sort_id')->get()->toArray();

            foreach ($idOptionsGroup as $idOG) {
                $valuesActiveOptions = DB::table('options_by_options_groups')->where('option_group_id', '=', $idOG)->pluck('active', 'option_id')->toArray();
                $optionByOptionsGroup = DB::table('options_by_options_groups')->where('option_group_id', '=', $idOG)->pluck('option_group_id', 'option_id')->toArray();

                foreach ($optionsRaw as $option) {
                    if (isset($valuesActiveOptions[$option['id']])) {
                        if ($valuesActiveOptions[$option['id']]) {
                            $option['option_group_id'] = $optionByOptionsGroup[$option['id']];
                            $optionsFinal[] = $option;
                        }
                    }
                }
            }

            $productArray['options'] = $optionsFinal;
            $productArray['option_groups'] = $optionsGroupFinal;

            return $this->sendResponse($productArray, 'Producto enviado exitosamente');
        }
        return $this->sendResponse([], 'Producto enviado exitosamente');

    }

    /**
     * Store a newly created Product in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
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
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($product->toArray(), __('lang.saved_successfully', ['operator' => __('lang.product')]));
    }

    /**
     * Update the specified Product in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }
        $input = $request->all();
        DB::table('product_categories')->where('product_id', '=', $id)->update([
            "active" => $input['featured'],
        ]);
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
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($product->toArray(), __('lang.updated_successfully', ['operator' => __('lang.product')]));

    }

    /**
     * Remove the specified Product from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $product = $this->productRepository->delete($id);

        return $this->sendResponse($product, __('lang.deleted_successfully', ['operator' => __('lang.product')]));

    }

}
