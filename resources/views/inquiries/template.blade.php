@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <div class="flex-grow-1">
                <h5 class="card-title mb-3">1:1문의 템플릿 관리</h5>
            </div>
            <div class="flex-shrink-0">
                <a href="{{route('inquiries_templets_add')}}" class="btn btn-primary">새 템플릿</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display table table-bordered table-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>제목</th>
                            <th style="width: 140px">작성일</th>
                            <th class="actions" style="width: 50px">기능</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($tempRecord as $record)
                        @php
                            $randform = rand(100000, 9999999);
                        @endphp
                        <tr>
                            <td style="text-align: left;">{{$record->title}}</td>
                            <td>{{date('m-d H:i', strtotime($record->created_at))}}</td>
                            <td class="actions">
                                <ul class="d-flex gap-2 list-unstyled mb-0">
                                    <li>
                                        <a href="{{route('inquiries_templets_edit')}}?id={{$record->id}}" class="btn btn-subtle-primary btn-icon btn-sm "><i class="ph-pencil"></i></a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="if (confirm('선택한 템플렛을 삭제하시겟습니까?')) { document.post_5f361e9d2a<?=$randform?>.submit(); } event.returnValue = false; return false;"  class="btn btn-subtle-danger btn-icon btn-sm remove-item-btn"><i class="ph-trash"></i></a>
                                    </li>
                                </ul>
                                <form action="{{route('inquiries_templets_del')}}?id={{$record->id}}" name="post_5f361e9d2a<?=$randform?>" id="post_5f361e9d2a<?=$randform?>" style="display:none;" method="post">
                                    @csrf
                                    <input type="hidden" name="_method" value="POST" />
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if ($tempRecord->count() == 0)
                        <tr>
                            <td colspan="20">자료가 없습니다</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
