<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Template;
use Illuminate\Support\Facades\Log;

class TemplateController extends Controller
{
    public static function current()
    {
        $data = Template::where("current", true)->firstOrFail();
        return $data;
    }

    public function get_products(Request $request)
    {
        $ids = $request->all();
        $result = Product::where("status", true)->whereIn("id", $ids)->orderBy("id","asc")->get();
        debug_log("Teste", "resultado", [$result]);
        
        $products = [];
        foreach ($result as $p) {
            $file = $p->files->first();
            $view_price = $p->view_price;
            $products[] = [
                "name"  => $p->name,
                "image" => $file ? $file->raw_url : null,
                "route" => route('product.view', ['slug' => $p->slug]),
                "department" => $p->department,
                "viewPrice" => @$view_price ? [
                    "price" => @$view_price["price"],
                    "promo_type"  => @$view_price["promo_type"],
                    "promo_value" => @$view_price["promo_value"],
                    "rule"  => @$view_price["rule"],
                ] : null
            ];
        }
        return response()->json(['success' => true, 'data' => $products]);
    }
}
