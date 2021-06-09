<?php
/**
 * File name: OptionController.php
 * Last modified: 2020.06.03 at 20:04:42
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers;

use App\Criteria\Options\OptionsOfUserCriteria;
use App\DataTables\OptionDataTable;
use App\Http\Requests\CreateOptionRequest;
use App\Http\Requests\UpdateOptionRequest;
use App\Models\Product;
use App\Repositories\CustomFieldRepository;
use App\Repositories\OptionGroupRepository;
use App\Repositories\OptionRepository;
use App\Repositories\MarketRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class OptionController extends Controller
{
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
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var OptionGroupRepository
     */
    private $optionGroupRepository;

        /**
     * @var MarketRepository
     */
    private $marketRepository;

    public function __construct(OptionRepository $optionRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo,
        ProductRepository $productRepo,
        MarketRepository $marketRepo,
        OptionGroupRepository $optionGroupRepo) {
        parent::__construct();
        $this->optionRepository = $optionRepo;
        $this->marketRepository = $marketRepo;

        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->productRepository = $productRepo;
        $this->optionGroupRepository = $optionGroupRepo;
    }

    /**
     * Display a listing of the Option.
     *
     * @param OptionDataTable $optionDataTable
     * @return Response
     */
    public function index(OptionDataTable $optionDataTable)
    {
        return $optionDataTable->render('options.index');
    }

    /**
     * Show the form for creating a new Option.
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function create()
    {
        $market = [];
        $product = [];
        $optionGroup = [];
        $hasCustomField = in_array($this->optionRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionRepository->model());
            $html = generateCustomField($customFields);
        }
        // return view('options.create')->with("customFields", isset($html) ? $html : false);
        return view('options.create')->with("customFields", isset($html) ? $html : false)->with('market',$market)->with("product", $product)->with("optionGroup", $optionGroup);
    }

    /**
     * Store a newly created Option in storage.
     *
     * @param CreateOptionRequest $request
     *
     * @return Response
     */
    public function store(CreateOptionRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionRepository->model());
        try {
            $option = $this->optionRepository->create($input);
            $option->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($option, 'image');
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.option')]));

        return redirect(route('options.index'));
    }

    /**
     * Display the specified Option.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function show($id)
    {
        $this->optionRepository->pushCriteria(new OptionsOfUserCriteria(auth()->id()));

        $option = $this->optionRepository->findWithoutFail($id);

        if (empty($option)) {
            Flash::error('Option not found');

            return redirect(route('options.index'));
        }

        return view('options.show')->with('option', $option);
    }

    /**
     * Show the form for editing the specified Option.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function edit($id)
    {
        $this->optionRepository->pushCriteria(new OptionsOfUserCriteria(auth()->id()));
        $option = $this->optionRepository->findWithoutFail($id);
        if (empty($option)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.option')]));
            return redirect(route('options.index'));
        }
        // Esta estructura es nueva y es asi para que solo cargue el producto que esta seleccionado previamente
        $product;
        $optionGroup;
        $market = [];
        if (isset($option->market_id)) {
            $marketGEt = $this->marketRepository->findWithoutFail($option->market_id);
            if ($marketGEt != null) {

                $market = array($marketGEt->id => $marketGEt->name);
                $option->name_market = $marketGEt->name;
            } else {
                $market = [];
            }
        } else {
            $market = [];
        }
       
        if (isset($option->product_id)) {
            $productGette = $this->productRepository->findWithoutFail($option->product_id);
            if ($productGette != null) {

                $product = array($productGette->id => $productGette->name);
            } else {
                $product = [];
            }
        } else {
            $product = [];
        }

        if (isset($option->option_group_id)) {
            $optionGroupTes = $this->optionGroupRepository->findWithoutFail($option->option_group_id);
            if ($optionGroupTes != null) {
                $optionGroup = array($optionGroupTes->id => $optionGroupTes->name);

            } else {
                $optionGroup = [];
            }
        } else {
            $optionGroup = [];
        }
        $customFieldsValues = $option->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionRepository->model());
        $hasCustomField = in_array($this->optionRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('options.edit')->with('option', $option)->with("customFields", isset($html) ? $html : false)->with('market',$market)->with("product", $product)->with("optionGroup", $optionGroup);
    }

    /**
     * Update the specified Option in storage.
     *
     * @param int $id
     * @param UpdateOptionRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateOptionRequest $request)
    {
        $this->optionRepository->pushCriteria(new OptionsOfUserCriteria(auth()->id()));

        $option = $this->optionRepository->findWithoutFail($id);

        if (empty($option)) {
            Flash::error('Option not found');
            return redirect(route('options.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->optionRepository->model());
        try {
            $option_group_id = $input['option_group_id'];
            $option_group = $this->optionGroupRepository->findWithoutFail($option_group_id);
            if (!empty($option_group)) {
                $option_group['id_producto'] = $input['product_id'];
                $inputAlt = $input;
                $inputAlt['name'] = $option_group['name'];
                $inputAlt['id_producto'] = $option_group['id_producto'];
                $algo = $this->optionGroupRepository->update($inputAlt, $option_group['id']);
            }
            $option = $this->optionRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($option, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $option->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.option')]));

        return redirect(route('options.index'));
    }

    /**
     * Remove the specified Option from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function destroy($id)
    {
        $this->optionRepository->pushCriteria(new OptionsOfUserCriteria(auth()->id()));
        $option = $this->optionRepository->findWithoutFail($id);

        if (empty($option)) {
            Flash::error('Option not found');

            return redirect(route('options.index'));
        }

        $this->optionRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.option')]));

        return redirect(route('options.index'));
    }

    /**
     * Remove Media of Option
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $option = $this->optionRepository->findWithoutFail($input['id']);
        try {
            if ($option->hasMedia($input['collection'])) {
                $option->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function searchProduct(Request $request)
    {

        $products = DB::table('products')->where('name', 'LIKE', '%' . $request->input('palabra', '') . '%')->get(['id', 'name as text', 'market_id']);
        $idMarkets = [];
        $productsNew = [];
        $arrayNuevo = [];
        foreach ($products as $model) {
            if (!in_array($model->market_id, $idMarkets)) {
                $idMarkets[] = $model->market_id;
            }
        }

        $market = DB::table('markets')->whereIn('id', $idMarkets)->pluck('name', 'id');

        foreach ($products as $model) {
            if (isset($market[$model->market_id])) {
                $arrayNuevo[] = array(
                    'id' => $model->id,
                    'text' => $model->text . ' | ' . strtoupper($market[$model->market_id]),
                );
            } else {
                $arrayNuevo[] = array(
                    'id' => $model->id,
                    'text' => $model->text,
                );
            }
        }
        $productsNew = $arrayNuevo;
        return ['results' => $productsNew];
    }

    public function searchOptionGroups(Request $request)
    {
        $OptionGroups = $this->optionGroupRepository->where('name_admin', 'LIKE', '%' . $request->input('q', '') . '%')
            ->get(['id', 'name as text', 'name_admin']);
        $OptionGroupsNew = [];
        $arrayNuevo;
        foreach ($OptionGroups as $model) {
            if (!empty($model->name_admin)) {
                $arrayNuevo = array(
                    'id' => $model->id,
                    'text' => $model->name_admin,
                );
            } else {
                $arrayNuevo = array(
                    'id' => $model->id,
                    'text' => $model->text,
                );

            }
            $OptionGroupsNew[] = $arrayNuevo;
            unset($arrayNuevo[0]);

        }
        return ['results' => $OptionGroupsNew];

    }

    public function searchOptionGroupsFromMarket(Request $request)
    {
        $OptionGroups = DB::table('option_groups')->where('market_id', '=', $request->input('idMarket', ''))->where('name', 'LIKE', '%' . $request->input('optiongroup', '') . '%')
            ->get(['id', 'name as text', 'name_admin']);
        $OptionGroupsNew = [];
        $arrayNuevo = [];
        foreach ($OptionGroups as $model) {
            if (isset($model->name_admin) && $model->name_admin != 'null') {
                $arrayNuevo[] = array(
                    'id' => $model->id,
                    'text' => $model->text . '(' . strtoupper($model->name_admin) . ')',
                );
            } else {
                $arrayNuevo[] = array(
                    'id' => $model->id,
                    'text' => $model->text,
                );

            }

        }
        $OptionGroupsNew = $arrayNuevo;
        return ['results' => $OptionGroupsNew];

    }

    /**
     * get the options by Group.
     *
     * @param int $id
     * @param UpdateProductRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getOptionsByGroup(Request $request)
    {

        $id = $request['id'];
        $options = $this->optionRepository->where('option_group_id', '=', $id)->orderBy('sort_id', 'asc')->get();

        // return $options;
        $id = $request['id'];

        $idsOptions = DB::table('options_by_options_groups')->where('option_group_id', '=', $id)->pluck('active', 'option_id as id')->toArray();
        // $idsOptionsOther = DB::table('options')->where('option_group_id', '=', $id)->pluck('id')->toArray();
        $idsOptionsAll = [];

        foreach ($idsOptions as $idO => $id) {
            if (!in_array($idO, $idsOptionsAll)) {
                $idsOptionsAll[] = $idO;
            }
        }
        //Query para que solo consigan las opciones activas globalmente
        // $optionsGroupsEncontrados = DB::table('option_groups')->where('active','1')->whereIn('id', $idsOptionGroupsAll)->orderBy('sort_id', 'asc')->get();

        $OpcionesEncontradas = DB::table('options')->whereIn('id', $idsOptionsAll)->orderBy('sort_id', 'asc')->get();

        $opciones = [];
        $idMarket = $request['market_id'];

        foreach ($OpcionesEncontradas as $opcion) {
            if (isset($idsOptions[$opcion->id])) {
                $opcion->active = $idsOptions[$opcion->id];
                $opciones[] = $opcion;
            }

        }

        return $opciones;

    }

    public function updateOrderOptions(Request $request)
    {
        if ($request->has('ids')) {
            $arr = explode(',', $request->input('ids'));

            foreach ($arr as $sortOrder => $id) {
                // $product = $this->optionRepository->find($id);
                // $product->sort_id = $sortOrder;
                // $product->save();

                DB::table('options')->where('id', '=', $id)->update([
                    "sort_id" => $sortOrder,
                ]);

            }
            return ['success' => true, 'message' => 'Updated'];
        }
    }

    /**
     * Store a newly created Option by Group in storage.
     *
     * @param CreateOptionRequest $request
     *
     * @return Response
     */
    public function storeFromGroupOption(CreateOptionRequest $request)
    {
        $input = $request->all();

        try {
            $option = $this->optionRepository->create($input);

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }
    }

    /**
     * Update the specified Option in storage.
     *
     * @param int $id
     * @param UpdateOptionRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateFromGroupOption(UpdateOptionRequest $request)
    {
        $id = $request['id'];
        $option = $this->optionRepository->findWithoutFail($id);

        if (empty($option)) {
            Flash::error('Option not found');
            // return redirect(route('options.index'));
        } else {
            $input = $request->all();
            $option_group_id = $input['option_group_id'];

            $OptionGroups = DB::table('options_by_options_groups')->where('option_group_id', '=', $option_group_id)->where('option_id', '=', $id)->update([
                'active' => $input['active'],
            ]);

            try {
                $option_group = $this->optionGroupRepository->findWithoutFail($option_group_id);
                if (!empty($option_group)) {
                    $option_group['id_producto'] = $input['product_id'];
                    $inputAlt = $input;
                    $inputAlt['name'] = $option_group['name'];
                    $inputAlt['id_producto'] = $option_group['id_producto'];
                    $algo = $this->optionGroupRepository->update($inputAlt, $option_group['id']);
                }
                $input['active'] = '1';
                $option = $this->optionRepository->update($input, $id);

            } catch (ValidatorException $e) {
                Flash::error($e->getMessage());
            }
        }

    }

    /**
     * Remove the specified option from storage without get out this page.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroyAlt(Request $request)
    {
        $input = $request->all();

        $option = $this->optionRepository->findWithoutFail($input['id']);

        if (empty($option)) {

        }
        $this->optionRepository->delete($input['id']);
    }
    public function searchOptions(Request $request)
    {

        $categories = DB::table('options')->where('market_id', '=', $request->input('market_id', ''))
            ->where('name', 'LIKE', '%' . $request->input('option', '') . '%')
            ->get(['id', 'name as text']);
        return ['results' => $categories];
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
    public function removeOptionFromOptionGroup(Request $request)
    {
        $id = $request['id'];
        $idG = $request['idG'];
        $idP = $request['idP'];

        DB::table('options_by_options_groups')->where('option_id', '=', $id)->where('option_group_id', '=', $idG)->delete();
        DB::table('options')->where('id', '=', $id)->update([
            "product_id" => $idP,
        ]);

    }

}
