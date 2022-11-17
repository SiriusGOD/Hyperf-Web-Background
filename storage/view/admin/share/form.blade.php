@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <form action="/admin/share/store" method="post" class="col-md-12" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.ip') ?? 'ip'}}</label>
                                    <input type="text" class="form-control" name="ip" id="ip" placeholder="{{trans('default.ip_msg_def') ?? '請輸入 ip'}}" value="{{$model->ip ?? ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.status') ?? '狀態'}}</label>
                                    <select class="form-control form-control-lg" name="status">
                                        <option value="{{\App\Model\Share::STATUS['undone']}}" >
                                        {{trans('default.status_one') ?? '未完成'}}
                                        </option>
                                        <option value="{{\App\Model\Share::STATUS['done']}}">
                                        {{trans('default.status_second') ?? '已完成'}}
                                        </option>
                                    </select>
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