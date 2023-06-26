@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form id="form-search" action="" method="get">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-xxl-2 col-sm-6">
                                <div>
                                    <select class="form-control" id="option" name="option" data-choices
                                            data-choices-search-false>
                                        <option value="1" {{$option == 1 ? 'selected' : ''}}>@lang('translation.userid')</option>
                                        <option value="2" {{$option == 2 ? 'selected' : ''}}>@lang('translation.nickname')</option>
                                    </select>
                                </div>
                            </div>

                            <!--end col-->
                            <div class="col-xxl-2 col-sm-6">
                                <select class="form-control" id="sub" name="sub" data-choices
                                        data-choices-search-false>
                                    <option value="1" {{$sub == 1 ? 'selected' : ''}}>@lang('translation.user')</option>
                                    <option value="2" {{$sub == 2 ? 'selected' : ''}}>@lang('translation.submember-include')</option>
                                </select>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="search-box">
                                    <input type="text" class="form-control search" name="search" id="seatch-txt"
                                           value="{{$search}}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-6">
                                <div>
                                    <input type="text" class="form-control" id="start"
                                           data-provider="flatpickr"
                                           data-date-format="Y-m-d" placeholder="Select date" name="start"
                                           value="{{$start}}">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-sm-6">
                                <div>
                                    <input type="text" class="form-control" id="end"
                                           data-provider="flatpickr"
                                           data-date-format="Y-m-d" placeholder="Select date" name="end"
                                           value="{{$end}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-1 col-sm-6">
                                <div>
                                    <button type="submit" class="btn btn-secondary w-100"><i
                                            class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                                    </button>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
            </form>
        </div>
        <!--end col-->
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('translation.daily-statistics')</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th rowspan="2">@lang('translation.date')</th>
                                <th rowspan="2">@lang('translation.charge-amount')</th>
                                <th rowspan="2">@lang('translation.excharge-amount')</th>
                                <th rowspan="2">@lang('translation.chexloss')</th>
                                <th colspan="3">@lang('translation.betting-amount')</th>
                                <th colspan="3">@lang('translation.casino')</th>
                                <th colspan="3">@lang('translation.rolling-amount')</th>
                                <th colspan="3">@lang('translation.profit-loss')</th>
                            </tr>
                            <tr>
                                <th>@lang('translation.casino')</th>
                                <th>@lang('translation.slot')</th>
                                <th>@lang('translation.total')</th>
                                <th>@lang('translation.casino')</th>
                                <th>@lang('translation.slot')</th>
                                <th>@lang('translation.total')</th>
                                <th>@lang('translation.casino')</th>
                                <th>@lang('translation.slot')</th>
                                <th>@lang('translation.total')</th>
                                <th>@lang('translation.casino')</th>
                                <th>@lang('translation.slot')</th>
                                <th>@lang('translation.total')</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr class="table-info">
                                    <td>@lang('translation.statistics')</td>
                                    @php
                                        $charge = array_sum(array_column($dateRecord, 'charge'));
                                        $excharge = array_sum(array_column($dateRecord, 'excharge'));

                                        $bb_money = array_sum(array_column($dateRecord, 'bb_money'));
                                        $sb_money = array_sum(array_column($dateRecord, 'sb_money'));

                                        $bw_money = array_sum(array_column($dateRecord, 'bw_money'));
                                        $sw_money = array_sum(array_column($dateRecord, 'sw_money'));

                                        $bf_amount = array_sum(array_column($dateRecord, 'bf_amount'));
                                        $sf_amount = array_sum(array_column($dateRecord, 'sf_amount'));
                                    @endphp
                                    <td>{{number_format($charge)}}</td>
                                    <td >{{number_format($excharge)}}</td>
                                    <td>{!! printColorMoney($charge - $excharge) !!}</td>

                                    <td>{{number_format($bb_money)}}</td>
                                    <td>{{number_format($sb_money)}}</td>
                                    <td>{{number_format($bb_money + $sb_money)}}</td>

                                    <td>{{number_format($bw_money)}}</td>
                                    <td>{{number_format($sw_money)}}</td>
                                    <td>{{number_format($bw_money + $sw_money)}}</td>

                                    <td>{{number_format($bf_amount)}}</td>
                                    <td>{{number_format($sf_amount)}}</td>
                                    <td>{{number_format($bf_amount + $sf_amount)}}</td>

                                    <td>{!! printColorMoney($bb_money - $bw_money -  $bf_amount) !!}</td>
                                    <td>{!! printColorMoney($sb_money - $sw_money - $sf_amount) !!}</td>
                                    <td>{!! printColorMoney(($bb_money - $bw_money - $bf_amount)
                                                        + ($sb_money - $sw_money - $sf_amount)) !!}
                                    </td>
                                </tr>
                            @foreach ($dateRecord as $record)
                                <tr>
                                    <td>{{$record->dt}}</td>
                                    <td>{{number_format($record->charge)}}</td>
                                    <td>{{number_format($record->excharge)}}</td>
                                    <td>{!! printColorMoney($record->charge - $record->excharge) !!}</td>

                                    <td>
                                        {{number_format($record->bb_money)}}
                                    </td>
                                    <td>{{number_format($record->sb_money)}}</td>
                                    <td>{{number_format($record->bb_money + $record->sb_money)}}</td>

                                    <td >
                                        {{number_format($record->bw_money)}}
                                    </td>
                                    <td>{{number_format($record->sw_money)}}</td>
                                    <td>{{number_format($record->bw_money + $record->sw_money)}}</td>

                                    <td>{{number_format($record->bf_amount)}}</td>
                                    <td>{{number_format($record->sf_amount)}}</td>
                                    <td>{{number_format($record->bf_amount + $record->sf_amount)}}</td>

                                    <td>{!! printColorMoney($record->bb_money - $record->bw_money - $record->bf_amount) !!}</td>
                                    <td>{!! printColorMoney($record->sb_money - $record->sw_money - $record->sf_amount) !!}</td>
                                    <td>{!! printColorMoney(($record->bb_money - $record->bw_money - $record->bf_amount)
                                                    + ($record->sb_money - $record->sw_money - $record->sf_amount)) !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
