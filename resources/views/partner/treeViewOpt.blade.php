@extends('layouts.master-without-nav')
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">파트너 구조 표시 옵션</h4>
        </div>
        <div class="card-body">
            <form action="" novalidate="novalidate" autocomplete="off" id="UserTreeViewOptForm" method="post"
                  accept-charset="utf-8">
                @csrf
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="view_allot_rate" id="UserViewAllotRate_" value="0"/>
                                <input type="checkbox" name="view_allot_rate" class="form-check-input" value="1" id="UserViewAllotRate"
                                       <?= ($view_allot_rate == 1 ? 'checked' : '') ?>
                                       role="switch"/>
                                <label for="UserViewAllotRate" class="form-check-label">응답알림쪽지보내기</label>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="view_comm_rate" id="UserViewCommRate_" value="0"/>
                                <input type="checkbox" name="view_comm_rate" class="form-check-input" value="1" id="UserViewCommRate"
                                       <?=($view_comm_rate == 1 ? 'checked' : '')?>
                                       role="switch"/>
                                <label for="UserViewCommRate" class="form-check-label">배수/당수율 표시</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-secondary btn-sm" type="submit">적용하기</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
