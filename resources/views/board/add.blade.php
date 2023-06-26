@extends('layouts.master-without-nav')
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                @php
                    if (\Session::has('form-board')) {
                        $boardinfo = Session::get('form-board');
                    }
                @endphp
                @if (\Session::has('result'))
                    @if (\Session::get('result'))
                        <div id="flashMessage" class="message success" style=''>글쓰기 성공!</div>
                    @else
                        <div id="flashMessage" class="message error" style=''>글쓰기 실패!</div>
                    @endif
                @endif
                <form action="" autocomplete="off" id="PostAddForm" method="post" accept-charset="utf-8">
                    @csrf
                    <input type="hidden" name="is_alert" id="PostIsAlert_" value="0">
                    <input type="hidden" name="is_banner" id="PostIsBanner_" value="0">
                    <input type="hidden" name="is_delete" id="PostIsDelete_" value="0">
                    <input type="hidden" name="hide_title" id="PostHideTitle_" value="0">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <label for="PostTitle" class="form-label">@lang('translation.title')<span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="PostTitle" required name="title"
                                   value="{{$boardinfo['title'] ?? ''}}">
                        </div>
                        <div class="col-6 d-flex align-items-center gap-3">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="PostIsAlert"
                                       name="is_alert" value="1"
                                       @if (($boardinfo['is_alert'] ?? '') == 1) checked @endif>
                                <label class="form-check-label" for="PostIsAlert">상단표시고정</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="PostIsBanner"
                                       value="1" name="is_banner"
                                       @if (($boardinfo['is_banner'] ?? '') == 1) checked @endif>
                                <label class="form-check-label" for="PostIsBanner">팝업</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="PostIsDelete"
                                       value="1" name="is_delete"
                                       @if (($boardinfo['is_delete'] ?? '') == 1) checked @endif>
                                <label class="form-check-label" for="PostIsDelete">비공개</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch" id="PostHideTitle"
                                       value="1" name="hide_title"
                                       @if (($boardinfo['hide_title'] ?? '') == 1) checked @endif>
                                <label class="form-check-label" for="PostHideTitle">제목현시안함</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        @if (count($domainRecord) > 0)
                            <div class="col-sm-2">
                                <label for="domain" class="form-label">@lang('translation.domain')<span
                                        class="text-danger">*</span></label>
                                <select class="form-control" data-choices name="domain" id="domain">
                                    <option value="0">전체</option>
                                    @foreach ($domainRecord as $record)
                                        <option value="{{$record->id}}"
                                                @if (($boardinfo['domain'] ?? '') == $record->domain) selected @endif>{{$record->domain}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-sm-3">
                            <label class="form-label">팝업크기<span
                                    class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="number" name="pos_width" value="{{$boardinfo['pos_width'] ?? 300}}"
                                           class="form-control"/>
                                </div>
                                <div class="col-sm-6">
                                    <input type="number" name="pos_height" value="{{$boardinfo['pos_height'] ?? 500}}"
                                           class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">팝업위치<span
                                    class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="number" name="pos_top" value="{{$boardinfo['pos_top'] ?? 100}}"
                                           class="form-control"/>
                                </div>
                                <div class="col-sm-6">
                                    <input type="number" name="pos_left"
                                           value="{{$boardinfo['pos_left'] ?? 100}}" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-primary" type="submit">저장</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @if (!str_contains($boardinfo['contents'] ?? '', 'iframe'))
                                <textarea name="contents" id="contents" class="ckeditor-classic">{{$boardinfo['contents'] ?? ''}}</textarea>
                            @else
                                동영상 자료는 편집불가입니다.
                                <textarea name="contents" id="contents" class="ckeditor-classic">{{$boardinfo['contents'] ?? ''}}</textarea>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ckeditor -->
    <script src="{{ URL::asset('build/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

    <script>
        class MyUploadAdapter {
            constructor( loader ) {
                this.loader = loader;
            }

            upload() {
                return this.loader.file
                    .then( file => new Promise( ( resolve, reject ) => {
                        this._initRequest();
                        this._initListeners( resolve, reject, file );
                        this._sendRequest( file );
                    } ) );
            }

            abort() {
                if ( this.xhr ) {
                    this.xhr.abort();
                }
            }

            _initRequest() {
                const xhr = this.xhr = new XMLHttpRequest();

                xhr.open( 'POST', "{{route('post.image', ['_token' => csrf_token() ])}}", true );
                xhr.responseType = 'json';
            }

            _initListeners( resolve, reject, file ) {
                const xhr = this.xhr;
                const loader = this.loader;
                const genericErrorText = `Couldn't upload file: ${ file.name }.`;

                xhr.addEventListener( 'error', () => reject( genericErrorText ) );
                xhr.addEventListener( 'abort', () => reject() );
                xhr.addEventListener( 'load', () => {
                    const response = xhr.response;

                    if ( !response || response.error ) {
                        return reject( response && response.error ? response.error.message : genericErrorText );
                    }

                    resolve( response );
                } );

                if ( xhr.upload ) {
                    xhr.upload.addEventListener( 'progress', evt => {
                        if ( evt.lengthComputable ) {
                            loader.uploadTotal = evt.total;
                            loader.uploaded = evt.loaded;
                        }
                    } );
                }
            }

            _sendRequest( file ) {
                const data = new FormData();

                data.append( 'upload', file );

                this.xhr.send( data );
            }
        }

        function MyCustomUploadAdapterPlugin( editor ) {
            editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
                return new MyUploadAdapter( loader );
            };
        }
        // ckeditor
        var ckClassicEditor = document.querySelectorAll(".ckeditor-classic")
        if (ckClassicEditor) {
            Array.from(ckClassicEditor).forEach(function () {
                ClassicEditor
                    .create(document.querySelector('.ckeditor-classic') , {
                        extraPlugins: [ MyCustomUploadAdapterPlugin ],
                    })
                    .then(function (editor) {
                        editor.ui.view.editable.element.style.height = '180px';
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            });
        }

    </script>
@endsection
