<?php

// このコントローラーは、アプリケーションのバージョン1（V1）のAPIリクエストを処理する責任があります。
// It handles product-related operations such as fetching popular, recommended, and drink products.
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;

class ProductController extends Controller
{

    // 人気商品を取得するエンドポイント
    // Endpoint to get popular products
    public function get_popular_products(Request $request)
    {

        $list = Food::where('type_id', 2)->take(10)->get();

        foreach ($list as $item) {
            $item['description'] = strip_tags($item['description']);
            $item['description'] = $Content = preg_replace("/&#?[a-z0-9]+;/i", " ", $item['description']);
            unset($item['selected_people']);
            unset($item['people']);
        }

        $data =  [
            'total_size' => $list->count(),
            'type_id' => 2,
            'offset' => 0,
            'products' => $list
        ];

        return response()->json($data, 200);
    }

    // おすすめ商品を取得するエンドポイント
    // Endpoint to get recommended products
    public function get_recommended_products(Request $request)
    {
        $list = Food::where('type_id', 3)->take(10)->get();

        foreach ($list as $item) {
            $item['description'] = strip_tags($item['description']);
            $item['description'] = $Content = preg_replace("/&#?[a-z0-9]+;/i", " ", $item['description']);
            unset($item['selected_people']);
            unset($item['people']);
        }

        $data =  [
            'total_size' => $list->count(),
            'type_id' => 3,
            'offset' => 0,
            'products' => $list
        ];

        return response()->json($data, 200);
    }


    // テスト用のおすすめ商品を取得するエンドポイント
    // Endpoint to get recommended products for testing
    public function test_get_recommended_products(Request $request)
    {

        $list = Food::skip(5)->take(2)->get();

        foreach ($list as $item) {
            $item['description'] = strip_tags($item['description']);
            $item['description'] = $Content = preg_replace("/&#?[a-z0-9]+;/i", " ", $item['description']);
        }

        $data =  [
            'total_size' => $list->count(),
            'limit' => 5,
            'offset' => 0,
            'products' => $list
        ];
        return response()->json($data, 200);
        // return json_decode($list);
    }

    // ドリンクを取得するエンドポイント
    // Endpoint to get drinks
    public function get_drinks(Request $request)
    {
        $list = Food::where('type_id', 4)->take(10)->orderBy('created_at', 'DESC')->get();

        foreach ($list as $item) {
            $item['description'] = strip_tags($item['description']);
            $item['description'] = $Content = preg_replace("/&#?[a-z0-9]+;/i", " ", $item['description']);
            unset($item['selected_people']);
            unset($item['people']);
        }

        $data =  [
            'total_size' => $list->count(),
            'type_id' => 4,
            'offset' => 0,
            'products' => $list
        ];

        return response()->json($data, 200);
    }
}
