<?php
/**
 *
 * File name: SectionAPIController.php
 * Last modified: 2020.05.04 at 09:04:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Sección;
use App\Repositories\CustomFieldRepository;
use App\Repositories\MarketRepository;
use App\Repositories\SectionRepository;
use App\Repositories\UploadRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class SubdeparmentController
 * @package App\Http\Controllers\API
 */

class SectionAPIController extends Controller
{
    /** @var  MarketRepository */
    private $marketRepository;

    /** @var  SectionRepository */
    private $sectionRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(MarketRepository $marketRepo,
        SectionRepository $sectionRepo,
        CustomFieldRepository $customFieldRepo,
        UploadRepository $uploadRepo) {
        parent::__construct();
        $this->marketRepository = $marketRepo;
        $this->sectionRepository = $sectionRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;

    }

    /**
     * Display a listing of the Sección.
     * GET|HEAD /markets
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->sectionRepository->pushCriteria(new RequestCriteria($request));
            $this->sectionRepository->pushCriteria(new LimitOffsetCriteria($request));

            $deparments = $this->sectionRepository->all();

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($deparments->toArray(), 'Secciones enviadas Satisfactoriamente');
    }

    /**
     * Display the specified Sección.
     * GET|HEAD /markets/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Sección $section */
        if (!empty($this->sectionRepository)) {
            try {
                $this->sectionRepository->pushCriteria(new RequestCriteria($request));
                $this->sectionRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $section = $this->sectionRepository->findWithoutFail($id);
            if (isset($section)) {

                $sectionArray = $section->toArray();
                $idsProducts = [];
                $productsFilter = [];

                if (isset($sectionArray['products'])) {

                    foreach ($sectionArray['products'] as $product) {
                        if (!in_array($product['id'], $idsProducts)) {

                            $idsProducts[] = $product['id'];
                        }
                    }

                    $checkActiveProducts = DB::table('section_product')->where('section_id', $id)->where('active', '=', '1')->whereIn('product_id', $idsProducts)->pluck('active', 'product_id')->toArray();
                    foreach ($sectionArray['products'] as $product) {
                        if (isset($checkActiveProducts[$product['id']])) {

                            if ($checkActiveProducts[$product['id']]) {

                                $IdsOptionGroups = DB::table('option_group_market_products')->where('product_id', '=', $product['id'])->where('active', '=', '1')->pluck('option_group_id')->toArray();
                                $optionFixter = [];
                                $optionGroupsFixter = [];
                                $AllOptionGroups = DB::table('option_groups')->whereIn('id', $IdsOptionGroups)->orderBy('sort_id', 'asc')->get()->toArray();

                                foreach ($AllOptionGroups as $IdOptionGroup) {

                                    $optionGroupsFixter[] = $IdOptionGroup;
                                    $idsOptions = DB::table('options_by_options_groups')->where('option_group_id', '=', $IdOptionGroup->id)->where('active', '=', '1')->pluck('option_id')->toArray();
                                    $options = DB::table('options')->whereIn('id', $idsOptions)->orderBy('sort_id', 'asc')->get()->toArray();

                                    foreach ($options as $option) {
                                        $option->option_group_id = $IdOptionGroup->id;
                                        $optionFixter[] = $option;
                                    }

                                    $product['options'] = $optionFixter;

                                }
                                $product['option_groups'] = $optionGroupsFixter;

                                $productsFilter[] = $product;
                            }
                        }

                    }
                }
                $sectionArray['products'] = $productsFilter;
            } else {
                return $this->sendResponse([], 'No se encontro la Sección');

            }

        }

        if (empty($section)) {
            return $this->sendError('Sección no encontrada');
        }

        return $this->sendResponse($sectionArray, 'Sección enviada exitosamente');
    }

    /**
     * Store a newly created Sección in storage.
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
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($section->toArray(), __('lang.saved_successfully', ['operator' => __('lang.deparment')]));
    }

    /**
     * Update the specified Sección in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $section = $this->sectionRepository->findWithoutFail($id);

        if (empty($section)) {
            return $this->sendError('Sección no encontrada');
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->sectionRepository->model());
        try {
            $section = $this->sectionRepository->update($input, $id);
            $input['users'] = isset($input['users']) ? $input['users'] : [];
            $input['drivers'] = isset($input['drivers']) ? $input['drivers'] : [];
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
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($section->toArray(), __('lang.updated_successfully', ['operator' => __('lang.deparment')]));
    }

    /**
     * Remove the specified Sección from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $section = $this->sectionRepository->findWithoutFail($id);

        if (empty($section)) {
            return $this->sendError('Sección no encontrada');
        }

        $section = $this->sectionRepository->delete($id);

        return $this->sendResponse($section, __('lang.deleted_successfully', ['operator' => __('lang.deparment')]));
    }
}
