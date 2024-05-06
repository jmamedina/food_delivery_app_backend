<?php
// このコントローラーは、アプリケーションのバージョン1（V1）のAPIリクエストを処理する責任があります。
// It deals with customer-related operations like managing addresses and retrieving user information.
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{

    // ユーザーのアドレスリストを返すエンドポイント
    // Endpoint to return the list of addresses for a user
    public function address_list(Request $request)
    {
        return response()->json(CustomerAddress::where('user_id', $request->user()->id)->latest()->get(), 200);
    }

    // ユーザーの情報を返すエンドポイント
    // Endpoint to return user information
    public function info(Request $request)
    {
        $data = $request->user();

        $data['order_count'] = 0; // Orders count is temporarily disabled
        $data['member_since_days'] = (int)$request->user()->created_at->diffInDays();
        //unset($data['orders']); // Temporarily disabled orders data
        return response()->json($data, 200);
    }

    // 新しいアドレスを追加するエンドポイント
    // Endpoint to add a new address
    public function add_new_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => "Error with the address"], 403);
        }

        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->insert($address);
        return response()->json(['message' => trans('messages.successfully_added')], 200);
    }

    // アドレスを更新するエンドポイント
    // Endpoint to update an address
    public function update_address(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $address = [
            'user_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'zone_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];
        DB::table('customer_addresses')->where('user_id', $request->user()->id)->update($address);
        return response()->json(['message' => trans('messages.updated_successfully'), 'zone_id' => $zone->id], 200);
    }
}
