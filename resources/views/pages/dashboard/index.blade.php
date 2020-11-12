@extends('pages.dashboard.master')
@section('content')
<!-- Main content -->
<div class="content-wrapper" style="min-height: 547.906px;">
    <div class="container-fluid">
        <div class="row my-3">
            <div class="col-lg-12">
                <router-view></router-view>
                <vue-progress-bar></vue-progress-bar>
            </div>
        </div>
    </div>
</div>
@endsection