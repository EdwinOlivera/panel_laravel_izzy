<?php

namespace App\Http\Controllers;

use App\DataTables\CategoryDataTable;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\MarketRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class CategoryController extends Controller
{
    /** @var  CategoryRepository */
    private $categoryRepository;

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

    public function __construct(CategoryRepository $categoryRepo, MarketRepository $marketRepo, ProductRepository $productRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepo;
        $this->marketRepository = $marketRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->productRepository = $productRepo;
    }

    /**
     * Display a listing of the Category.
     *
     * @param CategoryDataTable $categoryDataTable
     * @return Response
     */
    public function index(CategoryDataTable $categoryDataTable)
    {
        return $categoryDataTable->render('categories.index');
    }

    /**
     * Show the form for creating a new Category.
     *
     * @return Response
     */
    public function create()
    {
        $marketsSelected = [];

        if (auth()->user()->hasRole('admin')) {
            $markets = $this->marketRepository->pluck('name', 'id');
        } else {
            $markets = $this->marketRepository->myActiveMarkets()->pluck('name', 'id');
        }
        $hasCustomField = in_array($this->categoryRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->categoryRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('categories.create')->with('marketsSelected', $marketsSelected)->with('markets', $markets)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param CreateCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreateCategoryRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->categoryRepository->model());
        try {
            $category = $this->categoryRepository->create($input);
            $category->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($category, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.category')]));

        return redirect(route('categories.index'));
    }

    /**
     * Display the specified Category.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error('Category not found');

            return redirect(route('categories.index'));
        }

        return view('categories.show')->with('category', $category);
    }

    /**
     * Show the form for editing the specified Category.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {

        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.category')]));

            return redirect(route('categories.index'));
        }
        if (auth()->user()->hasRole('admin')) {
            $markets = $this->marketRepository->pluck('name', 'id');
        } else {
            $markets = $this->marketRepository->myActiveMarkets()->pluck('name', 'id');
        }
        $marketsSelected = $category->categoriesProducts()->pluck('market_id')->toArray();

        $customFieldsValues = $category->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->categoryRepository->model());
        $hasCustomField = in_array($this->categoryRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('categories.edit')->with('marketsSelected', $marketsSelected)->with('category', $category)->with('markets', $markets)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param  int              $id
     * @param UpdateCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCategoryRequest $request)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error('Category not found');
            return redirect(route('categories.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->categoryRepository->model());
        try {
            $category = $this->categoryRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($category, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $category->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.category')]));

        return redirect(route('categories.index'));
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error('Category not found');

            return redirect(route('categories.index'));
        }

        $this->categoryRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.category')]));

        return redirect(route('categories.index'));
    }

    /**
     * Remove Media of Category
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $category = $this->categoryRepository->findWithoutFail($input['id']);
        try {
            if ($category->hasMedia($input['collection'])) {
                $category->getFirstMedia($input['collection'])->delete();
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
    public function getCategoriesByProduct(Request $request)
    {
        $id = $request['id'];
        $product = $this->productRepository->findWithoutFail($id);
        // Conseguimos todas las categorias asociadas al producto
        $idsCategory = DB::table('product_categories')->where('product_id', '=', $id)->pluck('category_id');
        $categoryFinal = DB::table('categories')->whereIn('id', $idsCategory)->get();

        //    $categoriesSelected = $product->categories()->pluck('categories.id')->toArray();
        //     $categoriesArray = [];

        //     $categoriesProducts = [];
        //     $arrayDeIDCategorias = [];

        //     foreach ($categoriesSelected as $category) {
        //         if (!in_array($category, $arrayDeIDCategorias)) {
        //             $arrayDeIDCategorias[] = $category;
        //         }
        //     }
        // return $arrayDeIDCategorias;
        // $categoriesArray[] = $this->categoryRepository->whereIn('id',(array)$arrayDeIDCategorias);
        // foreach ($arrayDeIDCategorias as $categoryID) {
        //     $categoriesArray[] = $this->categoryRepository->findWithoutFail($categoryID);

        // }
        return $categoryFinal;
    }

    public function sortOrden(Request $request)
    {
        if ($request->has('ids')) {
            $arr = explode(',', $request->input('ids'));

            foreach ($arr as $sortOrder => $id) {
                // $category = $this->categoryRepository->find($id);
                DB::table('categories')->where('id', '=', $id)->update([
                    "sort_id" => $sortOrder,
                ]);
                // $category->sort_id = $sortOrder;
                // $category->save();
            }

            return ['success' => true, 'message' => 'Updated'];
        }
    }
    public function setCategoriesToMarket(Request $request)
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
    public function createFromMarket(CreateCategoryRequest $request)
    {

        $input = $request->all();
        $input['activeCM'];
        $category = $this->categoryRepository->create($input);
        $categoryProductsIDRaw = DB::table('categoriesproducts')->where('category_id', $category->id)->update([
            "active" => $input['activeCM'],
        ]);

    }
    public function updateFromMarket(CreateCategoryRequest $request)
    {

        $input = $request->all();
        $id = $request['idCa'];
        $idMarket = $request['idMarket'];

        $category = $this->categoryRepository->update($input, $id);
        DB::table('categoriesproducts')->where('market_id', $idMarket)->where('category_id', $category->id)->update([
            "active" => $input['activeCM'],
        ]);
        $categoryProductsIDRaw = DB::table('categoriesproducts')->where('market_id', $idMarket)->where('category_id', $category->id)->first();
    }

    public function addProductFormMarket(Request $request)
    {

        $input = $request->all();
        $id = $request['idC'];
        $idMarket = $request['idMarket'];
        $idsProducts = DB::table('product_categories')->where('category_id', $id)->pluck('product_id')->toArray();
        DB::table('products')->whereIn('id', $input['products'])->update([
            "category_id" => $id,
        ]);
        $idProductsMarketsFilter = [];

        foreach ($input['products'] as $idPro) {

            $idsProducts[] = (int) $idPro;
        }

        $input['products'] = $idsProducts;
        $this->categoryRepository->update($input, $id);

        return $idsProducts;
    }
    public function searchCategory(Request $request)
    {

        $categories = DB::table('categories')->where('name', 'LIKE', '%' . $request->input('categoria', '') . '%')->get(['id', 'name as text']);

        $categoriesNew = [];

        foreach ($categories as $model) {

            $arrayNuevo[] = array(
                'id' => $model->id,
                'text' => $model->text,
            );

        }
        $categoriesNew = $arrayNuevo;
        return ['results' => $categoriesNew];
    }

    public function removeProductsFromCategory(Request $request)
    {
        $input = $request->all();
        $id = $request['idC'];
        $idMarket = $request['idMarket'];
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error('Producto no encontrado.');
        }
        DB::table('categoriesproducts')->where('category_id', '=', $id)->where('market_id', '=', $idMarket)->delete();

    }

}
