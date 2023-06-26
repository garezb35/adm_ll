@extends('layouts.master')
@section('title')
    @lang('translation.ch-ex-details')
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form id="form-search" action="" method="get">
                <input type="hidden" name="sort" id="sort-arrow" value="">
                <input type="hidden" name="type" id="sort-type" value="">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-xxl-2 col-sm-6">
                                <label for="startDate">@lang('translation.date-search')</label>
                                <div>
                                    <input type="text" class="form-control" id="startDate"
                                           data-provider="flatpickr"
                                           data-date-format="Y-m-d" data-range-date="true" placeholder="Select date"
                                           name="start_date"
                                           value="{{$start_date}}">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-sm-6">
                                <label for="search-txt">@lang('translation.userid')</label>
                                <div class="search-box">
                                    <input type="text" class="form-control search" name="username" id="search-txt"
                                           value="{{$username}}" placeholder="@lang('translation.userid')">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-sm-6">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-secondary w-100"><i
                                            class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-sm-6 text-right">
                                <label>&nbsp;</label>
                                <div>
                                    <blockquote class="blockquote custom-blockquote blockquote-danger rounded mb-0 d-flex">
                                        <p class="mb-0 flex-grow-1">@lang('translation.charge-amount')</p>
                                        <p class="mb-0 flex-shrink-0">{{number_format($nSumCharge)}}</sp</p>
                                    </blockquote>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-sm-6 text-right">
                                <label>&nbsp;</label>
                                <div>
                                    <blockquote class="blockquote custom-blockquote blockquote-primary rounded mb-0 d-flex">
                                        <p class="mb-0 flex-grow-1">@lang('translation.excharge-amount')</p>
                                        <p class="mb-0 flex-shrink-0">{{number_format($nSumExcharge)}}</p>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('translation.ch-ex-details')</h5>
                </div>
                <div class="card-body">
                    @include('analysis.chargeExchargeListTable', [
                        'sort' => $sort,
                        'type'=> $type,
                        'chargeExhRecord' => $chargeExhRecord
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection
