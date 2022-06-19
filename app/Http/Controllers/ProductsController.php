<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use DB;
use Auth;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $looking_for = @$request["procurando"] ? $request["procurando"] : "";
        $products = Product::where("name", "like", "%" . $looking_for . "%")->orderBy('name')->with(['files', 'minPrice'])->get();
        //Log::debug('chegou busca');
        $data = ['products' => $products];
        return view('pages.products.index', $data);
    }

    public function index2(Request $request)
    {
        $looking_for = @$request["procurando"] ? $request["procurando"] : "";
        $products = Product::where("name", "like", "%" . $looking_for . "%")->orderBy('name')->with(['files', 'minPrice'])->paginate(20);
        //Log::debug('chegou lista todos');
        //Log::debug(json_encode($products));
        $data = ['products' => $products];
        return view('pages.products.products_all', $data);
    }

    public function index3(Request $request)
    {
        $looking_for = @$request["procurando"] ? $request["procurando"] : "";
        $products = Product::where("name", "like", "%" . $looking_for . "%")->orderBy('name')->with(['files', 'minPrice'])->get();
        //Log::debug('chegou busca');
        $data = ['products' => $products];
        return view('pages.products.products_lancamento', $data);
    }

    public function index4(Request $request)
    {
        $looking_for = @$request["procurando"] ? $request["procurando"] : "";
        $products = Product::where("name", "like", "%" . $looking_for . "%")->orderBy('name')->with(['files', 'minPrice'])->get();
        $data = ['products' => $products];
        return view('pages.products.products_by_category', $data);
    }

    public function index5(Request $request)
    {
        $looking_for = @$request["procurando"] ? $request["procurando"] : "";
        $products = Product::where("name", "like", "%" . $looking_for . "%")->orderBy('name')->with(['files', 'minPrice'])->get();
        $data = ['products' => $products];
        return view('pages.products.products_category1', $data);
    }

    public function department($department, Request $request)
    {
        if (!in_array($department, ["comunicacao-visual", "impressao-offset", "impressao-digital", "todos"]))
            abort(404);
        $looking_for = @$request["procurando"] ? $request["procurando"] : "";
        $department = str_replace("-", " ", $department);
        $products = Product::where("id", ">", 0);
        if ($department != "todos")
            $products = $products->where("department", $department)->where("name", "like", "%" . $looking_for . "%");
        $products = $products->orderBy('name')->with(['files', 'minPrice'])->get();
        return view('pages.products.index', compact("products", "department"));
    }

    public function view(Request $request, Product $product)
    {
        $product->load(["files", "faqs"]);
        $title = $product->name;
        $templates = $product->files(['pdf', 'ai', 'psd', 'cdr'])->get();
        return view('pages.products.view', compact('product', 'title', 'templates'));
    }

    public function create_avaliation(Product $product, Request $request)
    {
        $data = $request->all();
        unset($data["loading"]);
        $data["approved"] = false;
        $product->avaliations()->create($data);
        return response()->json(['success' => true, 'message' => null]);
    }

    public function create_faq(Product $product, Request $request)
    {
        $data = $request->all();
        $data["approved"] = false;
        $product->faqs()->create([
            "author" => Auth::user()->name,
            "product_id" => $product->id,
            "ask" => $data["question"],
        ]);
        return response()->json(['success' => true, 'message' => null]);
    }

    public function ShowDetail(Request $request)
    {

        $categories = Category::with(['products'])->find([1, 2, 3]);
        $categories->pluck('products');

        $product = Product::with(['categories'])->get();
        $data = ['product' => $product];

        return view('pages.products.detalhe', $data);
    }



    public function list($amount = null, Request $request)
    {
        $products = Product::orderBy('name')->where("status", true)->with(['files', 'minPrice'])->take($amount);
        //Log::debug('chegou list');
        //Log::debug(json_encode($products));
        if ($request->query('search')) {
            $products->where("name", "like", "%" . $request->query('search') . "%");
        }
        //Log::debug('chegou');
        return $products->get();
    }

    public function storeProduct($data)
    {

        try {

            $products = json_decode($data, true);
            foreach ($products as $product) {


                $product = Product::create($product);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => null]);
        } catch (\Exception $e) {

            @DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }
}
