<?php

namespace App\Http\Resources;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    

    public function toArray($request)
    {
        $this->item = $this['attributes'];
        $this->data = $this->item['data'];
        //return parent::toArray($request);
        //dd($this->resource);
        $cart_item = [
            "name" => $this['name'],
            "price" => $this['price'],
            "quantity" => $this->getQuantity(),
            "options" => $this->getOptions(),
            "files" => $this->getFiles(),
            "product" => $this->getProduct(),
            "prepared_in" => $this->getPreparedIn(),
            "skus" => $this->item['items']
        ];
        $this->setAdditionalOptions($cart_item);
        $this->setSizes($cart_item);
        $this->setMeasures($cart_item);
        $this->setCover($cart_item);
        $this->setFinishes($cart_item);
        return $cart_item;
    }

    private function getPreparedIn(){
        $days = $this->item->prepared_in;
        return "em até $days ".str_plural("dia",$days).($days > 1 ? " úteis" : " útil");
    }

    private function getFiles(){
        
        try{
            Log::debug('isset  '.gettype($this->item['items'][0]['upload']));
            if(gettype($this->item['items'][0]['upload'])=="object"){
                Log::debug('dentro do');
                $files = $this->item['items'][0]['upload']->file;
                if($files){
                    return $files ? $files : [];
                }
            }else{
                $files = @$this->item['items'][0]['upload']["file"];
                return $files ? $files : [];
            }
            } catch (Exception $e) {
                Log::debug('Exception->  '.json_encode($e));
                
            }
        }
    private function setSizes(&$array){
        $sizes = @$this->data['sizes'];
        if($sizes){
            $sizes = array_map(function($v){
                return [
                    'label' => $v['label'],
                    'quantity' => "{$v['qty']} ". str_plural('unidade',$v['qty'])
                ];
            },$sizes);
            $array['sizes'] = array_chunk($sizes,2);
        }
    }

    private function getQuantity(){
        $quantity = number_format($this['quantity'],0,",",".");
        $plural = str_plural("unidade",$quantity);
        return "$quantity $plural";
    }
    
    private function getProduct(){
        return $this->item['items'][0]['product'];
    }

    private function getOptions(){
        return array_filter($this->data['options'], function($v){
            return ($v !== null) || (trim($v) != "");
        });
        // try {
        //   if (isset($this->data['options'])) {
        //     return array_filter($this->data['options'], function($v){
        //         return ($v !== null) || (trim($v) != "");
        //     });
        //    }else if(isset($this->data["config_info"]->options)){
        //     //foreach ($this->data as $key => $value) {
        //         return array_filter((array)$this->data["config_info"]->options, function($v){
        //             return ($v !== null) || (trim($v) != "");
        //         });
        //     //}   
        //    }else{
        //     foreach ($this->data as $key => $value) {
        //         return array_filter((array)$value->options, function($v){
        //             return ($v !== null) || (trim($v) != "");
        //         });
        //     }  
        //    }
        // } catch (Exception $e) {
        //     Log::debug('Exception->  '.json_encode($e));
        // }
    }

    private function setCover(&$array){
        $cover = @$this->data['extra']['cover'];
        
        if($cover){
            $array['cover'] = $cover." g/m²";
        }
    }

    private function setFinishes(&$array){
        $finishes = $this->item['finishes'];
        if($finishes){
            $array['finishes'] = $finishes['finishes'];
            $array['finishesTotal'] = array_except($finishes,['finishes']);
        }
    }

    private function setAdditionalOptions(&$array){
        $additional = @$this->data['additional'];
        if($additional){
            $array['additionalConfig'] = $additional;
        }
    }

    //======================================= Measures
    private function setMeasures(&$array){
        $measures = $this->getMeasures();
        if($measures){
            $array['measures'] = $measures;
            $total = $this->getTotalMeasures($measures);
            $array['totalMeasures'] = $total;
            $array['measuresText'] = $this->getMeasuresText($total,$measures);
        }
    }

    private function format_number($n,$decimals = 2){
        if(is_numeric($n)){
            return number_format($n,$decimals);
        }else{
            return $n;
        }
    }

    private function getMeasures(){
       
        $measures = @$this->data['measures'];

        foreach($measures as $val){
            if($val === null) return;
        }

        //Log::debug('measures->  '.json_encode($measures));
        // if($measures){
        //     foreach($measures as $val){
        //         if($val === null) return;
        //     }
        // }
        

        return $measures;
    }

    private function getTotalMeasures($measures){
        extract($measures);
        $res = $width * $height;
        if($res < 0.09){
            return $this->format_number($res,4);
        }
        return $this->format_number($res);
    }

    private function getMeasuresText($total,$measures){
        $measures = array_map([$this,'format_number'],$measures);
        extract($measures);

        return "$total {$unit}² ($width$unit x $height$unit)";
    }

    //==================================== Measures

    
}
