@extends('layouts.master')
@section('title')
    @lang('translation.staged-statistics')
@endsection
@section('custom-css')
    <link rel="stylesheet" type="text/css" href="{{asset('/build/css/partner/treeview.css')}}">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            @lang('translation.statistics')
        @endslot
        @slot('title')
            @lang('translation.staged-statistics')
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-lg-12">
            <form id="form-search" action="" method="get">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-xxl-2 col-sm-6">
                                <div>
                                    <select class="form-control" id="gameType" name="gameType" data-choices
                                            data-choices-search-false data-choices-removeItem>
                                        <option value="all" @if ($gameType == 'all') selected @endif>전체</option>
                                        @if (config('app.site_type') != 'BS')
                                            <option value="pwb" @if ($gameType == 'pwb') selected @endif>파워볼</option>
                                        @endif
                                        <option value="live" @if ($gameType == 'live') selected @endif>카지노</option>
                                        <option value="slot" @if ($gameType == 'slot') selected @endif>슬롯</option>
                                    </select>
                                </div>
                            </div>

                            <!--end col-->
                            <div class="col-xxl-2">
                                <select class="form-control" id="gameItem" name="gameItem" data-choices
                                        data-choices-search-false data-choices-removeItem>
                                    <option value="">@lang('translation.total')</option>
                                </select>
                            </div>
                            <div class="col-xxl-2" style="text-align: right">
                                <label for="is_sub"
                                       class="form-check-label">@lang('translation.submember-include')</label>
                                <input name="is_sub" type="checkbox" id="is_sub" value="1" @if ($is_sub == 1) checked
                                       @endif class="form-check-input">
                            </div>
                            <!--end col-->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="search-box">
                                    <input type="text" class="form-control search" name="searchUser" id="search-txt"
                                           value="{{$searchUser}}" placeholder="@lang('translation.userid')">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-2 col-sm-6">
                                <div>
                                    <input type="text" class="form-control" id="date-start"
                                           data-provider="flatpickr"
                                           data-date-format="Y-m-d" placeholder="Select date" name="start"
                                           data-range-date="true"
                                           value="{{$start}}">
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-xxl-1 col-sm-6">
                                <div>
                                    <a href="javascript:void(0)" class="btn btn-secondary w-100" id="btn-sumbit"><i
                                            class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                                    </a>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">@lang('translation.user')</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tree-part">
                        <div style="padding: 10px; font-size: 14px">로딩중...</div>
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->

        <div class="col-xxl-9">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" id="data-tables-part">
                        <table class="display table table-bordered table-nowrap" style="width:100%" id="data-tables">
                            <thead>
                            <tr>
                                <th>@lang('translation.date')</th>
                                <th>@lang('translation.charge-amount')</th>
                                <th>@lang('translation.excharge-amount')</th>
                                <th>@lang('translation.chexloss')</th>
                                <th>@lang('translation.betting-amount')</th>
                                <th>@lang('translation.winner-amount')</th>
                                <th>@lang('translation.rolling-amount')</th>
                                <th>@lang('translation.betting-profit')</th>
                                <th>@lang('translation.having-amount')</th>
                                <th>@lang('translation.having-rolling-amount')</th>
                            </tr>
                            <tbody>
                            <tr class="table-info">
                                <td>@lang('translation.sum')</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            </tbody>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->

        <!-- deleteRecordModal -->
        <div id="deleteRecordModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" id="close-removemodal" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-md-5">
                        <div class="text-center">
                            <div class="text-danger">
                                <i class="bi bi-trash display-5"></i>
                            </div>
                            <div class="mt-4">
                                <h4 class="mb-2">Are you sure ?</h4>
                                <p class="text-muted mx-3 mb-0">Are you sure you want to remove this record ?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 pt-2 mb-2">
                            <button type="button" class="btn w-sm btn-light btn-hover" data-bs-dismiss="modal">Close
                            </button>
                            <button type="button" class="btn w-sm btn-danger btn-hover" id="remove-element">Yes, Delete
                                It!
                            </button>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /deleteRecordModal -->
        <script>
            var gameItem = document.getElementById('gameItem');
            var gameItemChoice = new Choices(gameItem);
            var curUserId = '';

            function init(url) {
                $('#loading').show();
                $("#data-tables-part").html('');
                $.ajax({
                    method: 'get',
                    url: url,
                    success: function (ret) {
                        $('#loading').hide();
                        $("#data-tables-part").html(ret);
                    }
                });
            }

            function getDatesFromRangeDate(str) {
                var dateStart = dateEnd = '';
                var splite = str.split('to');
                if (splite.length == 2) {
                    dateStart = splite[0].trim();
                    dateEnd = splite[1].trim();
                } else {
                    dateStart = dateEnd = splite[0].trim();
                }
                return new Array(dateStart, dateEnd);
            }

            $(function () {
                $('.tree-part').on('click', '.tree-parent', function () {
                    var objData = getDatesFromRangeDate($("#date-start").val());
                    var elem = $(this);
                    $('#search-txt').val('');
                    if (!elem.hasClass('active')) {
                        $('#tree1 span.tree-parent').removeClass('active');
                        elem.addClass('active');
                        curUserId = elem.data('userid');
                        var param = 'is_sub=' + $('#is_sub').prop('checked') + '&start=' + objData[0] + '&end=' + objData[1]
                            + '&gameType=' + $('#gameType').val()
                            + '&gameItem=' + $('#gameItem').val()
                            + '&userid=' + curUserId;
                        init("{{route('stat_partner_table')}}?" + param);
                    } else {
                        curUserId = '';
                        $('#tree1 span.tree-parent').removeClass('active');
                    }
                    $("#search-txt").val(curUserId)
                });

                $('.tree-part').on('click', '.tree-symbol', function () {
                    var elem = $(this);
                    var target = $(this).next().next();
                    if (elem.hasClass('closed')) {
                        elem.removeClass('closed');
                        elem.addClass('opened');
                        target.addClass('open');
                    } else {
                        target.removeClass('open');
                        elem.removeClass('opened');
                        elem.addClass('closed');
                    }
                });
                $('.tree-part').load("{{route('stat_partner_tree')}}")
                init("{{route('stat_partner_table')}}");

                var gameType = document.getElementById("gameType");
                gameType.addEventListener(
                    'change',
                    function (event) {
                        var newValue = event.detail.value;
                        $('#seatch-txt').val('');
                        gameItemChoice.clearStore();
                        $.ajax({
                            dataType: "json",
                            data: {gameType: newValue},
                            url: "{{url('/')}}/ajax/combo/game",
                            success: function (ret) {
                                gameItemChoice.setValue(ret)
                                gameItem.addEventListener(
                                    'change',
                                    function (gameItemEvent) {
                                        var objData = getDatesFromRangeDate($("#date-start").val());
                                        var param = 'is_sub=' + $('#is_sub').prop('checked', true) + '&start=' + objData[0] + '&end=' + objData[1]
                                            + '&gameType=' + $('#gameType').val()
                                            + '&gameItem=' + gameItemEvent.detail.value
                                            + '&userid=' + curUserId;

                                        init("{{route('stat_partner_table')}}?" + param);
                                    },
                                    false,
                                )
                                {{--$('#gameItem').combobox({--}}
                                {{--    editable:false,--}}
                                {{--    panelHeight: 450,--}}
                                {{--    panelMinHeight: 140,--}}
                                {{--    data: ret,--}}
                                {{--    onChange: function (newValue, oldValue) {--}}
                                {{--        var param = 'is_sub=' + $('#is_sub').prop('checked', true) + '&start=' + $('#date-start').datebox('getValue') + '&end=' + $('#date-end').datebox('getValue')--}}
                                {{--            + '&gameType=' + $('#gameType').combobox('getValue')--}}
                                {{--            + '&gameItem=' + newValue--}}
                                {{--            + '&userid=' + curUserId;--}}

                                {{--        init("{{route('stat_partner_table')}}?" + param);--}}
                                {{--    }--}}
                                {{--});--}}
                            },
                            error: function (e) {
                                console.log(e);
                            },
                            complete: function () {
                            }
                        });
                        {{--var param = 'is_sub=' + $('#is_sub').prop('checked', true) + '&start=' + $('#date-start').datebox('getValue') + '&end=' + $('#date-end').datebox('getValue') + '&gameType=' + newValue + '&userid=' + curUserId;--}}
                        {{--init("{{route('stat_partner_table')}}?" + param);--}}
                    },
                    false,
                );

                $('#btn-sumbit').bind('click', function () {
                    curUserId = $("#search-txt").val()
                    const curUserElement = $("span[data-userid='" + curUserId + "']")
                    $(".tree-symbol").removeClass("opened")
                    $(".tree-symbol").addClass("closed")
                    $(".tree.dropdown").removeClass("open")
                    $(".tree-parent").removeClass("active")
                    $(".plus").css("display: inline-block")
                    $(".minus").css("display: none")
                    if (curUserElement.length == 1) {
                        curUserElement.addClass("active")
                        let parentElement = curUserElement.parent()
                        while (true) {
                            if (parentElement.attr('id') == "tree1")
                                break;
                            parentElement = parentElement.parent()
                            if (parentElement.hasClass("dropdown") && parentElement.hasClass("tree"))
                                parentElement.addClass("open")
                            if (parentElement[0].tagName == "LI" && !parentElement.attr("class")) {
                                let treeSymbolElement = parentElement.children(".tree-symbol").eq(0)
                                treeSymbolElement.removeClass("closed")
                                treeSymbolElement.addClass("opened");
                                treeSymbolElement.find(".plus").css({display: 'none'})
                                treeSymbolElement.find(".minus").css({display: 'inline-block !important'})
                            }
                        }
                    }
                    var objData = getDatesFromRangeDate($("#date-start").val());
                    var param = 'is_sub=' + $('#is_sub').prop('checked', true) + '&start=' + objData[0] + '&end=' + objData[1]
                        + '&gameType=' + $('#gameType').val()
                        + '&gameItem=' + $('#gameItem').val()
                        + '&userid=' + curUserId;
                    init("{{route('stat_partner_table')}}?" + param);
                });
            })
        </script>
        @endsection
        @section('custom-script')
            <script src="{{asset('/build/js/partner/treeview.js')}}"></script>
@endsection
