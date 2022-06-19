@extends('template.simple')
@section('content')
<div class="col-sm-12">
  <div class="row mb-5">
    <div class="col-sm-12">
      <h1 class="font-weight-light text-transform-uppercase">NOSSOS <span class="font-weight-bold">CONTATOS</span> </h1>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-6">
      <contact-form class="mb-4">{!! app('captcha')->display(['add-js' => false]) !!}</contact-form>
      <join-us></join-us>
    </div>
    <div class="col-sm-6">
      <supply-stores-list></supply-stores-list>
    </div>
  </div>
</div>
@endsection
@section('captcha')

{!! app('captcha')->displayJs() !!}
@endsection
