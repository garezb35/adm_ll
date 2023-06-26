@extends('layouts.master-without-nav')
@section('content')
    <div class="mb-3">
        @php
            $chargeinfo = Session::get('form-charge');
        @endphp
        @if (\Session::has('result'))
            @if (\Session::get('result') == 'success')
                <div id="flashMessage" class="message success" style=''>관리자 충전성공!</div>
            @elseif (\Session::get('result') == 'pass')
                <div id="flashMessage" class="message error" style=''>관리자 비번틀림!</div>
            @else
                <div id="flashMessage" class="message error" style=''>관리자 충전실패!</div>
            @endif
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                @if ($live == 1)
                    카지노 알지급
                @elseif ($live == 0)
                    슬롯 알지급
                @else
                    관리자 충전
                @endif
            </h5>
        </div>
        <div class="card-body">
            <div class="alert border-0 alert-info" role="alert">
                회원 요청없이 관리자의 권한으로 충전합니다.
            </div>
            <form action="" novalidate="novalidate" autocomplete="off" id="UserAdminChargeForm" method="post"
                  accept-charset="utf-8">
                @csrf
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th scope="row">대상회원ID</th>
                                        <td class="text-muted text-end">{{$user->userid}}({{$user->nickname}})</td>
                                        <th scope="row">관리자암호</th>
                                        <td class="text-muted text-end"><input name="admin_pwd" type="password"
                                                                               id="UserAdminPwd"
                                                                               value="{{$chargeinfo['admin_pwd'] ?? ''}}" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">현재보유금</th>
                                        <td class="text-muted text-end"> @if (config('app.no_mini') == 0)
                                                {!! printColorMoney($user->procinfo->money) !!}</td>
                                        @else
                                            0
                                            @endif</td>
                                            <th scope="row"></th>
                                            <td class="text-muted text-end"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">현재보유알</th>
                                        <td class="text-muted text-end">
                                            @if (config('app.no_mini') == 0)
                                                {!! printColorMoney($user->procinfo->bpoint) !!}
                                            @else
                                                {!! printColorMoney($user->procinfo->bpoint + $user->procinfo->money) !!}
                                            @endif
                                        </td>
                                        <th scope="row">@if ($live >= 0)
                                                지급금액
                                            @else
                                                충전금액
                                            @endif</th>
                                        <td class="text-muted text-end"><input name="amount" type="number" id="UserAmount"
                                                                               value="{{$chargeinfo['amount'] ?? ''}}" required></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="submit">
                            <input type="submit" value="충전하기" class="btn btn-secondary btn-sm">
                            <input type="button" value="보유갱신" onclick="window.location.reload()" class="btn btn-outline-secondary btn-sm">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(function() {
            $("#UserAdminChargeForm").submit(function() {
                if(confirm("충전하시겠습니까?")) {
                    return true;
                }
                return false;
            });
        });
    </script>
@endsection
