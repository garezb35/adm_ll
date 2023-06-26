@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                새 단계 추가
            </h5>
        </div>
        <div class="card-body">
            <form action="" novalidate="novalidate" autocomplete="off" id="UserDepthNameAddForm" method="post" accept-charset="utf-8">
                @csrf
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="UserDepthNameName" class="form-label">단계명</label>
                        <input name="step_name" maxlength="50" type="text" value="" id="UserDepthNameName" required="required"  class="form-control"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <input type="submit" value="저장하기" class="btn btn-secondary btn-sm"/>
                        <a href="{{route('partner_depth_list')}}" class="btn btn-outline-secondary btn-sm">목록으로</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
