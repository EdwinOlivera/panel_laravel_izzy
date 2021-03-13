<?php
/**
 * File name: EncargoController.php
 * Last modified: 2020.05.05 at 16:55:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\DataTables\EncargoDataTable;
use App\Criteria\Encargos\EncargosOfUserCriteria;
use App\Repositories\EncargosStatusRepository;
use App\Events\EncargosChangedEvent;
use App\DataTables\ProductEncargoDataTable;
use App\Repositories\EncargoRepository;
use App\Notifications\AssignedEncargo;
use App\Http\Requests\CreateEncargosRequest;
use App\Notifications\StatusChangedEncargos;
use App\Http\Requests\UpdateEncargoRequest;
use App\Criteria\Users\ClientsCriteria;
use App\Criteria\Users\DriversCriteria;
use App\Criteria\Users\DriversOfMarketCriteria;
use App\Repositories\PaymentRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use Flash;




class EncargoController extends Controller
{
    /** @var  EncargoRepository */
    private $EncargoRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EncargosStatusRepository
     */
    private $encargosStatusRepository;
    /** @var  NotificationRepository */
    private $notificationRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;

    public function __construct(EncargoRepository $encargoRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo
        , EncargosStatusRepository $encargoStatusRepo, NotificationRepository $notificationRepo, PaymentRepository $paymentRepo
 
        )
        
    {
        parent::__construct();
 
        $this->EncargoRepository = $encargoRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->encargosStatusRepository = $encargoStatusRepo;
        $this->notificationRepository = $notificationRepo;
        $this->paymentRepository = $paymentRepo;
    }

    /**
     * Display a listing of the Order.
     *
     * @param EncargoDataTable $EncargoDataTable
     * @return Response
     */
    public function index(EncargoDataTable $EncargoDataTable)
    {
        return $EncargoDataTable->render('encargos.index');
    }

    /**
     * Show the form for creating a new Order.
     *
     * @return Response
     */
    public function create()
    {
       
     
        $encargo_setting = DB::table('encargos_settings')->get();
        if($encargo_setting[0] && isset($encargo_setting[0])){

            $encargo_setting = $encargo_setting[0];
        }
        
        return view('encargos.create')->with("encargo_setting", $encargo_setting);
        // return $encargo_setting;
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param CreateEncargosRequest $request
     *
     * @return Response
     */
    public function store(CreateEncargosRequest $request)
    {
        //Funcuin sin tocar
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->EncargoRepository->model());
        try {
            $encargo = $this->EncargoRepository->create($input);
            $encargo->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.encargo')]));

        return redirect(route('encargos.index'));
    }

    /**
     * Display the specified Order.
     *
     * @param int $id
     * @param ProductEncargoDataTable $ProductEncargoDataTable
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */

    public function show(ProductEncargoDataTable $ProductEncargoDataTable, $id)
    {
        //Funcion sin Modificar de la origina de Order
        $this->EncargoRepository->pushCriteria(new EncargosOfUserCriteria(auth()->id()));
        $encargo = $this->EncargoRepository->findWithoutFail($id);
        if (empty($encargo)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.encargo')]));

            return redirect(route('encargos.index'));
        }
        $subtotal = 0;

        foreach ($encargo->productOrders as $productOrder) {
            foreach ($productOrder->options as $option) {
                $productOrder->price += $option->price;
            }
            $subtotal += $productOrder->price * $productOrder->quantity;
        }

        $total = $subtotal + $encargo['delivery_fee'];
        $taxAmount = $total * $encargo['tax'] / 100;
        $total += $taxAmount;
        $ProductEncargoDataTable->id = $id;

        return $ProductEncargoDataTable->render('encargos.show', ["order" => $encargo, "total" => $total, "subtotal" => $subtotal,"taxAmount" => $taxAmount]);
    }



    /**
     * Show the form for editing the specified Encargo.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function edit($id)
    {
        $this->EncargoRepository->pushCriteria(new EncargosOfUserCriteria(auth()->id()));
        $encargo = $this->EncargoRepository->findWithoutFail($id);
        if (empty($encargo)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.Encargo')]));
            
            return redirect(route('encargos.index'));
        }
        
        $user = $this->userRepository->findWithoutFail($encargo['user_id']);
        $encargo['user_name']=$user['name'];
        $driver = $this->userRepository->findWithoutFail($encargo['driver_id']);

        $drivers = $this->userRepository->getByCriteria(new DriversCriteria())->pluck('name', 'id');

        $encargoStatus = $this->encargosStatusRepository->pluck('status', 'id');


        return view('encargos.edit')->with('encargo', $encargo)->with("drivers", $drivers)->with("driver", $driver)->with("encargoStatus", $encargoStatus);
    }

    /**
     * Update the specified Order in storage.
     *
     * @param int $id
     * @param UpdateEncargoRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateEncargoRequest $request)
    {

        $input = $request->all();

        if(isset($input['monto_base']) && $input['monto_base']){

            DB::table('encargos_settings')->where('id',1)->update([
                "monto_base" => $input['monto_base'],
                "monto_extra" => $input['monto_extra'],
                "habil_rang_extra" => $input['habil_rang_extra'],
                "rango_minimo" => $input['rango_minimo'],
                // "comision_repartidor" => $input['comision_repartidor'],
                'update_at'=> Carbon::now(),
    
            ]);
        }else{
            $this->EncargoRepository->pushCriteria(new EncargosOfUserCriteria(auth()->id()));
            $oldEncargo = $this->EncargoRepository->findWithoutFail($id);
            if (empty($oldEncargo)) {
                Flash::error(__('lang.not_found', ['operator' => __('lang.encargo')]));
                return redirect(route('encargos.index'));
            }
            
              $oldStatus = $oldEncargo->payment->status;
              $input['pagada'] =  $oldEncargo['pagada'];
              // $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->EncargoRepository->model());
              try {
                  if($input['driver_id'] !=null){
                      $input['assigned'] = 1;
                  }
              
                  //Asigna si se pago o no el encargo
                  if(isset($input['status']) && $input['status'] == "Paid"){
                      $input['pagada'] = 1;
                  }            
              
                  $encargo = $this->EncargoRepository->update($input, $id);
                  if (setting('enable_notifications', false)) {
                      if (isset($input['encargo_status_id']) && $input['encargo_status_id'] != $oldEncargo->order_status_id) {
                        //   Notification::send([$encargo->user], new StatusChangedEncargos($encargo));
                      }
                  }
                      if (isset($input['driver_id']) && ($input['driver_id'] != $oldEncargo['driver_id'])) {
                          $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                          if (!empty($driver)) {
                              Notification::send([$driver], new AssignedEncargo($encargo));
                          }
                      }
                  
                  
                  $this->paymentRepository->update([
                      "status" => $input['status'],
                  ], $encargo['payment_id']);
              
              
                  event(new EncargosChangedEvent($oldStatus, $encargo));
              
              } catch (ValidatorException $e) {
                  Flash::error($e->getMessage());
              }
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.encargo')]));

        return redirect(route('encargos.index'));
    }

    /**
     * Remove the specified Order from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function destroy($id)
    {
        // Funcion sin modificar
        if (!env('APP_DEMO', false)) {
            $this->EncargoRepository->pushCriteria(new EncargosOfUserCriteria(auth()->id()));
            $encargo = $this->EncargoRepository->findWithoutFail($id);

            if (empty($encargo)) {
                Flash::error(__('lang.not_found', ['operator' => __('lang.encargo')]));

                return redirect(route('encargos.index'));
            }

            $this->EncargoRepository->delete($id);

            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.encargo')]));


        } else {
            Flash::warning('Esta app solo es una demo, no puede modifcar esta sección ');
        }
        return redirect(route('encargos.index'));
    }

    /**
     * Remove Media of Order
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $encargo = $this->EncargoRepository->findWithoutFail($input['id']);
        try {
            if ($encargo->hasMedia($input['collection'])) {
                $encargo->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}