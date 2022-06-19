<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business\Business_;
use App\Models\Business\Business_Product;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use marcusvbda\uploader\Models\File;
use marcusvbda\uploader\Models\FileRelation;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.business.view');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        //Log::debug('id->  '.$slug );
        $business = Business_::where(['slug' => $slug, 'enabled' => '1'])->first();
        $business->files = $this->getFilesById($business); 
        $business_product = Business_Product::where(['business_id' => $business->id])->get();
        $result = [];
        foreach($business_product as $b){
            $products = Product::where(['status' => true, 'id' => $b->product_id])->orderBy("id","asc")->get();
            foreach($products as $product){
                $result[] = [
                    "product" => $product,
                    "name" => $product->name,
                    "url"  => route("product.view", ["product" => $product->slug]),
                    "department" => self::get_department($product->department)
                ];
            }
                        
        }
        Log::debug('business fim->  '.json_encode($business->files) );
        return view('pages.business.view',['business' => $business, 'result' => $result]);
    }

    //getFilesById MULTIPLOS ARQUIVOS
    //
    // public function getFilesById($business){
    //     $model = strtolower(class_basename($business));
    //     Log::debug('$business->getMorphClass()->  '.json_encode($model));
    //     $file_relation = FileRelation::where('model_id', $business->id)->where('model_type', $model)->where('ref', 'image')->get();
    //     Log::debug('file_relation->  '.json_encode($file_relation));
    //     $filearray = array();
    //     foreach ($file_relation as $key => $f) {
    //         $file = File::where('ref',$f->file_ref)->get();
    //         array_push($filearray, $file);
    //     }
        
    //     Log::debug('file->  '.json_encode($file));
    //     Log::debug('filearray->  '.json_encode($filearray));
    //     return $filearray;
    // }

    public function getFilesById($business){
        $model = strtolower(class_basename($business));
        //Log::debug('$business->getMorphClass()->  '.json_encode($model));
        $file_relation = FileRelation::where('model_id', $business->id)->where('model_type', $model)->where('ref', 'image')->first();
        //Log::debug('file_relation->  '.json_encode($file_relation));
        $file = File::where('ref',$file_relation->file_ref)->get();
        //Log::debug('file->  '.json_encode($file));
        return $file;
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
