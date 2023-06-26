@extends('layouts.master')
@section('title')
    @lang('translation.board-management')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-xxl-8 col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">@lang('translation.board-management')</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('board_add_view')}}" class="btn btn-primary add-btn button-m JQ_POPUPW"><i class="bi bi-plus-circle align-baseline me-1"></i>@lang('translation.Send')</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap basic-list" style="width:100%">
                            <thead>
                                <tr>
                                    <th>@lang('translation.title')</th>
                                    <th width="200px">@lang('translation.domain')</th>
                                    <th width="50px">@lang('translation.popup')</th>
                                    <th width="100px">@lang('translation.nondisclosure')</th>
                                    <th width="100px">@lang('translation.date-created')</th>
                                    <th width="100px">@lang('translation.function')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($boardRecord as $record)
                                <tr>
                                    <td>{{$record->title}}&nbsp;</td>
                                    <td>{{$record->info->domain ?? '-'}}</td>
                                    <td>@if ($record->is_banner == 1) Y @else N @endif&nbsp;</td>
                                    <td>@if ($record->is_delete == 1) Y @else N @endif&nbsp;</td>
                                    <td>{{$record->created_at}}&nbsp;</td>
                                    <td class="actions">
                                        <ul class="d-flex gap-2 list-unstyled mb-0">
                                            <li>
                                                <a href="{{route('board_edit_view')}}?id={{$record->id}}" class="JQ_POPUPW link-success btn btn-subtle-secondary btn-icon btn-sm edit-item-btn"><i class="ph-pencil"></i></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" data-id='{{$record->id}}' class="delete-btn link-danger btn btn-subtle-danger btn-icon btn-sm remove-item-btn"><i class="ph-trash"></i></a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            @if (count($boardRecord) == 0)
                                <tr>
                                    <td colspan="20">@lang('translation.nohistory')</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        @if (count($boardRecord) > 0)
                            <div class="pagination-part">
                                {!! $boardRecord->withQueryString()->links() !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        {{--$('.basic-list').on('click', '.delete-board', function() {--}}
        {{--    if (confirm('정말 삭제하시겟습니까?'))--}}
        {{--    {--}}
        {{--        $("#myModal").show();--}}
        {{--        var elem = $(this);--}}
        {{--        location.href = '{{route('board_del_proc')}}?id=' + elem.data('id');--}}
        {{--    }--}}
        {{--});--}}
        var deletes = '@lang('translation.delete')';
        var cancels = '@lang('translation.cancel')';
        var backend_del_url =' {{route('board_del_proc')}}';
    </script>
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/sweetalerts.init.js') }}"></script>
@endsection
