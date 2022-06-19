@extends('template.simple')
@section('content')

    <cart-view :user="{{json_encode($user)}}" :_cart="{{json_encode($cart)}}"  _notempty="{{$not_empty}}" ></cart-view>

@endsection
