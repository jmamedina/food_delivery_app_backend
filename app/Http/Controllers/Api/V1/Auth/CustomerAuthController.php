<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerAuthController extends Controller
{
    // Function for user login
    // ユーザーログイン用の関数
    public function login(Request $request)
    {
        // Validation rules for login credentials
        // ログイン資格情報の検証ルール
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        // If validation fails, return error response
        // 検証に失敗した場合、エラーレスポンスを返す
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        // Attempt to authenticate user with provided credentials
        // 提供された資格情報でユーザーの認証を試みる
        if (auth()->attempt($data)) {
            // Generate access token for authenticated user
            // 認証されたユーザーにアクセストークンを生成する
            $token = auth()->user()->createToken('RestaurantCustomerAuth')->accessToken;
            if (!auth()->user()->status) {
                // If user account is blocked, return error response
                // ユーザーアカウントがブロックされている場合、エラーレスポンスを返す
                $errors = [];
                array_push($errors, ['code' => 'auth-003', 'message' => trans('messages.your_account_is_blocked')]);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }

            return response()->json(['token' => $token, 'is_phone_verified' => auth()->user()->is_phone_verified], 200);
        } else {
            // If authentication fails, return unauthorized error response
            // 認証に失敗した場合、未認証のエラーレスポンスを返す
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }

    // Function for user registration
    // ユーザー登録用の関数
    public function register(Request $request)
    {
        // Validation rules for registration data
        // 登録データの検証ルール
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            //'l_name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
        ], [
            'f_name.required' => 'The first name field is required.',
            'phone.required' => 'The  phone field is required.',
        ]);

        // If validation fails, return error response
        // 検証に失敗した場合、エラーレスポンスを返す
        if ($validator->fails()) {
            return response()->json(['errors' => "Failed Validation"], 403);
        }
        // Create new user with provided registration data
        // 提供された登録データで新しいユーザーを作成する
        $user = User::create([
            'f_name' => $request->f_name,
            //'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        // Generate access token for newly registered user
        // 新規登録されたユーザーにアクセストークンを生成する
        $token = $user->createToken('RestaurantCustomerAuth')->accessToken;

        return response()->json(['token' => $token, 'is_phone_verified' => 0, 'phone_verify_end_url' => "api/v1/auth/verify-phone"], 200);
    }
}
