@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">루징{{$type}}내역</h5>
        </div>
        <div class="card-body">
            <form method="get" action="" id="form-search">
                <input name="userid" type="hidden" value="{{$userid}}">
                <di class="row mb-3">
                    <div class="col-3">
                        <label class="">날짜검색</label>
                        <div>
                            <input type="text" class="form-control" id="startDate"
                                   data-provider="flatpickr"
                                   data-date-format="Y-m-d" placeholder="Select date" name="start_date"
                                   data-range-date="true"
                                   value="{{$start_date}}">
                        </div>
                    </div>
                    <div class="col-2">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-secondary w-100"><i
                                    class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                            </button>
                        </div>
                    </div>

                </di>
            </form>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap">
                            <thead>
                            <tr>
                                <th>회원</th>
                                <th>처리형식</th>
                                <th>루징금액</th>
                                @if ($type == '정산')
                                    <th>처리전금액</th>
                                @endif
                                <th>처리후금액</th>
                                <th>날짜</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($loseRecord as $record)
                                <tr>
                                    <td>{{$nickid}}({{$nickname}})</td>
                                    <td>
                                        @if ($record->is_deposit == 1)
                                            <span style="color: blue; font-weight: bold;">정산</span>
                                        @else
                                            <span style="color: red; font-weight: bold;">초기화</span>
                                        @endif
                                    </td>
                                    <td>{!! printColorMoney($record->money) !!}</td>
                                    @if ($type == '정산')
                                        <td>{!! printColorMoney($record->user_money - $record->money) !!}</td>
                                    @endif
                                    <td>{!! printColorMoney($record->user_money) !!}</td>
                                    <td>{{date('m-d H:i', strtotime($record->created_at))}}</td>
                                </tr>
                            @endforeach
                            @if ($loseRecord->count() == 0)
                                <tr>
                                    <td colspan="99">자료가 없습니다.</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div>
                            {!! $loseRecord->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
