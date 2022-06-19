@extends('template.base-template')
@section('footer-widget')
@if(Auth::check())
    <?php $manager = Auth::user()->reseller->accountManager; ?>
    @if($manager)
        <help-float-button :manager="{{json_encode($manager)}}"></help-float-button>
    @endif
@endif
<div class="container-fluid">
    <testimonies-view style="margin-top:64px!important;"></testimonies-view>
</div>
@endsection
