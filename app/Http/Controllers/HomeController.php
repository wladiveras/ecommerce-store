<?php

namespace App\Http\Controllers;

// use App\Models\Product;
// use App\Services\Integration\Core;
use App\Models\StoreConfig;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config = StoreConfig::first();
        return view('index', compact('config'));
    }

    public function normas_art_view()
    {
        return view('pages.general.normas-art');
    }

    public function novos_produtos_desconto_view()
    {
        return view("pages.institutional.novosprodutos");
    }

    public function dia_das_mulheres_view()
    {
        return view("pages.institutional.diadasmulheres");
    }
}
