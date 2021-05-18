<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MarketsPayout;
use App\Repositories\PolygonRepository;
use App\Repositories\PolygonZoneRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class MarketsPayoutController
 * @package App\Http\Controllers\API
 */

class PolygonZoneAPIController extends Controller
{

    /** @var  PolygonZoneRepository */
    private $polygonZoneRepository;

    /** @var  PolygonRepository */
    private $polygonRepository;

    public function __construct(PolygonZoneRepository $polygonZoneRepo, PolygonRepository $polygonRepo)
    {
        $this->polygonZoneRepository = $polygonZoneRepo;
        $this->polygonRepository = $polygonRepo;
    }

    /**
     * Display a listing of the MarketsPayout.
     * GET|HEAD /marketsPayouts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->polygonZoneRepository->pushCriteria(new RequestCriteria($request));
            $this->polygonZoneRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $polygonZone = $this->polygonZoneRepository->all();

        return $this->sendResponse($polygonZone->toArray(), 'Poligonos de zonas eviados exitosamente');
    }

    /**
     * Display the specified MarketsPayout.
     * GET|HEAD /marketsPayouts/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id, Request $request)
    {
        /** @var MarketsPayout $polygonZone */
        if (!empty($this->polygonZoneRepository)) {
            try {
                $this->polygonZoneRepository->pushCriteria(new RequestCriteria($request));
                $this->polygonZoneRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $polygonZone = $this->polygonZoneRepository->findWithoutFail($id);
        }

        if (empty($polygonZone)) {
            return $this->sendError('Poligono de zona no encontrado');
        }

        return $this->sendResponse($polygonZone->toArray(), 'Poligono enviado exitosamente');
    }

    /**
     * Store a newly created Polygon Zone in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        try {
            $polygons = $input['polygons'];
            $input['polygons'] = [];
            $polygonZone = $this->polygonZoneRepository->create($input);
            $cantidad = 0;
            foreach ((array) $polygons as $polygon) {
                // $algo = json_decode($polygon, true);
                $polygon['cantidad'];
                $cantidad = $polygon['cantidad'];
                $inputPolygon['polygon_zone_id'] = $polygonZone->id;
                $polygonCreado = $this->polygonRepository->create((array) $inputPolygon);
                // return ["polygon_zone_id" => $inputPolygon['polygon_zone_id'], "cantidad" => $cantidad, '$polygonCreado->id' => $polygonCreado->id];
                for ($iterador = 1; $iterador <= $cantidad; $iterador++) {
                    DB::table('latlong_polygon')->insert([
                        'longitude' => $polygon['long_' . $iterador],
                        'latitude' => $polygon['lat_' . $iterador],
                        'polygon_id' => $polygonCreado->id,
                    ]);
                }

            }

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($polygonZone->toArray(), __('lang.saved_successfully', ['operator' => __('Poligono de zona')]));
    }

    /**
     * Update the specified DeliveryAddress in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $polygonZone = $this->polygonZoneRepository->findWithoutFail($id);

        if (empty($polygonZone)) {
            return $this->sendError('Polignos de zona no encotrado');
        }
        $input = $request->all();

        try {
            $polygons = $input['polygons_update'];
            $polygonZone = $this->polygonZoneRepository->update($input, $id);
            // DB::table('polygons')->where('polygon_zone_id', $polygonZone->id)->delete();

            $cantidad = 0;
            $idsPolygon = $polygonZone->polygons()->pluck('id')->toArray();
            DB::table('polygons')->whereIn('id', $idsPolygon)->delete();
            DB::table('latlong_polygon')->whereIn('polygon_id', $idsPolygon)->delete();

            foreach ((array) $polygons as $polygon) {
                $polygon['cantidad'];
                $cantidad = $polygon['cantidad'];
                $inputPolygon['polygon_zone_id'] = $polygonZone->id;
                // DB::table('latlong_polygon')->where('polygon_id', $idPolygon)->delete();

                $polygonCreado = $this->polygonRepository->create((array) $inputPolygon);
                for ($iterador = 1; $iterador <= $cantidad; $iterador++) {
                    DB::table('latlong_polygon')->insert([
                        'longitude' => $polygon['long_' . $iterador],
                        'latitude' => $polygon['lat_' . $iterador],
                        'polygon_id' => $polygonCreado->id,
                    ]);
                }

            }

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($polygonZone->toArray(), __('lang.updated_successfully', ['operator' => __('Poligono de zona')]));

    }

    /**
     * Remove the specified Address from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $address = $this->polygonZoneRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError('Pligono de Zona no encontrado');

        }

        $this->polygonZoneRepository->delete($id);

        return $this->sendResponse($address, __('lang.deleted_successfully', ['operator' => __('Polignos de zona')]));

    }
}
