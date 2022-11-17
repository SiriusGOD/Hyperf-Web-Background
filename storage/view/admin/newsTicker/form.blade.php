@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <form action="/admin/news_ticker/store" method="post" class="col-md-12" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{$model->id ?? null}}">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.name') ?? '名稱'}}</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="{{trans('default.ad_control.ad_input_name') ?? '請輸入廣告名稱'}}" value="{{$model->name ?? ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.content') ?? '內容'}}</label>
                                    <input type="text" class="form-control" name="detail" id="detail" placeholder="{{trans('default.newsticker_control.newsticker_content_def') ?? '我是跑馬燈'}}" value="{{$model->detail ?? ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.start_time') ?? '開始時間'}}</label>
                                    <input type="text" class="form-control" name="start_time" placeholder="name" value="{{$model->start_time ?? \Carbon\Carbon::now()}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.end_time') ?? '結束時間'}}</label>
                                    <input type="text" class="form-control" name="end_time" id="name" placeholder="name" value="{{$model->end_time ?? \Carbon\Carbon::now()}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.buyer') ?? '購買人'}}</label>
                                    <input type="text" class="form-control" name="buyer" id="name" placeholder="{{trans('default.buyer_msg') ?? '請輸入廣告購買人名稱'}}" value="{{$model->buyer ?? ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.take_up_down_info') ?? '上下架情況(任意時間均可下架，上架需在結束時間以前)'}}</label>
                                    <select class="form-control form-control-lg" name="expire">
                                        <option value="{{\App\Model\NewsTicker::EXPIRE['no']}}" {{$model->expire == \App\Model\NewsTicker::EXPIRE['no'] ? 'selected' : ''}}>
                                            {{trans('default.take_up') ?? '上架'}}
                                        </option>
                                        <option value="{{\App\Model\NewsTicker::EXPIRE['yes']}}" {{$model->expire == \App\Model\NewsTicker::EXPIRE['yes'] ? 'selected' : ''}}>
                                            {{trans('default.take_down') ?? '下架'}}
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

    <script>
        $(function() {
            $('input[name="start_time"]').daterangepicker({
                singleDatePicker: true,
                timePicker:true,
                timePicker24Hour: true,
                showDropdowns: true,
                locale: {
                    format: 'YYYY-M-DD HH:mm:00'
                }
            });
            $('input[name="end_time"]').daterangepicker({
                singleDatePicker: true,
                timePicker:true,
                timePicker24Hour: true,
                showDropdowns: true,
                locale: {
                    format: 'YYYY-M-DD HH:mm:00'
                }
            });
        });
    </script>

@endsection