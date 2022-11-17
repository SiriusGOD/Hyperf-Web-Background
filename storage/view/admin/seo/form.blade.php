@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <form action="/admin/seo/store" method="post" class="col-md-12" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{$model->id ?? null}}">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.seo_control.seo_keywords') ?? '關鍵字(用、區隔)'}}</label>
                                    <input type="text" class="form-control" name="keywords" id="name" placeholder="{{trans('default.seo_control.seo_keywords_def') ?? '請輸入關鍵字，EX:二次元、自拍'}}" value="{{$model->keywords ?? ''}}">
                                </div>
                                @include("partial.admin.siteSelect")
                                <button type="submit" class="btn btn-primary">{{trans('default.submit') ?? '送出'}}</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->


        </div>
        <!-- /.col -->
    </div>

@endsection