@extends('pages.home.master')
@section('content')
<!-- Main content -->
<div id="app">
    <router-view></router-view>
    <vue-progress-bar></vue-progress-bar>
</div>
@endsection