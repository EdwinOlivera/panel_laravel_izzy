<?php
/**
 * File name: MarketAPIController.php
 * Last modified: 2020.05.04 at 09:04:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;

use App\Criteria\Markets\ActiveCriteria;
use App\Criteria\Markets\MarketsOfFieldsCriteria;
use App\Criteria\Markets\NearCriteria;
use App\Criteria\Markets\PopularCriteria;
use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Repositories\CustomFieldRepository;
use App\Repositories\MarketRepository;
use App\Repositories\UploadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class MarketController
 * @package App\Http\Controllers\API
 */

class MarketAPIController extends Controller
{
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

    public function __construct(MarketRepository $marketRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->marketRepository = $marketRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;

    }

    /**
     * Display a listing of the Market.
     * GET|HEAD /markets
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->marketRepository->pushCriteria(new RequestCriteria($request));
            $this->marketRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->marketRepository->pushCriteria(new MarketsOfFieldsCriteria($request));
            if ($request->has('popular')) {
                $this->marketRepository->pushCriteria(new PopularCriteria($request));
            } else {
                $this->marketRepository->pushCriteria(new NearCriteria($request));
            }
            $this->marketRepository->pushCriteria(new ActiveCriteria());
            $markets = $this->marketRepository->all();

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($markets->toArray(), 'Markets enviados successfully');
    }

    /**
     * Display the specified Market.
     * GET|HEAD /markets/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Market $market */
        if (!empty($this->marketRepository)) {
            try {
                $this->marketRepository->pushCriteria(new RequestCriteria($request));
                $this->marketRepository->pushCriteria(new LimitOffsetCriteria($request));
                if ($request->has(['myLon', 'myLat', 'areaLon', 'areaLat'])) {
                    $this->marketRepository->pushCriteria(new NearCriteria($request));
                }
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $market = $this->marketRepository->findWithoutFail($id);

            $marketArray = $market->toArray();
            $idsDeparment = [];
            if (isset($marketArray['departments'])) {
                foreach ($marketArray['departments'] as $department) {
                    if (!in_array($department['id'], $idsDeparment)) {

                        $idsDeparment[] = $department['id'];
                    }
                }
                $checkActiveDepartment = DB::table('departments_market')->where('market_id', $id)->whereIn('department_id', $idsDeparment)->pluck('active', 'department_id')->toArray();
                $departmentsFilter = [];
                foreach ($marketArray['departments'] as $department) {
                    if ($checkActiveDepartment[$department['id']]) {

                        $departmentsFilter[] = $department;
                    }

                }
                $marketArray['departments'] = $departmentsFilter;
            }
            $idsSecntion = [];
            if (isset($marketArray['sections'])) {
                foreach ($marketArray['sections'] as $section) {
                    if (!in_array($section['id'], $idsDeparment)) {

                        $idsSecntion[] = $section['id'];
                    }
                }
                $checkActiveSections = DB::table('sections_markets')->where('market_id', $id)->whereIn('section_id', $idsSecntion)->pluck('active', 'section_id')->toArray();
                $sectionsFilter = [];
                $idsProducts = [];
                $productFinal = [];
                foreach ($marketArray['sections'] as $section) {
                    $productFinal = []; 
                    if ($checkActiveSections[$section['id']]) {
                        $idsProducts = DB::table('section_product')->where('section_id', '=', $section['id'])->where('active', '=', '1')->where('market_id', $marketArray['id'])->limit(20)->pluck('product_id')->toArray();
                        $products = DB::table('products')->whereIn('id', $idsProducts)->where('featured', '=', '1')->where('market_id', $marketArray['id'])->limit(20)->orderBy('sort_id', 'asc')->get()->toArray();
                        if (isset($products)) {
                            foreach ($products as $product) {
                                
                                $IdsOptionGroups = DB::table('option_group_market_products')->where('product_id', '=', $product->id)->where('active', '=', '1')->pluck('option_group_id')->toArray();
                                $optionFixter = [];
                                $optionGroupsFixter = [];
                                $AllOptionGroups = DB::table('option_groups')->whereIn('id', $IdsOptionGroups)->orderBy('sort_id', 'asc')->get()->toArray();

                                foreach ($AllOptionGroups as $IdOptionGroup) {

                                    $optionGroupsFixter [] = $IdOptionGroup;
                                    $idsOptions = DB::table('options_by_options_groups')->where('option_group_id', '=', $IdOptionGroup->id)->where('active', '=', '1')->pluck('option_id')->toArray();
                                    $options = DB::table('options')->whereIn('id', $idsOptions)->orderBy('sort_id', 'asc')->get()->toArray();

                                    foreach ($options as $option) {
                                        $option->option_group_id = $IdOptionGroup->id;
                                        $optionFixter [] = $option;
                                    }
                                    
                                    $product->options = $optionFixter;
                                    
                                }
                                $product->option_groups = $optionGroupsFixter;
                                $productFinal[] = $product;
                            }
                        }

                        $section['products'] = $productFinal;
                        $sectionsFilter[] = $section;
                    }
                }
                $marketArray['sections'] = $sectionsFilter;
            }

        }

        if (empty($market)) {
            return $this->sendError('Market not found');
        }

        return $this->sendResponse($marketArray, 'Establecimiento enviado exitosamente');
    }

    /**
     * Store a newly created Market in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        if (auth()->user()->hasRole('manager')) {
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
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($market->toArray(), __('lang.saved_successfully', ['operator' => __('lang.market')]));
    }

    /**
     * Update the specified Market in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $market = $this->marketRepository->findWithoutFail($id);

        if (empty($market)) {
            return $this->sendError('Market not found');
        }
        $input = $request->all();
        // return $input;
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->marketRepository->model());
        try {
            $market = $this->marketRepository->update($input, $id);
            $input['users'] = isset($input['users']) ? $input['users'] : [];
            $input['drivers'] = isset($input['drivers']) ? $input['drivers'] : [];
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($market, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $market->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($market->toArray(), __('lang.updated_successfully', ['operator' => __('lang.market')]));
    }

    /**
     * Remove the specified Market from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $market = $this->marketRepository->findWithoutFail($id);

        if (empty($market)) {
            return $this->sendError('Market not found');
        }

        $market = $this->marketRepository->delete($id);

        return $this->sendResponse($market, __('lang.deleted_successfully', ['operator' => __('lang.market')]));
    }
}
