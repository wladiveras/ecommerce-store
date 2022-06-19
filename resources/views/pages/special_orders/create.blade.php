@extends('template.simple')
@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('pedidos_especiais.index')}}">Projetos Especiais</a></li>
        <li class="breadcrumb-item"><a href="{{route('pedidos_especiais.list')}}">Meus Projetos</a></li>
        <li class="breadcrumb-item active">Novo Projeto Especial</li>
    </ol>
</nav>
<form-special-order :data="{{json_encode($data)}}"></form-special-order>
@endsection
