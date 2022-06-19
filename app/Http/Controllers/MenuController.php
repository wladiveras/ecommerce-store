<?php

namespace App\Http\Controllers;

use App\Models\{Product, Category};
use DB;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{

    public function get_menu_options()
    {
        return Cache::remember('menu.options', 1440, function () {
            $options = [];
            // $options[] = [
            //     "name"   =>  "Mais Vendidos",
            //     "value"  =>  "mais_vendidos",
            //     "values" =>  self::get_menu_values("mais_vendidos")
            // ];
            $without_categories = self::get_menu_values("sem_categoria");
            $categories = Category::get();
            foreach ($categories as $category) {
                $products = $category->products;
                if ($products->count() > 1) {
                    $options[] = [
                        "name"   =>  $category->name,
                        "value"  =>  $category->name,
                        "values" =>  self::get_menu_values("category", $products)
                    ];
                }
            }
            if (config("show_menu_withou_categories")) {
                if ($without_categories) {
                    $options[] = [
                        "name"   =>  "Sem Categorias",
                        "value"  =>  "sem_categoria",
                        "values" =>  $without_categories
                    ];
                }
            }
            return ["options" => $options];
        });
    }

    private static function get_category_products($products)
    {
        $result = [];
        foreach ($products as $product) {
            $result[] = [
                "name" => $product->name,
                "url"  => route("product.view", ["product" => $product->slug]),
                "department" => self::get_department($product->department)
            ];
        }
        return array_chunk($result, (count($result) >= 100 ? 5 : 10));
    }

    public static function get_menu_values($type, $products = null)
    {
        switch ($type) {
            case "mais_vendidos":
                return self::get_most_selled();
                break;
            case "sem_categoria":
                return self::get_without_categories();
            default:
                return self::get_category_products($products);
                break;
        }
    }

    private static function get_without_categories()
    {
        $with_categories = DB::connection("dashboard_server")->table("model_categories")->where("model_type", "product")->pluck("model_id")->toArray();
        $values = Product::whereNotIn("id", $with_categories)->get();
        $result = [];
        foreach ($values as $product) {
            $result[] = [
                "name" => $product->name,
                "url"  => route("product.view", ["product" => $product->slug]),
                "department" => self::get_department($product->department)
            ];
        }
        return array_chunk($result, (count($result) >= 100 ? 10 : 5));
    }

    public static function get_most_selled($limit = 20, $chunked = true)
    {
        return [];

        return Cache::remember('menu.products.most_sold', 1440, function () use ($limit) {
            $result = [];
            $ids =  DB::connection("dashboard_server")->table("orders")->join("order_sku", "order_sku.order_id", "orders.id")
                ->join("skus", "skus.id", "order_sku.sku_id")
                ->join("products", "products.id", "order_sku.product_id")
                ->join("status", "status.id", "=", "orders.status_id")
                ->where("status.value", "!=", 'canceled')
                ->groupBy("order_sku.product_id")
                ->limit($limit)
                ->orderBy("qty", "desc")
                ->select(DB::raw("products.id as product_id,count(*) as qty"))
                ->pluck("product_id")
                ->toArray();
            foreach ($ids as $id) {
                $product = Product::findOrFail($id);
                $result[] = [
                    "product" => $product,
                    "name" => $product->name,
                    "url"  => route("product.view", ["product" => $product->slug]),
                    "department" => self::get_department($product->department)
                ];
            }
            return array_chunk($result, 5);
        });
    }

    public static function get_most_selled_static($limit = 20)
    {
        return Cache::remember('menu.products.most_sold_static', 1440, function () use ($limit) {
            $result = [];
            $ids =  DB::connection("dashboard_server")->table("orders")->join("order_sku", "order_sku.order_id", "orders.id")
                ->join("skus", "skus.id", "order_sku.sku_id")
                ->join("products", "products.id", "order_sku.product_id")
                ->join("status", "status.id", "=", "orders.status_id")
                ->where("status.value", "!=", 'canceled')
                ->groupBy("order_sku.product_id")
                ->limit($limit)
                ->orderBy("qty", "desc")
                ->select(DB::raw("products.id as product_id,count(*) as qty"))
                ->pluck("product_id")
                ->toArray();
            foreach ($ids as $id) {
                $product = Product::findOrFail($id);
                $result[] = [
                    "product" => $product,
                    "name" => $product->name,
                    "url"  => route("product.view", ["product" => $product->slug]),
                    "department" => self::get_department($product->department)
                ];
            }
            return $result;
        });
    }

    public static function get_department($department)
    {
        switch ($department) {
            case "impressao offset":
                return "Impressão Offset";
            case "impressao digital":
                return "Impressão Digital";
                break;
            case "comunicacao visual":
                return "Comunicação Visual";
                break;
        }
    }
}
