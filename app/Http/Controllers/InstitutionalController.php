<?php

namespace App\Http\Controllers;
use App\Models\Page;

class InstitutionalController extends Controller
{
    public function showPage($slug)
    {
        $page = Page::where("slug",$slug)->where("enabled",true)->firstOrFail();
        
        if(@$page->seo["require_auth"] == "1" && \Auth::guest()){
            $redirect = @$page->seo["require_auth_redirect"];
            if(!$redirect)
                $redirect = route("login");
            
            return redirect($redirect);
        }
        
        return view("pages.institutional.page",compact("page"));
    }

}
