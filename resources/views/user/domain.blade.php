@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{$userinfo->userid}}({{$userinfo->nickname}}) 누르기율 배당율 변경 로그</h5>
                </div>
            </div>
        </div>
    </div>
@endsection
