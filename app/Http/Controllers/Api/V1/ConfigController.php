<?php

// このコントローラーは、アプリケーションのバージョン1（V1）のAPIリクエストを処理する責任があります。
// It deals with configurations related to geocoding and zone information.
namespace App\Http\Controllers\Api\V1;

use App\Models\Zone;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\CentralLogics\Helpers;
use Grimzy\LaravelMysqlSpatial\Types\Point;


class ConfigController extends Controller
{
    // Google Maps APIからジオコードデータを取得するエンドポイント
    // Endpoint to fetch geocode data from Google Maps API
    public function geocode_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        // Google Maps APIからジオコードデータを取得
        // Fetch geocode data from Google Maps API
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $request->lat . ',' . $request->lng . '&key=' . "YOUR_API_KEY");
        return $response->json();
    }

    // 座標に基づいてゾーンを取得するエンドポイント
    // Endpoint to get zone based on coordinates
    public function get_zone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        // 緯度と経度からポイントオブジェクトを作成する
        // Creating a point object from latitude and longitude
        $point = new Point($request->lat, $request->lng);

        // 指定された座標を含むゾーンを取得
        // Fetching zones containing the given coordinates
        $zones = Zone::contains('coordinates', $point)->latest()->get();

        // デモ目的のためにデフォルトのゾーンを返す
        // Returning a default zone for demonstration purpose
        return response()->json(['zone_id' => 1], 200);
    }
}
