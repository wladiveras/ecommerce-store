@extends("template.padraocolor.empty-template")
@section('content')
<join-us
    @if(isset($data)) :data="{{json_encode($data)}}" @endif
/>
@endsection