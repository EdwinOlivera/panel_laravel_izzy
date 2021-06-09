<?php

/**
 * File name: UserAPIController.php
 * Last modified: 2020.05.04 at 09:04:09
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Prettus\Validator\Exceptions\ValidatorException;

class UserAPIController extends Controller
{
    private $userRepository;
    private $uploadRepository;
    private $roleRepository;
    private $customFieldRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, UploadRepository $uploadRepository, RoleRepository $roleRepository, CustomFieldRepository $customFieldRepo)
    {
        $this->userRepository = $userRepository;
        $this->uploadRepository = $uploadRepository;
        $this->roleRepository = $roleRepository;
        $this->customFieldRepository = $customFieldRepo;
    }

    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                // Authentication passed...
                $user = auth()->user();
                $user->device_token = $request->input('device_token', '');

                $user->save();
                return $this->sendResponse($user, 'User retrieved successfully');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }
    }

    public function loginDriver(Request $request)
    {
        try {

            $this->validate($request, [
                'phone' => 'required',
                'password' => 'required',
            ]);
            $verificar = User::where('phone', '=', $request->input('phone'))->exists();
            if ($verificar) {
                $userFind = User::where('phone', '=', $request->input('phone'))->get();

                if (auth()->attempt(['email' => $userFind[0]['email'], 'password' => $request->input('password')])) {
                    $user = auth()->user();
                    $user->device_token = $request->input('device_token', '');

                    $user->save();
                    return $this->sendResponse($user, 'User retrieved successfully');
                } else {
                    return $this->sendResponse([], 'Usuario no encontrado');
                }
            } else {
                return $this->sendResponse([], 'Usuario no encontrado');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return
     */
    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|unique:users|email',
                'password' => 'required',
            ]);
            $user = new User;
            $user->name = $request->input('name');
            $user->url_image_firebase = $request->input('url_image_firebase');
            $user->rtn_user = $request->input('rtn_user');

            $user->email = $request->input('email');
            $user->device_token = $request->input('device_token', '');
            $user->password = Hash::make($request->input('password'));
            $user->api_token = str_random(60);
            $user->save();

            $defaultRoles = $this->roleRepository->findByField('default', '1');
            $defaultRoles = $defaultRoles->pluck('name')->toArray();
            $user->assignRole($defaultRoles);

            if (copy(public_path('images/avatar_default.png'), public_path('images/avatar_default_temp.png'))) {
                $user->addMedia(public_path('images/avatar_default_temp.png'))
                    ->withCustomProperties(['uuid' => bcrypt(str_random())])
                    ->toMediaCollection('avatar');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    public function logout(Request $request)
    {
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();
        if (!$user) {
            return $this->sendError('User not found', 401);
        }
        try {
            auth()->logout();
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }
        return $this->sendResponse($user['name'], 'User logout successfully');
    }

    public function user(Request $request)
    {
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();

        if (!$user) {
            return $this->sendError('User not found', 401);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    public function settings(Request $request)
    {
        $settings = setting()->all();
        $settings = array_intersect_key(
            $settings,
            [
                'default_tax' => '',
                'default_currency' => '',
                'default_currency_decimal_digits' => '',
                'app_name' => '',
                'currency_right' => '',
                'enable_paypal' => '',
                'enable_stripe' => '',
                'enable_razorpay' => '',
                'main_color' => '',
                'main_dark_color' => '',
                'second_color' => '',
                'second_dark_color' => '',
                'accent_color' => '',
                'accent_dark_color' => '',
                'scaffold_dark_color' => '',
                'scaffold_color' => '',
                'google_maps_key' => '',
                'mobile_language' => '',
                'app_version' => '',
                'enable_version' => '',
                'distance_unit' => '',
                'home_section_1' => '',
                'home_section_2' => '',
                'home_section_3' => '',
                'home_section_4' => '',
                'home_section_5' => '',
                'home_section_6' => '',
                'home_section_7' => '',
                'home_section_8' => '',
                'home_section_9' => '',
                'home_section_10' => '',
                'home_section_11' => '',
                'home_section_12' => '',
                'enable_pixelpay' => '',
                'pixelpay_dominio' => '',
                'number_for_whatsapp' => '',
                'initial_message_for_whatsapp' => '',
                'pay_on_line' => '',
                'enable_pay_on_line' => '',
                'hora_cierre_agenda' => '',
                'minutos_cierre_agenda' => '',
                'minutos_apertura_agenda' => '',
                'hora_apertura_agenda' => '',
                'enable_agendar' => '',
                'enable_fac' => '',
                'enable_fac_3d_secure' => '',
                'enable_zone_maps' => '',
                'enable_mandaditos' => '',
                'initial_greeting' => '',
                'message_home' => '',
                'message_markets_closed' => '',
                'message_home_markets' => '',
                'app_off_line' => '',
                'message_app_off_line' => '',

            ]
        );

        if (!$settings) {
            return $this->sendError('Settings not found', 401);
        }

        $sistemaInterfaz = DB::table('sistema_interfaz')->get();

        // * Identificadores para los tipos de interfaz
        //? 438585712093 = Rutas normales (No se modifica el app)
        //? 098085908235 = muestra directamente el listado de establecimientos, sin las categorias de establecimientos
        //? 440128575066 = Redirige al unico establecimiento que hay registro en el app
        //? 607568578007 = Muestra la categorias de establecimientos sin la opcion de los mandaditos

        if (isset($sistemaInterfaz[0])) {
            $settings['codigo_interfaz'] = $sistemaInterfaz[0]->codigo;
            $settings['enable_mandaditos'] = '0';

            switch ($sistemaInterfaz[0]->codigo) {
                case "438585712093":
                    $settings['direccion_home'] = '/HomeCategories';
                    $settings['enable_mandaditos'] = '1';
                    break;
                case "098085908235":

                    $settings['direccion_home'] = '/HomeMarkets';
                    $idCategoria = DB::table('fields')->get(['id'])->first()->id;

                    $settings['codigo_categoria'] = $idCategoria;

                    break;
                case "440128575066":


                    $datosEstablecimientos = DB::table('markets')->limit(1)->get(['id', 'type_market_id']);
                    // * Tipos de establecimientos
                    // ? Normal = 1
                    // ? Tienda de conveniencia = 2
                    // ? Supermercado = 3
                    
                    // * Rutas para cada tipo de establecimiento
                    // '/Details'
                    // '/DetailsConvenienceStore'
                    // '/DetailsSupermarket'
                    switch ((string) $datosEstablecimientos[0]->type_market_id) {
                        case "1":
                            $settings['direccion_home'] = '/Details';
                            break;
                        case "2":
                            $settings['direccion_home'] = '/DetailsConvenienceStore';
                            break;
                        case "3":
                            $settings['direccion_home'] = '/DetailsSupermarket';
                            break;
                    }


                    $settings['codigo_establecimiento'] = $datosEstablecimientos[0]->id;
                    break;
                case "607568578007":
                    $settings['direccion_home'] = '/HomeCategories';

                    break;

                default:
                    $settings['direccion_home'] = '/HomeCategories';
                    $settings['enable_mandaditos'] = '1';
            }
        } else {
            $settings['codigo_interfaz'] = '438585712093';
        }


        // $settings
        return $this->sendResponse($settings, 'Settings retrieved successfully');
    }

    public function encargos_settings(Request $request)
    {
        $encargo_setting = DB::table('encargos_settings')->get();

        $settnigEnviados['estado'] = 200;
        $settnigEnviados['data'] = $encargo_setting->where('id', 1)->first();
        $settnigEnviados['enviado'] = 'Sin errores';
        return $settnigEnviados;
    }
    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param Request $request
     *
     */
    public function update($id, Request $request)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendResponse([
                'error' => true,
                'code' => 404,
            ], 'User not found');
        }
        $input = $request->except(['password', 'api_token']);
        try {
            if ($request->has('device_token')) {
                $user = $this->userRepository->update($request->only('device_token'), $id);
            } else {
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
                $user = $this->userRepository->update($input, $id);

                foreach (getCustomFieldsValues($customFields, $request) as $value) {
                    $user->customFieldsValues()
                        ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
                }
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage(), 401);
        }

        return $this->sendResponse($user, __('lang.updated_successfully', ['operator' => __('lang.user')]));
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return $this->sendResponse(true, 'Reset link was sent successfully');
        } else {
            return $this->sendError('Reset link not sent', 401);
        }
    }

    public function loginWithFacebook(Request $request)
    {

        try {

            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $Verificar = User::where('email', $request['email'])->exists();
            if ($Verificar) {
                $userFind = User::where('email', $request['email'])->first();
                $user = $this->userRepository->findWithoutFail($userFind->id);

                $user->name = $request['name'];
                $user->device_token = $request->input('device_token', '');
                $user->save();
                return $this->sendResponse($user, 'User retrieved successfully');
            } else {
                $user = new User;
                $user->name = $request->input('name');
                $user->url_image_firebase = $request->input('url_image_firebase');
                $user->rtn_user = $request->input('rtn_user');

                $user->email = $request->input('email');
                $user->device_token = $request->input('device_token', '');
                $user->password = Hash::make($request->input('password'));
                $user->api_token = str_random(60);
                $user->save();

                $defaultRoles = $this->roleRepository->findByField('default', '1');
                $defaultRoles = $defaultRoles->pluck('name')->toArray();
                $user->assignRole($defaultRoles);

                if (copy(public_path('images/avatar_default.png'), public_path('images/avatar_default_temp.png'))) {
                    $user->addMedia(public_path('images/avatar_default_temp.png'))
                        ->withCustomProperties(['uuid' => bcrypt(str_random())])
                        ->toMediaCollection('avatar');
                }
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return
     */
    public function registerWithFacebook(Request $request)
    {

        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $Verificar = User::where('email', $request['email'])->exists();
            if ($Verificar) {
                $userFind = User::where('email', $request['email'])->first();
                $user = $this->userRepository->findWithoutFail($userFind->id);

                $user->name = $request['name'];
                $user->device_token = $request->input('device_token', '');
                $user->save();
                return $this->sendResponse($user, 'User retrieved successfully');
            } else {
                $user = new User;
                $user->name = $request->input('name');
                $user->url_image_firebase = $request->input('url_image_firebase');
                $user->rtn_user = $request->input('rtn_user');

                $user->email = $request->input('email');
                $user->device_token = $request->input('device_token', '');
                $user->password = Hash::make($request->input('password'));
                $user->api_token = str_random(60);
                $user->save();

                $defaultRoles = $this->roleRepository->findByField('default', '1');
                $defaultRoles = $defaultRoles->pluck('name')->toArray();
                $user->assignRole($defaultRoles);

                if (copy(public_path('images/avatar_default.png'), public_path('images/avatar_default_temp.png'))) {
                    $user->addMedia(public_path('images/avatar_default_temp.png'))
                        ->withCustomProperties(['uuid' => bcrypt(str_random())])
                        ->toMediaCollection('avatar');
                }
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    public function loginWithGoogle(Request $request)
    {

        try {

            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $Verificar = User::where('email', $request['email'])->exists();
            if ($Verificar) {
                $userFind = User::where('email', $request['email'])->first();
                $user = $this->userRepository->findWithoutFail($userFind->id);

                $user->name = $request['name'];
                // $user = $userFind;
                $user->device_token = $request->input('device_token', '');
                $user->save();
                return $this->sendResponse($user, 'User retrieved successfully');
            } else {
                $user = new User;
                $user->name = $request->input('name');
                $user->url_image_firebase = $request->input('url_image_firebase');
                $user->rtn_user = $request->input('rtn_user');

                $user->email = $request->input('email');
                $user->device_token = $request->input('device_token', '');
                $user->password = Hash::make($request->input('password'));
                $user->api_token = str_random(60);
                $user->save();

                $defaultRoles = $this->roleRepository->findByField('default', '1');
                $defaultRoles = $defaultRoles->pluck('name')->toArray();
                $user->assignRole($defaultRoles);

                if (copy(public_path('images/avatar_default.png'), public_path('images/avatar_default_temp.png'))) {
                    $user->addMedia(public_path('images/avatar_default_temp.png'))
                        ->withCustomProperties(['uuid' => bcrypt(str_random())])
                        ->toMediaCollection('avatar');
                }
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return
     */
    public function registerWithGoogle(Request $request)
    {

        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);
            $Verificar = User::where('email', $request['email'])->exists();
            if ($Verificar) {
                $userFind = User::where('email', $request['email'])->first();
                $user = $this->userRepository->findWithoutFail($userFind->id);

                $user->name = $request['name'];
                $user->device_token = $request->input('device_token', '');
                $user->save();
                return $this->sendResponse($user, 'User retrieved successfully');
            } else {
                $user = new User;
                $user->name = $request->input('name');
                $user->url_image_firebase = $request->input('url_image_firebase');
                $user->rtn_user = $request->input('rtn_user');

                $user->email = $request->input('email');
                $user->device_token = $request->input('device_token', '');
                $user->password = Hash::make($request->input('password'));
                $user->api_token = str_random(60);
                $user->save();

                $defaultRoles = $this->roleRepository->findByField('default', '1');
                $defaultRoles = $defaultRoles->pluck('name')->toArray();
                $user->assignRole($defaultRoles);

                if (copy(public_path('images/avatar_default.png'), public_path('images/avatar_default_temp.png'))) {
                    $user->addMedia(public_path('images/avatar_default_temp.png'))
                        ->withCustomProperties(['uuid' => bcrypt(str_random())])
                        ->toMediaCollection('avatar');
                }
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }
}
