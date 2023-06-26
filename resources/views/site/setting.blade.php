@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">사이트 설정</h5>
        </div>
        <div class="card-body">
            <form action="" novalidate="novalidate" autocomplete="off" id="ConfigurationEditForm" method="post"
                  accept-charset="utf-8">
                @csrf
                <input type="hidden" name="id" value="{{$id}}" id="ConfigurationId"/>
                <div class="row">
                    {{--                    <div class="col-xxl-4">--}}
                    {{--                        <h5 class="card-title mb-3">최고관리자 관리 설정</h5>--}}
                    {{--                    </div>--}}
                    <div class="col-xxl-12">
{{--                        @if (in_array(auth()->user()->userid, ["skfkrrn999", "jimong", "elena"]))--}}
{{--                            <div class="form-check form-switch  mb-3">--}}
{{--                                <label for="ConfigurationCreateAdmin" class="form-check-label">관리자 계정 생성 허용</label>--}}
{{--                                <input type="hidden" name="admin_new" id="ConfigurationCreateAdmin_" value="0"/>--}}
{{--                                <input type="checkbox" name="admin_new" value="1" id="ConfigurationCreateAdmin"--}}
{{--                                       @if (env('ADMIN_NEW')== 1) checked @endif class="form-check-input"--}}
{{--                                       role="switch"/>--}}
{{--                            </div>--}}
{{--                        @endif--}}
                        <div class="form-check form-switch  mb-3">
                            <label for="ConfigurationIsAllowJoin" class="form-check-label">회원 가입 가능</label>
                            <input type="hidden" name="is_allow_join" id="ConfigurationIsAllowJoin_" value="0"/>
                            <input type="checkbox" name="is_allow_join" value="1" id="ConfigurationIsAllowJoin"
                                   @if ($is_allow_join == 1) checked @endif  class="form-check-input"
                                   role="switch"/>
                        </div>
                        <div class="form-check form-switch  mb-3">
                            <label for="ConfigurationIsSiteClose" class="form-check-label">회원사이트 운영중지(점검)</label>
                            <input type="hidden" name="is_site_close" id="ConfigurationIsSiteClose_" value="0"/>
                            <input type="checkbox" name="is_site_close" value="1" id="ConfigurationIsSiteClose"
                                   @if ($is_site_close == 1) checked @endif  class="form-check-input" role="switch"/>
                        </div>
                        <div class="form-check form-switch  mb-3">
                            <label for="ConfigurationIsAllowParterBankInput" class="form-check-label">회원사이트에서 하부 파트너 회원
                                생성 시 계좌정보 입력 가능</label>
                            <input type="hidden" name="is_allow_parter_bank_input"
                                   id="ConfigurationIsAllowParterBankInput_" value="0"/>
                            <input type="checkbox" name="is_allow_parter_bank_input" value="1"
                                   id="ConfigurationIsAllowParterBankInput"
                                   @if ($is_allow_parter_bank_input == 1) checked @endif  class="form-check-input"
                                   role="switch"/>
                        </div>
                        <div class="form-check form-switch  mb-3">
                            <label for="ConfigurationIsDirectBetNoAlert" class="form-check-label">회원사이트에서 배팅 시 확인 창 및 배팅
                                완료 창 표시를 생략</label>
                            <input type="hidden" name="is_direct_bet_no_alert" id="ConfigurationIsDirectBetNoAlert_"
                                   value="0"/>
                            <input type="checkbox" name="is_direct_bet_no_alert" value="1"
                                   id="ConfigurationIsDirectBetNoAlert"
                                   @if ($is_direct_bet_no_alert == 1) checked @endif  class="form-check-input"
                                   role="switch"/>

                        </div>
                        <div class="form-check form-switch  mb-3">
                            <label for="ConfigurationIsUsePwd" class="form-check-label">회원 특수비번 사용안함</label>
                            <input type="hidden" name="is_use_pwd" id="ConfigurationIsUsePwd_" value="0"/>
                            <input type="checkbox" name="is_use_pwd" value="1" id="ConfigurationIsUsePwd"
                                   @if ($is_use_pwd == 1) checked @endif  class="form-check-input"
                                   role="switch"/>

                        </div>
                        <div class="form-check form-switch  mb-3">
                            <label for="ConfigurationIsLeaderApi" class="form-check-label">API배팅 따라가기 허용</label>
                            <input type="hidden" name="is_leader_api" id="ConfigurationIsLeaderApi_" value="0"/>
                            <input type="checkbox" name="is_leader_api" value="1" id="ConfigurationIsLeaderApi"
                                   @if ($is_leader_api == 1) checked @endif  class="form-check-input"
                                   role="switch"/>

                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationLoginNotice" class="form-label">로그인 알림</label>
                            <textarea name="notice_login" class="form-control" maxlength="300" cols="30" rows="6"
                                      id="ConfigurationLoginNotice"
                                      placeholder="회원사이트 로그인 페이지 표시(300자 이내)">{{$notice_login}}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationLiveNotice" class="form-label">실시간 알림</label>
                            <textarea name="notice_live" class="form-control" maxlength="300" cols="30" rows="6"
                                      id="ConfigurationLiveNotice"
                                      placeholder="회원사이트 실시간 알림판 표시(300자 이내)">{{$notice_live}}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationLiveNoticeWelcome" class="form-label">환영 알림</label>
                            <textarea name="notice_welcome" class="form-control" maxlength="300" cols="30" rows="6"
                                      id="ConfigurationLiveNoticeWelcome"
                                      placeholder="회원사이트 실시간 알림판 표시(300자 이내)">{{$notice_welcome}}</textarea>
                        </div>
                        <div class="d-flex gap-2 bg-success mb-3" style="padding: 5px">
                            <div class="form-check form-radio-dark">
                                <label for="PwbChecking1" class="form-check-label">파워볼 운영</label>
                                <input type="radio" name="is_pwb_check" value="0" id="PwbChecking1"
                                       @if ($is_pwb_check == 0) checked @endif
                                       class="form-check-input">

                            </div>
                            <div class="form-check form-radio-dark">
                                <label for="PwbChecking2" class="form-check-label">파워볼 점검</label>
                                <input type="radio" name="is_pwb_check" value="1" id="PwbChecking2"
                                       @if ($is_pwb_check == 1) checked @endif
                                       class="form-check-input">
                            </div>
                            <div class="form-check form-radio-dark">
                                <label for="PwbChecking3" class="form-check-label">파워볼 숨김</label>
                                <input type="radio" name="is_pwb_check" value="2" id="PwbChecking3"
                                       @if ($is_pwb_check == 2) checked @endif
                                       class="form-check-input">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mb-3 bg-primary text-white" style="padding: 5px">
                            <div class="form-check form-radio-dark">
                                <label for="LiveChecking1" class="form-check-label">카지노 운영</label>
                                <input type="radio" name="is_live_check" value="0" id="LiveChecking1"
                                       @if ($is_live_check == 0) checked @endif
                                       class="form-check-input">
                            </div>
                            <div class="form-check form-radio-dark">
                                <label for="LiveChecking2" class="form-check-label">카지노 점검</label>
                                <input type="radio" name="is_live_check" value="1" id="LiveChecking2"
                                       @if ($is_live_check == 1) checked @endif
                                       class="form-check-input">
                            </div>
                            <div class="form-check form-radio-dark">
                                <label for="LiveChecking3" class="form-check-label">카지노 숨김</label>
                                <input type="radio" name="is_live_check" value="2" id="LiveChecking3"
                                       @if ($is_live_check == 2) checked @endif
                                       class="form-check-input">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mb-3 bg-danger" style="padding: 5px">
                            <div class="form-check form-radio-dark">
                                <label for="SlotChecking1" class="form-check-label">슬롯 운영</label>
                                <input type="radio" name="is_slot_check" value="0" id="SlotChecking1"
                                       @if ($is_slot_check == 0) checked @endif
                                       class="form-check-input">
                            </div>
                            <div class="form-check form-radio-dark">
                                <label for="SlotChecking2" class="form-check-label">슬롯 점검</label>
                                <input type="radio" name="is_slot_check" value="1" id="SlotChecking2"
                                       @if ($is_slot_check == 1) checked @endif
                                       class="form-check-input">
                            </div>
                            <div class="form-check form-radio-dark">
                                <label for="SlotChecking3" class="form-check-label">슬롯 숨김</label>
                                <input type="radio" name="is_slot_check" value="2" id="SlotChecking3"
                                       @if ($is_slot_check == 2) checked @endif
                                       class="form-check-input">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mb-3 bg-success" style="padding: 5px">
                            <div class="form-check form-radio-dark">
                                <label for="TypePWB1" class="form-check-label">파워볼 디자인 1</label>
                                <input type="radio" name="type_pwb" value="1" id="TypePWB1"
                                       @if ($type_pwb == 1) checked @endif
                                       class="form-check-input">
                            </div>
                            <div class="form-check form-radio-dark">
                                <label for="TypePWB2" class="form-check-label">파워볼 디자인 2</label>
                                <input type="radio" name="type_pwb" value="2" id="TypePWB2"
                                       @if ($type_pwb == 2) checked @endif
                                       class="form-check-input">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mb-3 bg-success" style="padding: 5px">
                            <div class="form-check form-radio-dark">
                                <label for="TypePWBOpen1" class="form-check-label">파워볼 중계화면 기본열기</label>
                                <input type="radio" name="type_pwb_open" value="1" id="TypePWBOpen1"
                                       @if ($type_pwb_open == 1) checked @endif
                                       class="form-check-input">
                            </div>
                            <div class="form-check form-radio-dark">
                                <label for="TypePWBOpen2" class="form-check-label">파워볼 중계화면 기본닫기</label>
                                <input type="radio" name="type_pwb_open" value="2" id="TypePWBOpen2"
                                       @if ($type_pwb_open == 2) checked @endif
                                       class="form-check-input">
                            </div>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <div class="form-check form-switch">
                                <label for="ConfigurationApiBetEnable" class="form-check-label">회원 배팅을 허용</label>
                                <input type="hidden" name="site_bet_enable" id="ConfigurationApiBetEnable_" value="0"/>
                                <input type="checkbox" name="site_bet_enable" value="1" id="ConfigurationApiBetEnable"
                                       @if ($site_bet_enable == 1) checked @endif
                                       class="form-check-input"
                                       role="switch"/>
                            </div>
                            <div class="form-check form-switch">
                                <label for="ConfigurationSiteBetEnable" class="form-check-label">API 배팅을 허용</label>
                                <input type="hidden" name="api_bet_enable" id="ConfigurationSiteBetEnable_" value="0"/>
                                <input type="checkbox" name="api_bet_enable" value="1" id="ConfigurationSiteBetEnable"
                                       @if ($api_bet_enable == 1) checked @endif
                                       class="form-check-input"
                                       role="switch"/>
                            </div>
                            <div class="form-check form-switch">
                                <label for="ConfigurationSiteBetMax" class="form-check-label">API 배팅금액 무제한</label>
                                <input type="hidden" name="api_bet_max" id="ConfigurationSiteBetMax_" value="0"/>
                                <input type="checkbox" name="api_bet_max" value="1" id="ConfigurationSiteBetMax"
                                       @if ($api_bet_max == 1) checked @endif
                                       class="form-check-input"
                                       role="switch"/>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationChargeCoin" class="form-label">가상계좌 설정</label>
                            <input name="charge_coin" type="text" placeholder="http://domain.com/coin"
                                   value="{{$charge_coin ?? ''}}" id="ConfigurationChargeCoin"
                                   class="form-control"/>
                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationChargeReqUnit" class="form-label">충/환전 신청가능기준금액(원)</label>
                            <input name="charge_req_unit" type="number" value="{{$charge_req_unit}}"
                                   id="ConfigurationChargeReqUnit" placeholder="1000이면 1000원단위로 신청가능, 0이면 제한없음"
                                   class="form-control"/>
                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationChargeReqRetryTerm" class="form-label">충전 시 동일금액 재신청
                                제한시간(분)</label>
                            <input name="charge_req_retry_term" type="number" value="{{$charge_req_retry_term}}"
                                   id="ConfigurationChargeReqRetryTerm"
                                   placeholder="10이면 10분동안 같은금액 재신청 불가, 0이면 제한없음"
                                   class="form-control"/>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label"><span class="fw-semibold">환전 휴식 시간 </span> <br><span>시/분 단위로 설정(0~23시, 0~59분) 예)23/30~9/0 = 오후11시30분부터 다음날 오전9시까지 휴식</span></label>
                            <div>
                                <input name="exchange_rest_from" min="0" max="23" type="number"
                                       value="{{$exchange_rest_from}}" id="ConfigurationExchangeRestFrom"/>시
                                <input name="exchange_rest_from_m" min="0" max="23" type="number"
                                       value="{{$exchange_rest_from_m}}" id="ConfigurationExchangeRestFromM"/>분 부터 ~
                                <input name="exchange_rest_to" min="0" max="23" type="number"
                                       value="{{$exchange_rest_to}}" id="ConfigurationExchangeRestTo"/>시
                                <input name="exchange_rest_to_m" min="0" max="23" type="number"
                                       value="{{$exchange_rest_to_m}}" id="ConfigurationExchangeRestToM"/>분 까지
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationExchangeTermMin" class="form-label">
                                <span class="fw-bold">환전 재신청 가능 간격</span><br>
                                <span>분 단위로 설정, 0이면 연속 신청 가능, 설정 시 최근XX분 이내로 재신청 불가, 처리여부 무관</span>
                            </label>
                            <input name="exchange_term_min" min="0" max="300" type="number"
                                   value="{{$exchange_term_min}}" id="ConfigurationExchangeTermMin" class="form-control"
                                   style="width: 100px"/>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <label for="ConfigurationCommWithdrawAutoGrantAmount"
                                   class="form-check-label">수수료 출금 신청 자동수락</label>
                            <input type="hidden" name="comm_withdraw_auto_grant_amount"
                                   id="ConfigurationCommWithdrawAutoGrantAmount_" value="0"/>
                            <input type="checkbox" name="comm_withdraw_auto_grant_amount" value="1"
                                   id="ConfigurationCommWithdrawAutoGrantAmount"
                                   @if ($comm_withdraw_auto_grant_amount == 1) checked @endif
                                   class="form-check-input" role="switch"/>
                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationCommWithdrawRetryTerm" class="form-label">
                                <span class="fw-bold">수수료 재신청가능간격(시간)</span><br>
                                <span>
                                    1이면 1시간 동안 재신청 불가, 0이면 제한없음
                                </span>
                            </label>
                            <input name="comm_withdraw_retry_term" type="number"
                                   value="{{$comm_withdraw_retry_term}}" id="ConfigurationCommWithdrawRetryTerm"
                                   class="form-control" style="width: 100px"/>
                        </div>
                        <div class="mb-3">
                            <label for="ConfigurationTelegram" class="form-label">텔레그람문의</label>
                            <input name="telegram" type="text" value="{{$telegram}}" id="ConfigurationTelegram"
                                   class="form-control"/>
                        </div>
                        <div class="mb-3">
                            <label for="bettingnumber" class="form-label">배팅내역현시개수</label>
                            <input name="betcnt" type="text" value="{{$betcnt}}" id="bettingnumber" class="form-control"
                                   style="width: 100px"/>
                        </div>
                        <div>
                            <input type="submit" value="적용하기" class="btn btn-primary"/>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(function() {
            $("#ConfigurationEditForm").submit(function() {
                if(confirm("정말 수정할까요?")) {
                    return true;
                }
                return false;
            });
            $("#ConfigurationOpenapiBlock").change(function() {
                var v = $(this).is(":checked")
                if(v == true) {
                    $("#ConfigurationOpenapiBlockDup").attr('checked', false);
                }
            });
        });
    </script>
@endsection
