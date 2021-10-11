<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required|min:2|max:30',
            'last_name' => 'required|min:2|max:30',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|max:10',
            'phone' => 'max:15',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return ApiHelper::GenerateApiResponse(false, 422, $validator->errors()->first());
        }
        try {
            $data = [
                'first_name' => trim($request->first_name),
                'last_name' => trim($request->last_name),
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];
            $store = User::create($data);
            return $this->login($request);
        } catch (\Exception $e) {
            return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        if(empty($email) || empty($password)){
            return ApiHelper::GenerateApiResponse(false, 422, 'All fields are required');
        }

        $client = new Client(['base_uri' => 'http://localhost:9001',
            'defaults' => [
                'exceptions' => false
            ]]);
        try {
            return $client->post('/v1/oauth/token', [
                "form_params" => [
                    "client_secret" => "BUk7cWPf8MaQtedxqOqYaluPhy0ggFD8HciKoYTS",
                    "grant_type" => "password",
                    "client_id" => "2",
                    "username" => $email,
                    "password" => $password
                ]
            ]);
        } catch (BadResponseException $e) {
            return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->each(function($token){
                $token->delete();
            });
            return ApiHelper::GenerateApiResponse(true, 200, 'Logged out successfully');
        } catch (\Exception $e) {
            return ApiHelper::GenerateApiResponse(false, 400, $e->getMessage());
        }
    }
}
