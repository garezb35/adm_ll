@extends('layouts.master-without-nav')
@section('content')
   <div class="card">
       <div class="card-header">
           <h5 class="card-title mb-0">소속 이동</h5>
       </div>
       <div class="card-body">
           @php
               if (\Session::has('ment')) {
                  $snzResult = \Session::get('ment');
               }
           @endphp
           @if (($snzResult ?? '') != '')
               <div class="message-part">{{$snzResult ?? ''}}</div>
           @endif
           <div class="alert alert-warning alert-dismissible alert-additional fade show mb-3" role="alert">
               <div class="alert-body">
                   <div class="d-flex">
                       <div class="flex-shrink-0 me-3">
                           <i class="ri-alert-line fs-lg align-middle"></i>
                       </div>
                       <div class="flex-grow-1">
                           <h5 class="alert-heading">소속이동하려는 회원의 수수료가 상위회원보다 큰 경우 수수료 적립이 안됩니다. </h5>
                           <p class="mb-0">새 상위 회원을 지정하여 회원의 소속을 변경합니다. </p>
                           <p class="mb-0">상위 회원 지정이 없으면 해당 회원이 최상위 회원이 됩니다. </p>
                           <p class="mb-0">사이트 운영 중에는 소속 이동을 실행할 수 없습니다.</p>
                       </div>
                   </div>
               </div>
           </div>
           <div class="mb-3">
               <span class="text-muted text-uppercase fw-semibold mb-3">현재 소속 구조 :</span>
               <span class="text-muted mb-1"> {{$master}}</span>
           </div>
           <div>
               <form action="" novalidate="novalidate" id="UserEditPathForm" method="post" accept-charset="utf-8">
                   @csrf
                   <input type="hidden" name="userid" value="{{$userid}}" />
                   <div class="mb-3">
                       <label class="form-label" for="UserParentUsername">상위회원 ID</label>
                       <input name="parent_username" type="text" id="UserParentUsername" class="form-control" style="width: 200px"/>
                   </div>
                   <div>
                       <input type="submit" class="btn btn-primary"  value="이동"/>
                   </div>
               </form>
           </div>
       </div>
   </div>
@endsection
