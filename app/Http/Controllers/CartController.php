<?php

namespace App\Http\Controllers;

use Cart;
use App\Models\Sku;
use App\Models\lista_desejos\ListaDesejo;
use Illuminate\Support\Facades\Log;
use App\Services\PDFService;
use Auth;
use App\Http\Resources\CartResource;
use Illuminate\Support\Arr;

class CartController extends Controller
{
    public $cart = null;

    public function init($userCode)
    {
        return $this->cart = Cart::session($userCode);
    }

    public function generateBudget()
    {
        $user = Auth::user();
        $cart = $this->init($user->code);
        $data = [
            'user' => $user,
            'cart' => $cart->getContent()
        ];
        return PDFService::stream('pages.cart.budget-pdf', $data, "orçamento padrao color.pdf");
    }

    public static function getCartWeight($cart)
    {
        $content = $cart->getContent();
        $total = 0.0;
        foreach ($content as $item) {
            $res = array_sum(array_pluck($item->attributes["items"], "sku.weight"));
            $total += $res;
        }
        return $total;
    }

    public function prepareForCart($cart)
    {
        $content = $cart->getContent();
        //$content = (new CartResource($content))->collection($content);
        $content = CartResource::collection($content);
        //dd($content->toArray(request()));

        return $content;
    }

    public function index()
    {
        $user = Auth::user();
        $cart = $this->init($user->code);
        $data = [
            'user' => $user,
            'cart' => $this->prepareForCart($cart),
            'not_empty' => (bool) count($cart->getContent()),
        ];
        return view('pages.cart.index', $data);
    }

    public function clear()
    {
        $user = \Auth::user();
        $cart = $this->init($user->code);
        $cart->clear();

        return redirect(route('cart.index'));
    }

    public function remove($id)
    {
        $user = \Auth::user();
        $cart = $this->init($user->code);
        $data = [
            'user' => $user,
            'cart' => Cart::get($id),
            'not_empty' => (bool) count($cart->getContent()),
        ];
        $this->saveListaDesejos($data,$user,$id);
        $cart->remove($id);
        return redirect(route('cart.index'));
    }

    public function saveListaDesejos($data,$user,$id){
        $listaDesejos = new ListaDesejo();
        $listaDesejos->id_user = $user->id;
        $listaDesejos->id_cart = $id;
        $listaDesejos->cart = json_encode($data['cart']);
        $listaDesejos->save();
    }

    public function calculate_preparation_time($data)
    {
        $longest_sku_time = max(Arr::pluck($data['order']['items'], 'sku.prepared_in'));
        return $longest_sku_time + @$data['order']['finishes']['additional_time'];
    }

    public static function longest_sku_preparation_time($cart)
    {
        $content = $cart->getContent();
        return (int) max(Arr::pluck($content, "attributes.prepared_in"));
    }

    public function sku_to_cart($skus, $group)
    {
        $result = [];
        $cart =  [
            "id"        => $group,
            "quantity"  => $skus["order"]["qty"],
            "price"     => $skus["order"]["total"],
            "name"      => $skus["order"]["data"]["ref_name"],
            "attributes" => [
                "data" => $skus["order"]["data"],
                "prepared_in" => $this->calculate_preparation_time($skus),
                "date" => $skus["order"]["date"],
                "time" => $skus["order"]["time"],
                "total" => $skus["order"]["total"],
                "additional"  => $skus["order"]["additional"],
                "finishes"    => $skus["order"]["finishes"],
                "items"       => $skus["order"]["items"]
            ]
        ];
        //return $skus["order"]["finishes"];
        for ($i = 0; $i < count($cart["attributes"]["items"]); $i++) {
            $_sku = Sku::find($cart["attributes"]["items"][$i]["sku"]['id']);
            $cart["attributes"]["items"][$i]["product"] = $_sku->product->load("files")->toArray();
        }
        return $cart;
    }

    public function edit($id)
    {
        $user = \Auth::user();
        $view = \App::make(CheckoutController::class);
        $sku_id = $this->init($user->code)->getContent()[$id]['attributes']['id'];
        $sku = Sku::find($sku_id);

        return $view->upload($sku->product, null);
    }

    public function setCookie($skus)
    {
        $errorMessage = ' Atributo inexistente para este SKU';

        foreach ($skus as $key => $sku) {
            $response = $this->$attributesVerify($sku);
            $items[$key] = ($response == true) ? $sku : $response . $errorMessage . $sku['name'];
        }

        $values = cookie('cardItems', $items);

        return response('')->cookie($values);
    }

    public function attributesVerify($sku)
    {
        $attributes = Sku::find($sku['id'], ['attributes']);
        $diff = array_diff($attributes, $sku['attributes']);

        if ($diff == null) {
            return true;
        } else {
            return $diff;
        }
    }
}
