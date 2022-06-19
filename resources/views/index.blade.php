@extends('template.master')
<?php $template = App\Http\Controllers\TemplateController::current(); ?>
@section('body')
    
    <?php 
        $showmore = true;
        $department = "todos";
    ?>
    @include("pages.products.list",compact('department','showmore'))


@endsection