<?php

namespace App\Http\Controllers;

use App\Models\SupplyStore;
use Illuminate\Http\Request;

class SupplyStoreController extends Controller
{


    public function getAll()
    {
        $addresses = SupplyStore::orderBy('created_at', 'DESC')->get();

        return response()->json(['success' => true, 'message' => null, 'data' => $addresses]);
    }


    public function create()
    {
        return view('pages.addresses.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $created = SupplyStore::create($data);

            $alert ="Endereço cadastrado com sucesso.";

            return response()->json(['success' => true, 'message' => null, 'data' => $alert]);
        } catch (\Exception $e) {
            @DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }



    public function show(Request $request)
    {
        $addresses = SupplyStore::where('id', $request->id)->first();

        return view('pages.addresses.view', compact('addresses'));
    }



    public function update(Request $request)
    {
        try {

        $data = $request->all();
        $updated = SupplyStore::update($data->id,$data);
        $alert = " Categoria de produto atualizada com sucesso";

        return response()->json(['success' => true, 'message' => null, 'data' => $alert]);
    } catch (\Exception $e) {
        @DB::rollBack();

        return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $alert = "Endereço <strong> {$request->name} </strong> foi excluido com sucesso.";
            $deleted = SupplyStore::destroy($request->id);

            return response()->json(['success' => true, 'message' => null, 'data' => $alert]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }
}
