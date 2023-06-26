@extends('layouts.master-without-nav')
@section('content')
    @php
        $snzResult = '';
        if (\Illuminate\Support\Facades\Session::has('ment')) {
            $snzResult = \Illuminate\Support\Facades\Session::get('ment');
        }
    @endphp
    <div>
        @if ($snzResult == 'ok')
            <div id="flashMessage" class="message success" >수수료 전환 성공!</div>
        @elseif ($snzResult != '')
            <div id="flashMessage" class="message error" >{{$snzResult}}</div>
        @endif
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-header">
                <h5 class="card-title mb-0">수수료 전환</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="alert border-0 alert-info" role="alert">
                보유 수수료를 회원 배팅머니로 충전신청 합니다.
            </div>
            <div class="row">
                <div class="col-12">
                    <form action="" novalidate="novalidate" id="UserWithdrawChargeForm" method="post" accept-charset="utf-8">
                        @csrf
                        <input name="userid" type="hidden" value="{{$userinfo->id}}"/>
                        <div class="table-responsive mb-3">
                            <table class="table table-striped">
                                <tr>
                                    <th>대상회원ID</th>
                                    <td>{{$userinfo->userid}}({{$userinfo->nickname}})</td>
                                    <th>관리자암호</th>
                                    <td>
                                        <div class="input password">
                                            <input name="admin_pwd" type="password" id="UserAdminPwd" />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>보유수수료</th>
                                    <td><span class="money money-plus">{{number_format(floor($userinfo->procinfo->point))}}</span></td>
                                    <th>전환금액</th>
                                    <td>
                                        <input name="amount" type="number" id="UserAmount" />원</td>
                                </tr>
                            </table>
                            <div class="submit">
                                <input type="submit" value="전환하기" class="btn btn-secondary btn-sm" />
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mb-3">
                        <table id="rate_edit_log_tbl" class="table table-striped">
                            <thead>
                                <tr>
                                    <th >요청회원</th>
                                    <th>처리회원</th>
                                    <th>환전금액</th>
                                    <th>결과금액</th>
                                    <th>환전일시</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($chargeRecord as $record)
                                @if (!empty($record->adminfo))
                                    <tr>
                                        <td class="member-info">{{$record->userinfo->userid}}({{$record->userinfo->nickname}})</td>
                                        <td class="member-info">@if ($record->adminfo->rolecode == '0001')[관]@endif{{$record->adminfo->userid}}({{$record->adminfo->nickname}})</td>
                                        <td>{{number_format($record->money)}}</td>
                                        <td>{{number_format($record->user_money)}}</td>
                                        <td>{{date('Y-m-d H:i', strtotime($record->created_at))}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($chargeRecord->count() == 0)
                                <tr><td colspan="20">내역이 없습니다.</td></tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="pagination-part">
                            {!! $chargeRecord->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $("#UserWithdrawChargeForm").submit(function() {
                var amount = $("#UserAmount").val();
                if(isNaN(amount)) {
                    alert('금액 오류');
                    return false;
                }
                return true;
            });
        });
    </script>
@endsection
