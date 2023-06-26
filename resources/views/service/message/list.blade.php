@extends('layouts.master')
@section('title')
    @lang('translation.site-mail')
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form id="form-search" action="" method="get">
                <input type="hidden" name="sort" id="sort-arrow" value="{{$sort}}"/>
                <input type="hidden" name="type" id="sort-type" value="{{$type}}"/>
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-xxl-2 col-sm-6">
                                <label for="date_opt" class="form-label">@lang('translation.date-search')<span
                                        class="text-danger">*</span></label>
                                <select name="date_opt" class="form-control" data-choices>
                                    <option value="write" @if ($date_opt == 'write') selected @endif>작성일로</option>
                                    <option value="read" @if ($date_opt == 'read') selected @endif>읽은날짜로</option>
                                </select>
                            </div>
                            <div class="col-xxl-2 col-sm-6">
                                <label class="form-label">&nbsp;</label>
                                <input type="text" class="form-control" id="date-start"
                                       data-provider="flatpickr"
                                       data-date-format="Y-m-d" placeholder="Select date" name="startDate"
                                       data-range-date="true"
                                       value="{{$startDate}}">
                            </div>
                            <div class="col-xxl-2 col-sm-6">
                                <label for="is_read" class="form-label">읽음여부<span
                                        class="text-danger">*</span></label>
                                <select name="is_read" class="form-control" data-choices id="is_read">
                                    <option value="">전체</option>
                                    <option value="0" @if ($is_read == '0') selected @endif>안읽음</option>
                                    <option value="1" @if ($is_read == '1') selected @endif>읽음</option>
                                </select>
                            </div>
                            <div class="col-xxl-1 col-sm-6">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button class="btn btn-secondary w-100" id="search-btn">
                                        <i class="bi bi-funnel align-baseline me-1"></i> @lang('translation.filter')
                                    </button>
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
                <div class="card-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">@lang('translation.site-mail')</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('message_add_all_view')}}" class="btn btn-primary add-btn button-m JQ_POPUPW">전체쪽지</a>
                            <a href="#" id="delete-all" class="btn btn-danger add-btn button-m">전체삭제</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%" id="rate_edit_log_tbl">
                            <thead>
                            <tr>
                                <th class="username-td">최상위</th>
                                <th>받은사람</th>
                                <th>제목</th>
                                <th>최근활동일</th>
                                <th width="150px"><a href="javascript:void(0);" data-type="req"
                                       class="@if ($type == 'req') {{$sort}} @endif sort-link">작성일</a></th>
                                <th width="50px"><a href="javascript:void(0);" data-type="read"
                                       class="@if ($type == 'read') {{$sort}} @endif sort-link">읽음</a></th>
                                <th width="150px"><a href="javascript:void(0);" data-type="unread"
                                       class="@if ($type == 'unread') {{$sort}} @endif sort-link">읽은날짜</a></th>
                                <th class="actions" width="100px">기능</th>
                            </tr>
                              </thead>
                            <tbody>
                            @foreach ($msgRecord as $record)
                                <tr>
                                    <td>
                                        @php
                                            $headinfo = $record->userinfo->headinfo;
                                            if (empty($headinfo)) {
                                                $headinfo = $record->userinfo;
                                            }
                                        @endphp
                                        <span class="user-context-menu" data-user-id="{{$headinfo->id}}"
                                              data-username="{{$headinfo->userid}}">
                                            <span style='font-weight: bold;color: {{'#' . $headinfo->user_color}}'>{{$headinfo->userid}}({{$headinfo->nickname}})</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="user-context-menu" data-user-id="{{$record->userinfo->id}}"
                                              data-username="{{$record->userinfo->userid}}">
                                            <span style='font-weight: bold;color: {{'#' . $record->userinfo->user_color}}'>{{$record->userinfo->userid}}({{$record->userinfo->nickname}})</span>
                                        </span>
                                    </td>
                                    <td>{{$record->title}}</td>
                                    <td>{{$record->userinfo->request_at}}</td>
                                    <td>{{date('Y-m-d H:i:s', strtotime($record->created_at))}}</td>
                                    <td>@if ($record->is_read == 0)
                                            X
                                        @else
                                            O
                                        @endif</td>
                                    <td>
                                        @if ($record->read_at != '')
                                            {{date('Y-m-d H:i:s', strtotime($record->read_at))}}
                                        @endif
                                    </td>
                                    <td class="actions">
                                        <ul class="d-flex gap-2 list-unstyled mb-0">
                                            <li>
                                                <a href="{{route('message_view')}}?id={{$record->id}}" class="JQ_POPUP btn btn-subtle-primary btn-icon btn-sm "><i class="ph-eye"></i></a>
                                            </li>
                                            <li>
                                                <a href="{{route('message_send_view')}}?userid={{$record->userid}}" class="JQ_POPUP btn btn-subtle-secondary btn-icon btn-sm edit-item-btn"><i class="ph-pencil"></i></a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" data-id="{{$record->id}}"  class="delete-message btn btn-subtle-danger btn-icon btn-sm remove-item-btn"><i class="ph-trash"></i></a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($msgRecord->count() == 0)
                                <tr>
                                    <td colspan="20">@lang('translation.nohistory')</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div class="pagination-part" style="text-align: center;">
                            {!! $msgRecord->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
       $(function(){
           $("#delete-all").click(function() {
               if (confirm('정말 전체삭제 하시겟습니까?')) {
                   location.href = "{{route('message_del_all')}}";
               }
           });

           $('#rate_edit_log_tbl').on('click', '.delete-message', function() {
               if (confirm('정말 삭제 하시겟습니까?')) {
                   var elem = $(this);
                   $("#myModal").show();
                   location.href = "{{route('message_del_proc')}}?id=" + elem.data('id');
               }
           });
           $('.sort-link').bind('click', function() {
               var elem = $(this);
               if (elem.hasClass('asc')) {
                   $('#sort-arrow').val('desc');
                   $('#sort-type').val(elem.data('type'));
               }
               else {
                   $('#sort-arrow').val('asc');
                   $('#sort-type').val(elem.data('type'));
               }
               $("#form-search").submit();
           });

           $('#btn-sumbit').bind('click', function() {
               $("#form-search").submit();
           });
       })
    </script>
@endsection
