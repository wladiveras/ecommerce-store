@extends('template.focused')
@section('content')
<div class="">
  <checkout-form
    :_creditcard_config="{{json_encode(config('creditcard'))}}"
    :_content="{{json_encode($content)}}"
    fingerprint="{{session('fingerprint')}}"
    _subtotal={{$cart->getSubTotal()}}
    :_user="{{json_encode($user)}}"
    _route_store_order="{{route('order.store')}}"
    :_cards="{{json_encode($cards)}}"
  />
</div>

<iframe style="width: 100px; height: 100px; border: 0; position:absolute; top: -5000px;" src="https://h.online-metrix.net/fp/tags?org_id=k8vif92e &session_id={{session('fingerprint')}}"></iframe>
@endsection
