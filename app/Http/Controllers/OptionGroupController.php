<?php

namespace App\Http\Controllers;

use App\DataTables\OptionGroupDataTable;
use App\Http\Requests\CreateOptionGroupRequest;
use App\Http\Requests\UpdateOptionGroupRequest;
use App\Repositories\CustomFieldRepository;
use App\Repositories\MarketRepository;
use App\Repositories\OptionGroupRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class OptionGroupController extends Controller
{
    /** @var  OptionGroupRepository */
    private $optionGroupRepository;

    /** @var  OptionRepository */
    private $optionRepository;

    /** @var  ProductRepository */
    private $productRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var MarketRepository
     */
    private $marketRepository;

    public function __construct(OptionGroupRepository $optionGroupRepo, OptionRepository $optionRepo, ProductRepository $productRepo, CustomFieldRepository $customFieldRepo, 
    MarketRepository $marketRepo)
    {
        parent::__construct();
        $this->marketRepository = $marketRepo;
        $this->optionRepository = $optionRepo;
        $this->optionGroupRepository = $optionGroupRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->productRepository = $productRepo;
    }

    /**
     * Display a listing of the OptionGroup.
     *
     * @param OptionGroupDataTable $optionGroupDataTable
     * @return Response
     */
    public function index(OptionGroupDataTable $optionGroupDataTable)
    {
        return $optionGroupDataTable->render('option_groups.index');
    }

    /**
     * Show the form for creating a new OptionGroup.
     *
     * @return Response
     */
    public function create()
    {

        $hasCustomField = in_array($this->optionGroupRepository->model(), setting('custom_field_models', []));
        $market = [];
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionGroupRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('option_groups.create')->with("customFields", isset($html) ? $html : false)->with('market',$market);
    }

    /**
     * Store a newly created OptionGroup in storage.
     *
     * @param CreateOptionGroupRequest $request
     *
     * @return Response
     */
    public function store(CreateOptionGroupRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionGroupRepository->model());
        try {
            $optionGroup = $this->optionGroupRepository->create($input);
            $optionGroup->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.option_group')]));

        return redirect(route('optionGroups.index'));
    }

    /**
     * Store a newly created OptionGroup in storage.
     *
     * @param CreateOptionGroupRequest $request
     *
     * @return Response
     */
    public function storeFromProduct(CreateOptionGroupRequest $request)
    {
        $input = $request->all();

        try {
            $optionGroup = $this->optionGroupRepository->create($input);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
        return $optionGroup;
    }

    /**
     * Display the specified OptionGroup.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);

        if (empty($optionGroup)) {
            Flash::error('Option Group not found');

            return redirect(route('optionGroups.index'));
        }

        return view('option_groups.show')->with('optionGroup', $optionGroup);
    }

    /**
     * Show the form for editing the specified OptionGroup.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);

        if (empty($optionGroup)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.option_group')]));

            return redirect(route('optionGroups.index'));
        }

       
        $market = [];
        if (isset($optionGroup->market_id)) {
            $marketGet = $this->marketRepository->findWithoutFail($optionGroup->market_id);

            if ($marketGet != null) {
                $market = array($marketGet->id => $marketGet->name);
                $optionGroup->name_market = $marketGet->name;
            } else {
                $market = [];
            }
        } else {
            $market = [];
        }

        $customFieldsValues = $optionGroup->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionGroupRepository->model());
        $hasCustomField = in_array($this->optionGroupRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('option_groups.edit')->with('optionGroup', $optionGroup)->with("market", $market)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified OptionGroup in storage.
     *
     * @param  int              $id
     * @param UpdateOptionGroupRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOptionGroupRequest $request)
    {
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);

        if (empty($optionGroup)) {
            Flash::error('Option Group not found');
            return redirect(route('optionGroups.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionGroupRepository->model());
        try {
            $optionGroup = $this->optionGroupRepository->update($input, $id);
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $optionGroup->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.option_group')]));

        return redirect(route('optionGroups.index'));
    }

    /**
     * Update the specified OptionGroup in storage.
     *
     * @param  int              $id
     * @param UpdateOptionGroupRequest $request
     *
     * @return Response
     */
    public function updateFromMarket(UpdateOptionGroupRequest $request)
    {
        $id = $request['idGrupo'];
        $productId = $request['id_producto'];
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);

        if (empty($optionGroup)) {
            Flash::error('Grupo de opciones no encontrado');
        }
        $idsOptionGroup = DB::table('option_group_market_products')->where('product_id', '=', $request['id_producto'])->where('option_group_id', '=', $id)->update([
            "active" => $request['active'],
        ]);
        $request['active'] = '1';
        $input = $request->all();
        $optionGroup = $this->optionGroupRepository->update($input, $id);

    }
    /**
     * Remove the specified OptionGroup from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $optionGroup = $this->optionGroupRepository->findWithoutFail($id);

        if (empty($optionGroup)) {
            Flash::error('Grupo de opciones no encontrado');

            return redirect(route('optionGroups.index'));
        }

        $this->optionGroupRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.option_group')]));

        return redirect(route('optionGroups.index'));
    }

    /**
     * Remove Media of OptionGroup
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $optionGroup = $this->optionGroupRepository->findWithoutFail($input['id']);
        try {
            if ($optionGroup->hasMedia($input['collection'])) {
                $optionGroup->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function searchOptionGroups(Request $request)
    {
        $products = $this->optionGroupRepository->where('name', 'LIKE', '%' . $request->input('q', '') . '%')
            ->get(['id', 'name as text', 'market_id']);
        $productsMarket = [];
        $productsNew = [];
        foreach ($products as $model) {
            if (!empty($model->market)) {
                $productsMarket[$model->market->name][$model->id] = $model->text;

            }
        }
        foreach ($products as $model) {
            if (!empty($model->market)) {
                $arrayNuevo = array(
                    'id' => $model->id,
                    'text' => $model->text . ' | ' . $model->market->name,
                );
                $productsNew[] = $arrayNuevo;
            }
        }
        return ['results' => $productsNew];
    }

    /**
     * get the group of options.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getOptionGroupByProduct(Request $request)
    {

        $id = $request['id'];

        // $optionGroupProduct = $this->optionGroupRepository->where('id_producto', '=', $id)->orderBy('sort_id', 'asc')->get();
        // return $optionGroupProduct;
        $idsOptionGroup = DB::table('option_group_market_products')->where('product_id', '=', $id)->pluck('active', 'option_group_id as id')->toArray();
        $idsOptionGroupOther = DB::table('option_groups')->where('id_producto', '=', $id)->pluck('id')->toArray();
        // $idsOptionGroupsAll = $idsOptionGroupOther;
        $idsOptionGroupsAll = [];

        foreach ($idsOptionGroup as $idP => $id) {
            if (!in_array($idP, $idsOptionGroupsAll)) {
                $idsOptionGroupsAll[] = $idP;
            }
        }
        //Query para que solo consigan las opciones activas globalmente
        // $optionsGroupsEncontrados = DB::table('option_groups')->where('active','1')->whereIn('id', $idsOptionGroupsAll)->orderBy('sort_id', 'asc')->get();

        $optionsGroupsEncontrados = DB::table('option_groups')->whereIn('id', $idsOptionGroupsAll)->orderBy('sort_id', 'asc')->get();
        $grupos = [];

        foreach ($optionsGroupsEncontrados as $grupo) {

            if (isset($idsOptionGroup[$grupo->id])) {
                $grupo->active = $idsOptionGroup[$grupo->id];
                $grupos[] = $grupo;
            }
        }
        return $grupos;
    }

    public function updateOrderGroupOption(Request $request)
    {
        if ($request->has('ids')) {
            $arr = explode(',', $request->input('ids'));

            foreach ($arr as $sortOrder => $id) {
                $product = $this->optionGroupRepository->find($id);
                $product->sort_id = $sortOrder;
                $product->save();
            }
            return ['success' => true, 'message' => 'Updated'];
        }
    }

    /**
     * Remove the specified Option Group from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroyAlt(Request $request)
    {
        $input = $request->all();
        $optionGroup = $this->optionGroupRepository->findWithoutFail($input['id']);

        if (empty($optionGroup)) {
            Flash::error('Grupo de opciones no encontrado.');

        }

        $options = $this->optionRepository->where('option_group_id', '=', $input['id']);
        foreach ($options as $option) {
            $this->optionRepository->delete($option->id);
        }
        $this->optionGroupRepository->delete($input['id']);

    }
    public function addOptionGroupsFromMarket(Request $request)
    {
        $input = $request->all();
        $idP = $request['idP'];
        $idMarket = $request['idMarket'];
        $idsGroupOption = DB::table('option_group_market_products')->where('product_id', '=', $idP)->pluck('option_group_id')->toArray();
        $idProdutsMarket = DB::table('option_groups')->where('id_producto', '=', $idP)->where('market_id', '=', $idMarket)->pluck('id');

        foreach ($idProdutsMarket as $idGO) {

            $idsGroupOption[] = (int) $idGO;
        }

        foreach ($input['optionGroupsList'] as $idGO) {

            $idsGroupOption[] = (int) $idGO;
        }

        $input['optionGroupsList'] = $idsGroupOption;
        $this->productRepository->update($input, $idP);
    }

    /**
     * Remove the specified Product from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function removeOptionGroupFromProduct(Request $request)
    {
        $input = $request->all();
        $product = $this->productRepository->findWithoutFail($input['id']);
        if (empty($product)) {
            Flash::error('Producto no encontrado.');
        }
        DB::table('option_group_market_products')->where('product_id', '=', $input['id'])->where('option_group_id', '=', $input['idG'])->delete();
        DB::table('option_groups')->where('id', '=', $input['idG'])->update([
            "id_producto" => '0',
        ]);

    }

    public function addOptionsFromMarket(Request $request)
    {

        $input = $request->all();

        $idGO = $request['idGO'];
        $idMarket = $request['idMarket'];
        $idsOption = DB::table('options_by_options_groups')->where('option_group_id', '=', $idGO)->pluck('option_id')->toArray();
        $idOptionsMarket = DB::table('options')->where('option_group_id', '=', $idGO)->where('market_id', '=', $idMarket)->pluck('id');

        foreach ($idOptionsMarket as $idO) {

            $idsOption[] = (int) $idO;
        }

        foreach ($input['optionsList'] as $idO) {

            $idsOption[] = (int) $idO;
        }

        $input['optionsList'] = $idsOption;

        return $this->optionGroupRepository->update($input, $idGO);
    }

}
