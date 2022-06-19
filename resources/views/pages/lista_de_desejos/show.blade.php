@extends('template.simple')
@section('content')

    <listadesejos-view :user="{{json_encode($user)}}" :_cart="{{json_encode($cart)}}"  _notempty="{{$not_empty}}" ></listadesejos-view>

@endsection