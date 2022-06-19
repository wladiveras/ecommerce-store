<?php

namespace App\Http\Controllers;

use App\Models\PricingRole;
use App\Models\Finish;
use App\Models\Sku;
use Auth;

class FinishesController extends Controller
{
    private $departments = ["IGNORE", "comunicacao visual", "impressao offset", "impressao digital"];
    private $price = null;

    private function get_department_id_by_name($dep_name)
    {
        return array_search($dep_name,  $this->departments);
    }

    private function get_finish_price($skuID, $finishID)
    {
        $prices = (array) ($this->finish->prices);

        $role = Auth::user()->reseller->pricing_role_id;


        if (isset($prices[$role])) {
            $this->price = ($prices ? (float) $prices[$role] : 0);
        } else {
            $role_ref_id = $this->getIdByRefId(1);
            return $this->price = ($prices ? (float) $prices[$role_ref_id] : 0);
        }
        $this->price;
    }

    private function getIdByRefId($id)
    {
        $finish = PricingRole::where("ref_id", $id)->first();
        return $finish->id;
    }

    private function setFinishes($skuID, $finishID){
        $this->sku = Sku::find($skuID);
        $this->finish = $this->sku->finishes()->where("finish_ref_id", $finishID)->first();
        $this->finishes = request('finishes') ? collect(request('finishes')) : collect([]);
    }

    private function checkDepartmentRules($department){
        return;
        switch($department){
            case 2: // offset
            break;
        }
    }

    public function calcFinishes($skuID, $finishID, $department, $amount, $countFinishes)
    {
        $this->setFinishes($skuID, $finishID);
        $department = $this->get_department_id_by_name($department);
        $this->get_finish_price($skuID, $finishID);
        
        $this->checkDepartmentRules($department);

        switch ($finishID) {
            case 1: //corte reto
                $finishRule = $this->case_corte_reto($department, $amount, $countFinishes);
                break;
            case 2: //Furo 3mm
                $finishRule = $this->case_furo($department, $amount, $countFinishes);
                break;
            case 4: //Corte e Vinco
                $finishRule = $this->case_corte_e_vinco($department, $amount, $countFinishes);
                break;
            case 7: // Blocagem
                $finishRule = $this->case_blocagem($department, $amount, $countFinishes);
                break;
            case 8: //Canteamento
                $finishRule = $this->case_canteamento($department, $amount, $countFinishes);
                break;
            case 9: // Dobra
                $finishRule = $this->case_dobra($department, $amount, $countFinishes);
                break;
            case 10: //Serrilha
                $finishRule = $this->case_serrilha($department, $amount, $countFinishes);
                break;
            case 11: //Vinco
                $finishRule = $this->case_vinco($department, $amount, $countFinishes);
                break;
            case 13: //Numeração
                $finishRule = $this->case_numeracao($department, $amount, $countFinishes);
                break;
            case 14: //Encadernação
                $finishRule = $this->case_encadernacao();
                break;
            case 15: //Encadernação Wire-o
                $finishRule = $this->case_encadernacao_wireo();
                break;
            case 12: //Laminação Brilho 1/0
                $finishRule = $this->case_laminacao($department, $amount, $countFinishes);
                break;
            case 16: //Laminação Fosca 1/0
                $finishRule = $this->case_laminacao($department, $amount, $countFinishes);
                break;
            case 17: //Laminação Brilho 1/1
                $finishRule = $this->case_laminacao($department, $amount, $countFinishes);
                break;
            case 18: //Laminação Fosca 1/1
                $finishRule = $this->case_laminacao($department, $amount, $countFinishes);
                break;
        }

        $finishRule = $this->getFinalMaxQuantity($skuID, $finishRule);

        return $finishRule;
    }

