<style>
    .form-label {
        font-weight: bold !important;
    }
</style>
@extends('layouts.master-without-nav')
@section('content')
    <div>
        @if (\Session::has('result'))
            @if (\Session::get('result') == 'success')
                <div id="flashMessage" class="message success" style=''>유저 정보수정 성공!</div>
            @elseif (\Session::get('result') == 'fail')
                <div id="flashMessage" class="message error" style=''>유저 정보수정 실패! </div>
            @elseif (\Session::get('result') == 'fail3')
                <div id="flashMessage" class="message error" style=''>유저 정보수정 실패! 계좌정보와 전화번호는 특수문자 입력 불가 합니다.</div>
            @else
                <div id="flashMessage" class="message error" style=''>유저 정보수정 실패! 비밀번호는 영문, 숫자, 특수문자 조합되여야 합니다.</div>
            @endif
        @endif
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                회원 수정
            </h5>
        </div>
        <div class="card-body">
            <form action="" novalidate="novalidate" autocomplete="off" id="UserEditForm" method="post"
                  accept-charset="utf-8">
                @csrf
                <input name="api_key" tabindex="22" readonly="readonly" maxlength="40" type="hidden"
                       value="{{$userinfo->api_key}}" id="UserOpenapiV4Key">
                <div class="row mb-3">
                    <div class="col-md-6">
                        @php
                            $headinfo = $userinfo->headinfo;
                        @endphp
                        <label for="firstNameinput" class="form-label">최상위파트너</label>
                        <input type="text" class="form-control-plaintext" id="firstNameinput" readonly
                               value="{{$headinfo->userid ?? $userinfo->userid}}({{$headinfo->nickname ?? $userinfo->nickname}})">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="UserBankName">은행명</label>
                        <input type="text" name="bankname" tabindex="17" maxlength="50"
                               value="{{$userinfo->bankname}}" id="UserBankName" required="required"
                               class="form-control mb-2"/>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="UserBankNumber">계좌번호</label>
                        <input name="banknumber" tabindex="18" maxlength="50" type="text"
                               value="{{$userinfo->banknumber}}" id="UserBankNumber" required="required"
                               class="form-control mb-2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="UserBankerName">예금주</label>
                        <input name="bankmaster" tabindex="19" maxlength="50" type="text"
                               value="{{$userinfo->bankmaster}}" id="UserBankerName" required="required"
                               class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        @php
                            $joininfo = $userinfo->joininfo;
                        @endphp
                        <label class="form-label">상위파트너</label>
                        <input type="text" class="form-control" readonly
                               value="@if (isset($joininfo->userid) && $joininfo->userid != "")
                                                    {{$joininfo->userid}}({{$joininfo->nickname}})
                                                @endif">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="UserPhone">전화번호</label>
                        <input name="phone" tabindex="20" maxlength="50" type="tel" value="{{$userinfo->phone}}"
                               id="UserPhone" required="required" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">아이디(닉네임)</label>
                        <input type="text" class="form-control-plaintext"
                               value="{{$userinfo->userid}}({{$userinfo->nickname}})">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="password_show">비밀번호</label>
                        <input name="password" tabindex="3" maxlength="100" type="text"
                               value="{{$userinfo->password_show}}" id="UserPassword" required="required"
                               id="password_show" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="UserMemo">메모</label>
                        <textarea name="memo" maxlength="1000" cols="30" rows="6"
                                  id="UserMemo" class="form-control">{{$userinfo->memo}}</textarea>
                    </div>
                    <div class="col-md-6">
                        <input type="hidden" name="limit_bet" id="UserWebBetBlocked_" value="0">
                        <h5 class="fs-md mb-3 text-muted">계좌정보</h5>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="limit_bet" tabindex="25" value="1"
                                   @if ($userinfo->limit_bet == '1') checked @endif id="UserWebBetBlocked"
                                   class="form-check-input" role="switch">
                            <label class="form-check-label" for="UserWebBetBlocked">회원 웹사이트 배팅 금지</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="limit_exchange" id="UserExchgBann_" value="0">
                            <input type="checkbox" name="limit_exchange" tabindex="25" value="1"
                                   @if ($userinfo->limit_exchange == '1') checked @endif id="UserExchgBann"
                                   class="form-check-input" role="switch">
                            <label class="form-check-label" for="UserExchgBann">환전신청금지</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="limit_rolling" id="UserCommWithdrawBann_" value="0">
                            <input type="checkbox" name="limit_rolling" tabindex="25" value="1"
                                   @if ($userinfo->limit_rolling == '1') checked @endif id="UserCommWithdrawBann"
                                   class="form-check-input" role="switch">
                            <label class="form-check-label" for="UserCommWithdrawBann">수수료전환 신청금지</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="isStore" id="IsUserStore_" value="0">
                            <input type="checkbox" name="isStore" tabindex="25" value="1"
                                   @if ($userinfo->isStore == '1') checked @endif id="IsUserStore"
                                   class="form-check-input" role="switch">
                            <label class="form-check-label" for="IsUserStore">매장권한</label>
                        </div>
                        <div>
                            <label for="UserColor" class="form-label">회원강조색</label>
                            <input type="color" class="form-control form-control-color w-100" id="UserColor" value="{{$userinfo->user_color ?? "333"}}" name="user_color">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="UserExchangePwd">환전 비밀번호</label>
                        <input name="exchangepassword" tabindex="4" maxlength="100" type="text"
                               value="{{$userinfo->exchangepassword}}" id="UserExchangePwd" required="required"
                               class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="UserCredit">여신금액(원)</label>
                        <input name="loan" tabindex="4" value='{{$userinfo->procinfo->loan}}' type="number"
                               id="UserCredit" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="UserJoinCode">가입코드</label>
                        <input name="joinkey" tabindex="22" value="{{$userinfo->joinkey}}" readonly="readonly"
                               maxlength="50" type="text" id="UserJoinCode" class="form-control mb-2">
                        <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                            생성/삭제 후 저장해야 적용
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <button type="button" onclick="genJoinCode(6);" class="btn btn-outline-secondary btn-sm">새로생성
                        </button>
                        <button type="button" onclick="removeJoinCode();" class="btn btn-outline-secondary btn-sm">삭제
                        </button>
                    </div>
                </div>
                <div class="text-end">
                        <button class="btn btn-primary" type="reset">수정취소</button>
                        <button class="btn btn-primary" type="submit">저장하기</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(function() {
            $("#UserEditForm").submit(function() {
                if(confirm("정말 저장할까요?")) {
                    $("input[type=submit]").attr('disabled', true);
                    return true;
                }
                return false;
            });
        });

        function genKey(len) {
            if(!len) len = 30;
            var key = randStr(len);
            $("#UserOpenapiV4Key").val(key);
        }

        function randStr(len) {
            var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            var str = '';
            for(var i = 0; i < len; i++) {
                var rnum = Math.floor(Math.random() * chars.length);
                str += chars.substring(rnum, rnum + 1);
            }
            return str;
        }

        function genJoinCode(len) {
            if(!len) len = 30;
            var key = randStr2(len);
            $("#UserJoinCode").val(key);
        }

        function randStr2(len) {
            var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            var str = '';
            for(var i = 0; i < len; i++) {
                var rnum = Math.floor(Math.random() * chars.length);
                str += chars.substring(rnum, rnum + 1);
            }
            return str;
        }

        function removeKey() {
            $("#UserOpenapiV4Key").val("");
        }

        function removeJoinCode() {
            $("#UserJoinCode").val("");
        }
    </script>
@endsection
