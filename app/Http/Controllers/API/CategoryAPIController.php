<?php
/**
 * File name: CategoryAPIController.php
 * Last modified: 2020.05.04 at 09:04:18
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;

use App\Criteria\Categories\CategoriesOfFieldsCriteria;
use App\Criteria\Categories\CategoriesOfMarketCriteria;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Repositories\MarketRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class CategoryController
 * @package App\Http\Controllers\API
 */
class CategoryAPIController extends Controller
{
    /** @var  CategoryRepository */
    private $categoryRepository;
    /** @var  MarketRepository */
    private $marketRepository;

    public function __construct(CategoryRepository $categoryRepo, MarketRepository $marketRepo)
    {
        $this->categoryRepository = $categoryRepo;
        $this->marketRepository = $marketRepo;
    }

    /**
     * Display a listing of the Category.
     * GET|HEAD /categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        try {
            $this->categoryRepository->pushCriteria(new RequestCriteria($request));
            $this->categoryRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->categoryRepository->pushCriteria(new CategoriesOfFieldsCriteria($request));
            $this->categoryRepository->pushCriteria(new CategoriesOfMarketCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $categories = $this->categoryRepository->all();

        return $this->sendResponse($categories->toArray(), 'Categories Conseguidas successfully');
    }

    /**
     * Display the specified Category.
     * GET|HEAD /categories/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Category $category */
        if (!empty($this->categoryRepository)) {
            $category = $this->categoryRepository->findWithoutFail($id);
        }

        if (empty($category)) {
            return $this->sendError('Category not found');
        }

        return $this->sendResponse($category->toArray(), 'Category retrieved successfully');
    }

    public function getCategoriesMarket($id)
    {

        $market = $this->marketRepository->findWithoutFail($id);
        $categoriesFinal = [];

        if (!empty($market)) {
            $category = $this->categoryRepository->findWithoutFail('4');

            $categories = $market->categoriesProducts()->orderBy('sort_id', 'asc')->get();
            $checkActiveCategory = DB::table('categoriesproducts')->where('market_id', $id)->pluck('active', 'category_id')->toArray();
            foreach ($categories as $category) {
                if ($category->active) {

                    if ($checkActiveCategory[$category->id]) {
                        $categoriesFinal[] = $category;
                    }

                }
            }
            $algo = [];
            $products = [];
            foreach ($categoriesFinal as $categoryFinal) {
                
                $products = $categoryFinal->products()->where('featured', '=', '1')->where('market_id', '=', $id)->orderBy('sort_id', 'asc')->get(['id', 'name', 'price', 'discount_price', 'description', 'deliverable', 'featured', 'market_id']);
                foreach($products as $product){
                    $product->category_id = $categoryFinal->id; 
                }
                $categoryFinal['products'] = $products;
            }

        }

        return $this->sendResponse($categoriesFinal, 'Categories Conseguidas successfully');
    }
}
