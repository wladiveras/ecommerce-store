@extends('template.master')
@section('header')
    @if($page->seo)
        @if(@$page->seo["canonical"])
            <link rel="canonical" href="{{$page->seo["canonical"]}}"/>
        @endif
        @if(@$page->seo["title"])
            <meta property="og:title" content="{{$page->seo["title"]}}">
        @endif
        @if(@$page->seo["description"])
            <meta name="description" content="{{$page->seo["description"]}}">
        @endif
    @endif
@endsection
@section('content')
<nav aria-label="breadcrumb" >
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Início</a></li>
        <li class="breadcrumb-item">{{$page->title}}</li>
    </ol>
</nav>
<h1 style="color:rgb(23, 157, 171); font-weight: 900;text-transform: uppercase;text-align: center;">{{$page->title}}</h1>
<div style="margin-bottom: 100px;margin-bottom: 100px;">
    {!! $page->content !!}
</div>
@endsection
