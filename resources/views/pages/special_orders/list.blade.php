@extends('template.simple')
@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('pedidos_especiais.index')}}">Projetos Especiais</a></li>
        <li class="breadcrumb-item active">@if(!$data["is_admin"])Meus @endif Projetos</li>
        @if(!$data["is_admin"])
            <a href="{{route("pedidos_especiais.create")}}" class="d-flex flex-grow-1 link justify-content-end">Criar Um Projeto Especial</a>
        @endif
    </ol>
</nav>
<list-special-order :data="{{json_encode($data)}}"></list-special-order>
    
@endsection
