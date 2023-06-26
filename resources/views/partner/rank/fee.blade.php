@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{$title}}총액랭킹</h4>
                </div>
                <div class="card-body">
                    <form method="get" action="" id="form-search">
                        <input name="direction" id="sort-arrow" type="hidden" value="">
                        <input name="userid" type="hidden" value="{{$userinfo->id}}">
                        <div class="row mb-3">
                            <div class="col-sm-4 col-xxl-12">
                                <label>날짜검색</label>
                                <div>
                                    <input type="text" class="form-control" id="start_date"
                                           data-provider="flatpickr"
                                           data-date-format="Y-m-d" placeholder="Select date" name="start_date"
                                           data-range-date="true"
                                           value="{{$start_date}}">
                                </div>
                            </div>
                            <div class="col-sm-2 col-xxl-12">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-secondary w-100"><i
                                            class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-2 col-xxl-12"></div>
                            <div class="col-sm-4 col-xxl-12">
                                <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0">
                                    <p class="mb-2">합계: {!! printColorMoney($charge_sum) !!}</p>
                                </blockquote>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="display table table-bordered table-nowrap">
                                    <thead>
                                        <tr>
                                            <th>회원</th>
                                            <th><a href="javascript:void(0);" class="{{$direction}} sort-link">금액</a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($chargeRecord as $record)
                                        <tr>
                                            <td style="color: {{'#' . $record->user_color}}"><b>{{$record->userid}}({{$record->nickname}})</b></td>
                                            <td>{!! printColorMoney($record->fee_sum) !!}</td>
                                        </tr>
                                    @endforeach
                                    @if ($chargeRecord->count() == 0)
                                        <tr>
                                            <td colspan="99">자료가 없습니다.</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                <div>
                                    {!! $chargeRecord->withQueryString()->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.sort-link').click(function() {
                if ($('.sort-link').hasClass('asc')) {
                    $('.sort-link').removeClass('asc');
                    $('.sort-link').addClass('desc');
                    $('#sort-arrow').val('desc');
                }
                else {
                    $('.sort-link').removeClass('desc');
                    $('.sort-link').addClass('asc');
                    $('#sort-arrow').val('asc');
                }
                $("#form-search").submit();
            });
        });
    </script>
@endsection
