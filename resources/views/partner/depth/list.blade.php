@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                회원 단계관리
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-12">
                    <a href="{{route('partner_depth_add')}}" class="btn btn-outline-secondary btn-sm">새 단계추가</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="display table table-bordered table-nowrap" style="width:100%">
                            <thead>
                            <tr>
                                <th>NO</th>
                                <th>이름</th>
                                <th class="actions">기능</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $index = 1;
                            @endphp
                            @foreach ($depthRecord as $record)
                                <tr>
                                    <td class="text-center">{{$index++}}&nbsp;</td>
                                    <td class="text-center">{{$record->step_name}}&nbsp;</td>
                                    <td class="text-center">
                                        <a href="{{route('partner_depth_edit')}}?id={{$record->id}}" class="btn btn-outline-secondary btn-sm">수정</a>
                                        <a href="javascript:delDepth({{$record->id}})"  class="btn btn-outline-secondary btn-sm">삭제</a>
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
    <script type="text/javascript">
        function delDepth(id) {
            if(confirm('선택한 항목을 삭제하시겠습니까?')) {
                location.href = '{{route('partner_depth_del')}}?id=' + id;
            }
        }
    </script>
@endsection
