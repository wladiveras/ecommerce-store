<?php

namespace App\Http\Controllers;

use App\Models\{StoreConfig};
use Illuminate\Http\Request;

class StoreConfigController extends Controller
{
    public function index()
    {
        $storeConfigs = StoreConfig::orderBy('created_at', 'DESC')->get();
        return view('pages.configs.index', compact('storeConfigs'));
    }

    public function store(CreateStoreConfig $request)
    {
        try
        {
            $data = $request->all();
            $variations = [];
            $cont=0;
            // foreach( $data as $row)
            // {
            //     $variations['key'] = $row["name"];
             
            // }

            DB::beginTransaction();
            $data["type"] = json_encode($data["type"]);
            $data["variations"] = $variations;

           if($this->storeRemoteServer($data))     
           {
            $storeConfig = StoreConfig::create($data);
            DB::commit();

            $route = route('storeConfig.show',["code"=>$storeConfig->code]);
            $alert = AlertService::flash('success', '<b>Pronto!</b> Configurações Cadastrada com sucesso.');
            return response()->json(["success"=>true,"message"=>null,"data"=> ["route"=>$route]  ]);
           }
            return false;
        }
        catch(\Exception $e)
        {
            @DB::rollBack();
            return response()->json(["success"=>false,"message"=>$e->getMessage(),"data"=>null]);
        }
    }


    public function storeRemoteServer($data) 
    {
        try
        { 
            $dashboard_server = DB::connection('dashboard_server');

            $query = "'insert into store_config (`store_id`, `key`, `value`) values (?,?,?)', 
            array(".$data['store_id'].",'".$data['key']."', '".$data['values']."')";

            $dashboard_server->insert($query);
            DB::commit('dashboard_server');
            return true;
        }
        catch(\Exception $e)
        {
            @DB::rollBack();
            return response()->json(["success"=>false,"message"=>$e->getMessage(),"data"=>null]);
        }

    } 

}
