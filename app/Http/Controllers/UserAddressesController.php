<?php

namespace App\Http\Controllers;

use App\Models\UserAddresses;
use Illuminate\Http\Request;
use App\Http\Requests\UserAddressRequest as AddressRequest;
use Auth;
use App\Services\AlertService;
use App\Models\UserCard;
use ismaelgr\getnet\services\Payment;
use Illuminate\Support\Facades\Log;

class UserAddressesController extends Controller
{

    public function edit(Request $request)
    {
        $data = $request->only(["email"]);
        $user = Auth::user();
        $user->email = $data["email"];
        $user->save();
        AlertService::flash('success', "Dados de usuário alterado com sucesso");
        return redirect(route("user.data"));
    }
    public function index()
    {
        $addresses = UserAddresses::where('user_id', \Auth::user()->id)->latest()->get();
        Log::debug("addresses".json_encode($addresses));
        return response()->json(['success' => true, 'message' => null, 'data' => $addresses->groupBy('type')]);
    }

    public function index_view()
    {
        $user = \Auth::user();
        $addresses = UserAddresses::where('user_id', \Auth::user()->id)->latest()->get();
        $data = [
            'main' => $user->address,
            'user' => $user,
            'addresses' => $addresses,
            'cards' => UserCard::where("user_id",$user->id)->get()
        ];
        Log::debug("addresses".json_encode($addresses));
        $data['main']['name'] = "Minha Loja";
        return view('pages.user.addresses', $data);
    }

    public function create()
    {
        return view('pages.addresses.create');
    }

    public function store(AddressRequest $request, UserAddresses $address)
    {
        try {
            $data = $request->all();
            $address->fill(array_only($data, $address->fillable));
          
            $address->user_id = \Auth::user()->id;
            $address->data = array_except($data, $address->fillable);
            $address->save();
            return response()->json(['success' => true, 'message' => "Endereço cadastrado com sucesso.", 'data' => $address]);
        } catch (\Exception $e) {
            @\DB::rollback();

            return ['success' => false, 'message' => $e->getMessage(), 'data' => null];
        }
    }



    public function show(Request $request)
    {
        $addresses = UserAddresses::where('id', $request->id)->first();
        return view('pages.addresses.view', compact('UserAddresses'));
    }
    public function delete(Request $request, $id)
    {
        $address = UserAddresses::where('user_id', \Auth::user()->id)->where('id', $id)->first();
        if ($address) {
            $address->delete();
        }
        return [
            'success' => true,
            'message' => 'Endereço apagado com sucesso'
        ];
    }

    public function update(AddressRequest $request, $id)
    {
        try {

            $data = $request->all();
            $update = UserAddresses::where('user_id', \Auth::user()->id)->where('id', $id)->first();
            if ($update) {
                $update->fill($data);
                $update->data = array_except($data, $update->fillable);
                $update->save();
            }

            return response()->json(['success' => true, 'message' => "Endereço atualizado com sucesso"]);
        } catch (\Exception $e) {
            @DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $alert = "Endereço <strong> {$request->name} </strong> foi excluido com sucesso.";
            $deleted = UserAddresses::destroy($request->id);

            return response()->json(['success' => true, 'message' => null, 'data' => $alert]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function get_logo_url()
    {
        try {
            $logo = Auth::user()->reseller->logo_url;
            if (!$logo) throw new \Exception("Nao há logo");
            $options = "?height=180&format=webp";
            $logo = config("filesystems.disks.cdn.api_url") . "/" . $logo . $options;
            return response()->json(['success' => true, 'message' => null, 'data' => $logo]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function logo_upload(Request $request)
    {
        try {
            $reseller = Auth::user()->reseller;
            $file = $request->only('file')["file"];
            $path = $file->store(config("filesystems.disks.cdn.api_namespace"), "cdn");
            $reseller->logo_url = $path;
            $reseller->save();
            $options = "?height=180&format=webp";
            return response()->json(['success' => true, 'message' => null, 'data' => config("filesystems.disks.cdn.api_url") . "/" . $reseller->logo_url . $options]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function cardEdit(Request $request) 
    {
        $data = $request->all();
        if(@$data["id"]) return UserCard::where("id",$data["id"])->update(["billing_address" => json_encode($data["billing_address"])]);
    }

    public function cardDelete(Request $request) 
    {
        $data = $request->all();
        $card = UserCard::find($data["id"]);
        $payment = new Payment();
        $result = $payment->makePayment(["cardAction"=>"deleteCard","cardId"=>"b456acbd-3e4a-4bc2-b1a3-f097e546b40f","action"=>5]);
        $card->delete();
        return $result;
    }


    
}
