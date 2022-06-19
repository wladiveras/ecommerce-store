<?php

namespace App\Http\Controllers;

use App\Models\SpecialOrder;
use App\Models\StatusSpecialOrder;
use App\Http\Requests\SpecialOrdersRequest;
use App\Http\Requests\UpdateSpecialOrdersRequest;
use Illuminate\Http\Request;
use App\Services\AlertService;
use DB;
use Auth;

class SpecialOrderController extends Controller
{
    public function index()
    {
        $special_orders = $this->getSpecialOrders();
        $logged = Auth::check();

        $data = [
            "logged" => $logged,
            "is_admin" => $logged ? (@Auth::user()->ticketit_agent ? true : false) : false,
            "routes" => [
                "create" => route("pedidos_especiais.create"),
                "list"   => ($logged ? route("pedidos_especiais.list") : null)
            ],
            "special_orders" => [
                "show_list_link" => ($special_orders->count() > 0 ? ($logged ? true : false) : false)
            ],
            "text_btn" => ($logged ? "SOLITICAR UM PROJETO ESPECIAL" : "ENTRE PARA TER ACESSO AOS PROJETOS ESPECIAIS")
        ];
        return view("pages.special_orders.index", compact("data"));
    }

    public function list()
    {
        $data = [
            "is_admin" => (@Auth::user()->ticketit_agent ? true : false),
            "routes" => [
                "index" => route("pedidos_especiais.index"),
                "show"  => route("pedidos_especiais.show", ["id" => "%%"]),
                "get_items" => route("pedidos_especiais.get_items_list"),
            ]
        ];
        return view("pages.special_orders.list", compact("data"));
    }

    public function get_items_list(Request $request)
    {
        $perpage = 8;
        $data = $request->all();
        $special_orders = $this->getSpecialOrders();
        $items = $special_orders->select(DB::raw("special_orders.id, special_orders.title, status.value as status_value, status.name as status, 
        DATE_FORMAT(special_orders.created_at, '%d/%m/%Y') as formated_date, users.name as author, users.user_name as username , users.email as user_email,
        users.id as user_id, users.wpp_phone, DATE_FORMAT(special_orders.target_date,'%d/%m/%Y') as target_date"))
            ->where(function ($query) use ($data) {
                $query->OrWhere("special_orders.id", "like", "%" . $data["filter"] . "%")
                    ->OrWhere("special_orders.title", "like", "%" . $data["filter"] . "%")
                    ->OrWhere("users.email", "like", "%" . $data["filter"] . "%")
                    ->OrWhere("special_orders.extra", "like", "%" . $data["filter"] . "%")
                    ->OrWhere("special_orders.description", "like", "%" . $data["filter"] . "%");
            });
        if ($data["status"] != "all")  $items = $items->Where("special_orders.status_id", StatusSpecialOrder::where("value", $data["status"])->firstOrFail()->id);
        $items = $items->paginate($perpage, $data["page"]);
        return response()->json(['success' => true, "data" => $items]);
    }

    private function getSpecialOrders()
    {
        $special_orders = SpecialOrder::where("special_orders.id", ">", 0)
            ->join("users", "users.id", "=", "special_orders.user_id")
            ->join("status", "status.id", "=", "special_orders.status_id")
            ->orderBy("special_orders.id", "desc");
        return $special_orders;
    }

    public function store(SpecialOrdersRequest $request)
    {
        if (@Auth::user()->ticketit_agent) abort(404);
        $data = $request->all();
     
        $specialOrder = SpecialOrder::create($data);
       
        $dash_request = new DashboardApi($specialOrder->id, "/send_email_new_special_order");

            $dash_request->make("new_special_order_email", []);
            $dash_request->send();

        AlertService::flash('success', "Projeto Especial <b>" . $data["title"] . "</b> enviado com sucesso !!!");
        return response()->json(['success' => true, 'message' => "Cadastrado com Sucesso", "data" => ["route" => route("pedidos_especiais.list")]]);
    }

    public function put($id, UpdateSpecialOrdersRequest $request)
    {
        $specialOrder = SpecialOrder::findOrFail($id);
        $data = $request->all();
        $extra = $specialOrder->extra;
        @$extra["hystory"][] = $data;
        $specialOrder->status_id = $data["status"]["id"];
        $specialOrder->extra = $extra;
        $specialOrder->save();
        AlertService::flash('success', "Projeto Especial <b>" . $specialOrder->title . "</b> atualizado com sucesso !!!");
    }

    public function delete(SpecialOrder $specialOrder)
    {
        $specialOrder->delete();
        return response()->json(['success' => true, 'message' => "Excluido com Sucesso"]);
    }

    public function show($id)
    {
        $order = SpecialOrder::findOrFail($id);
        $status = $order->status;
        $user = $order->user;
        $data = [
            "id" => $order->id,
            "is_admin" => (@Auth::user()->ticketit_agent ? true : false),
            "formated_id" => "#" . str_pad($order->id, '6', '0', STR_PAD_LEFT),
            "formated_created_at" => $order->created_at->format("d/m/Y - H:i:s"),
            "target_date" => date_format(date_create($order->target_date), 'd/m/Y'),
            "title" => $order->title,
            "description" => $order->description,
            "status_id" => $status->id,
            "status" => $status->name,
            "status_value" => $status->value,
            "hystory" => @$order->extra["hystory"] ? $order->extra["hystory"] : [],
            "status_list" => StatusSpecialOrder::select("id", "name")->get(),
            "user" => [
                "id" => str_pad($order->user_id, '6', '0', STR_PAD_LEFT),
                "username" => $user->user_name,
                "email" => $user->email,
                "phone" => $user->wpp_phone ?  $user->wpp_phone : null,
            ],
            "routes" => [
                "put" => route('pedidos_especiais.put', ['id' => $order->id])
            ]
        ];
        return view("pages.special_orders.show", compact("data"));
    }

    public function create()
    {
        if (@Auth::user()->ticketit_agent) abort(404);
        $data = [
            "routes" => [
                "upload_image" => route("api.upload_image"),
                "store"        => route("pedidos_especiais.store"),
            ]
        ];
        return view("pages.special_orders.create", compact("data"));
    }
}
