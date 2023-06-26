@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">회원 추가</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div>
                        @php
                            $snzResult = '';
                            if (\Session::has('result'))
                                $snzResult = \Session::get('result');
                        @endphp
                        @if ($snzResult != '')
                            @if (strpos($snzResult, '성공') !== false)
                                <div id="flashMessage" class="message success" style=''>{{$snzResult}}</div>
                            @else
                                <div id="flashMessage" class="message error" style=''>{{$snzResult}}</div>
                            @endif
                        @endif
                        @php
                            $userinfo = Session::get('form-user');
                        @endphp
                    </div>
                    <form action="" id="UserAddForm" method="post" accept-charset="utf-8">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="UserParentUsername" class="form-label">상위회원ID</label>
                                    <input type="text" class="form-control" placeholder="*비워두면 최상위 회원으로 등록"
                                           id="UserParentUsername" name="parent_username"
                                           value="{{$userinfo['parent_username'] ?? $parent}}"
                                           @if ($parent != '') readonly @endif>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="UserBankName" class="form-label">은행명</label>
                                    <input type="text" class="form-control" value="{{$userinfo['bank_name'] ?? ''}}"
                                           id="UserBankName" name="bank_name" maxlength="50" required>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="UserUsername" class="form-label">회원로그인ID</label>
                                    <input type="text" class="form-control" maxlength="50"
                                           id="UserUsername" required value="{{$userinfo['username'] ?? ''}}"
                                           name="username">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="UserBankNumber" class="form-label">계좌번호</label>
                                    <input type="text" class="form-control" maxlength="50"
                                           id="UserBankNumber" required value="{{$userinfo['bank_number'] ?? ''}}"
                                           name="bank_number">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phonenumberInput" class="form-label">전화번호</label>
                                    <input type="tel" class="form-control"
                                           id="phonenumberInput" maxlength="50" name="phone"
                                           value="{{$userinfo['phone'] ?? ''}}" required>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="UserExchangePwd" class="form-label">환전 비밀번호</label>
                                    <input type="text" class="form-control"
                                           id="UserExchangePwd" name="exchange_pwd"
                                           value="{{$userinfo['exchange_pwd'] ?? ''}}" required maxlength="100">
                                </div>
                            </div>
                            <!--end col-->
                            <div class=" col-md-6">
                                <div class="mb-3">
                                    <label for="UserPassword" class="form-label">로그인암호</label>
                                    <input type="password" class="form-control"
                                           id="UserPassword" name="password" maxlength="100"
                                           value="{{$userinfo['password'] ?? ''}}" required>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="UserBankerName" class="form-label">예금주</label>
                                    <input type="text" class="form-control"
                                           id="UserBankerName" name="banker_name" maxlength="50"
                                           value="{{$userinfo['banker_name'] ?? ''}}" required>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="UserNickname" class="form-label">닉네임</label>
                                    <input type="text" class="form-control"
                                           id="UserNickname" value="{{$userinfo['nickname'] ?? ''}}" name="nickname"
                                           maxlength="50" required>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="UserState" class="form-label">상태</label>
                                    <select id="UserState" class="form-select" data-choices
                                            data-choices-sorting="true" name="state">
                                        <option value="1" @if (($userinfo['state'] ?? '') == '1') selected @endif >정상회원</option>
                                        <option value="0" @if (($userinfo['state'] ?? '') == '0') selected @endif >이용중지</option>
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
