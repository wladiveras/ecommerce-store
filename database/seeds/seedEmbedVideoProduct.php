<?php

use Illuminate\Database\Seeder;
use App\Models\Product;

class seedEmbedVideoProduct extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::where("description", "!=", "")->get();
        foreach ($products as $product) {
            $dom = new DOMDocument;
            if (trim($product->description) != "") {
                $dom->loadHTML($product->description);
                $iframes = $dom->getElementsByTagName('iframe');
                foreach ($iframes as $iframe) {
                    if ($iframe) {
                        $iframe_str = $dom->saveHTML($iframe);
                        if ($iframe_str) {
                            $product->description = str_replace($iframe_str, "", $product->description);
                            $product->embed_video = $iframe->getAttribute('src');
                            $product->save();
                        }
                    }
                }
            }
        }
    }
}