    private function case_corte_reto($department, $amount, $countFinishes)
    {
        $additionalPrice = $this->price;
        $additionalTime = 0;


        if ($countFinishes > 1) {
            $additionalPrice =   $this->price * $countFinishes;
        }

        if ($amount > 1000 && $countFinishes > 1) {
            $additionalPrice = (($amount / 1000) * $this->price) * $countFinishes;
        }
        $additionalTime = 1;

        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => null];
    }

    private function case_furo($department, $amount, $countFinishes)
    {
        $additionalPrice = $this->price;
        $additionalTime = 0;

        if ($department == 2) {
            if ($countFinishes > 1) {
                $additionalPrice =   $this->price * $countFinishes;
            }
            if ($amount > 1000)
                $additionalPrice = (floor($amount / 1000) *  $this->price) * $countFinishes;
            $additionalTime = 1;
        } else {
            if ($countFinishes > 1) {
                $additionalPrice =   $this->price * $countFinishes;
            }
            if ($amount > 1000)
                $additionalPrice = (floor($amount / 1000) *  $this->price) * $countFinishes;
            $additionalTime = 1;
        }
        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => null];
    }

    private function case_corte_e_vinco($department, $amount, $countFinishes)
    {
        $additionalPrice = $this->price;
        if ($amount > 1000)
            $additionalPrice = floor($amount / 1000) *  $this->price;
        $additionalTime = 5;
        $additionalPrice;
        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => 1, "ref" => 4];
    }


    private function case_blocagem($department, $amount, $countFinishes)
    {
        $additionalPrice = $this->price;
        $additionalTime = 0;

        if ($department == 2) {

            if ($amount > 1000)
                $additionalPrice = floor($amount / 1000) *  $this->price;
            $additionalTime = 1;
        } else {

            if ($amount > 1000)
                $additionalPrice = floor($amount / 1000) *  $this->price;
            $additionalTime = 1;
        }

        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => 1];
    }

    private function case_canteamento($department, $amount, $countFinishes)
    {
        $additionalPrice = $this->price;
        $additionalTime = 0;
        if ($department == 2) {
            if ($amount > 1000)
                $additionalPrice = floor($amount / 1000) *  $this->price;
            $additionalTime = 1;
        } else {
            if ($amount > 1000)
                $additionalPrice = floor($amount / 1000) *  $this->price;
            $additionalTime = 1;
        }
        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => 1];
    }

    private function case_dobra($department, $amount, $countFinishes)
    {
        $additionalPrice = ($countFinishes > 1) ? $this->price : 0;
        $additionalTime = 0;


        if ($amount > 1000 && $countFinishes > 1)
            $additionalPrice = floor($amount / 1000) *  $this->price;

        $additionalTime = 1;


        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => null];
    }

    private function case_serrilha($department, $amount, $countFinishes)
    {
        $additionalPrice = $this->price;
        $additionalTime = 0;
        if ($department == 2) {
            if ($countFinishes > 1) {
                $additionalPrice =   $this->price * $countFinishes;
            }

            if ($amount > 1000 && $countFinishes > 1)
                $additionalPrice = (floor($amount / 1000) *  $this->price) * $countFinishes;
            $additionalTime = 1;
        } else {
            if ($countFinishes > 1) {
                $additionalPrice =   $this->price * $countFinishes;
            }

            if ($amount > 1000 && $countFinishes > 1) {
                $additionalPrice = (floor($amount / 1000) *  $this->price) * $countFinishes;
            }

            $additionalTime = 1;
        }
        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => null];
    }

    private function case_vinco($department, $amount, $countFinishes)
    {
        $additionalPrice = ($countFinishes > 1) ? $this->price : 0;
        $additionalTime = 0;

        if ($amount > 1000 && $countFinishes > 1)
            $additionalPrice = floor($amount / 1000) *  $this->price;

        $additionalTime = 1;

        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => null];
    }


    private function case_numeracao($department, $amount, $countFinishes)
    {
        $additionalPrice = $this->price;
        $additionalTime = 0;
        if ($amount > 1000 && $countFinishes > 1) {
            $additionalPrice = floor($amount / 1000) *  $this->price;
        }
        $additionalTime = 1;
        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => 1];
    }


    private function case_encadernacao()
    {
        $additionalPrice = $this->price;
        $additionalTime = 0;

        $additionalTime = 1;
        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => 1];
    }

    private function case_encadernacao_wireo()
    {
        $additionalPrice = $this->price;
        $additionalTime = 0;

        $additionalTime = 1;
        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => 1];
    }

    private function case_laminacao($department, $amount, $countFinishes){
        if(@$this->finish->data['multiply'] == "sku")
            return $this->case_laminacao_sku($amount);
        
        return $this->case_laminacao_default($department, $amount, $countFinishes);
    }

    private function case_laminacao_sku($amount){
        return ["additionalPrice" => $this->price, "additionalTime" => 1, "maxQuantity" => null];
    }

    private function case_laminacao_default($department, $amount, $countFinishes){
        $additionalPrice = $this->price;
        $additionalTime = 0;
    
        if ($countFinishes > 1) {
            $additionalPrice = $this->price * $countFinishes;
        }
        
        if ($amount > 1000 && $countFinishes > 1) {
            $additionalPrice = (floor($amount / 1000) *  $this->price) * $countFinishes;
        }
        
        $additionalTime = 1;
        return ["additionalPrice" => $additionalPrice, "additionalTime" => $additionalTime, "maxQuantity" => 1];
    }

    private function getFinalMaxQuantity($skuID, $finishRule)
    {
        $sku = SKU::where("id", $skuID)->first();
        $data = (array) json_decode($sku->data);
        $variation = (array) $data["variations"];

        if ($data["department"] === "OFFSET" && ($variation["Gramatura"] == 250 || $variation["Gramatura"] == 300))
            $finishRule['maxQuantity'] = 1;

        return $finishRule;
    }
}
