@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{$user->userid}}({{$user->nickname}})</b> 보유머니상세</h5>
        </div>
        <div class="card-body">
            <div class="row mt-3">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <th>보유머니:</th>
                                <td>{{number_format($user->procinfo->money ?? 0)}}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <th>카지노보유:</th>
                                <td class="fw-bold">{{number_format($user->procinfo->cx_point ?? 0)}}</td>
                                <td><a href="{{route('user_withdraw')}}?id={{$user->id}}&type=cx" class="btn btn-outline-secondary btn-sm">회수</a></td>
                            </tr>
                            <tr>
                                <th>
                                    <div title='전환실패로 보류된 머니. 게임재실행시에 충전됨'>보류중머니:</div>
                                </th>
                                <td class="fw-bold text-danger">{{number_format($user->procinfo->fail_point ?? 0)}}</td>
                                <td><a href="{{route('user_withdraw')}}?id={{$user->id}}&type=fail" class="btn btn-sm btn-outline-secondary">회수</a></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
@endsection
