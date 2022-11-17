@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <form action="/admin/icon/store" method="post" class="col-md-12" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{$model->id ?? null}}">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.name') ?? '名稱'}}</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="{{$icon_name_def ?? '請輸入入口圖標名稱'}}" value="{{$model->name ?? ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.image_profile_dec') ?? '圖片(不上傳就不更新，只接受圖片檔案(png jpeg gif))'}}</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="image" id="customFile" accept="image/png, image/gif, image/jpeg">
                                        <label class="custom-file-label" for="customFile">{{trans('default.choose_file') ?? '選擇檔案'}}</label>
                                    </div>
                                </div>
                                <div class="form-group" id="modelImage">
                                    @if(!empty($model->image_url))
                                        <img src="{{$model->image_url}}" alt="image" style="width:100px">
                                    @endif
                                </div>
                                <div class="form-group" id="selectedFiles">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.web_connect_url') ?? '連結網址'}}</label>
                                    <input type="text" class="form-control" name="url" id="url" placeholder="www.google.com" value="{{$model->url ?? ''}}">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.place') ?? '位置'}}</label>
                                    <select name="position" class="form-control form-control-lg">
                                        <option value="{{\App\Model\Icon::POSITION['top']}}" {{($model->position ?? '') == \App\Model\Icon::POSITION['top'] ? 'selected' : ''}}>
                                            {{trans('default.icon_control.location_one') ?? '站點總站'}}
                                        </option>
                                        <option value="{{\App\Model\Icon::POSITION['bottom']}}" {{($model->position ?? '') == \App\Model\Icon::POSITION['bottom'] ? 'selected' : ''}}>
                                            {{trans('default.icon_control.location_second') ?? '精品推薦'}}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{trans('default.sort_msg') ?? '排序(由左自右由上自下，數字越小越前面，最小為0，最大為225)'}}</label>
                                    <input type="text" class="form-control" name="sort" id="sort" placeholder="0" value="{{$model->sort ?? ''}}">
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
                                        <option value="{{\App\Model\Icon::EXPIRE['no']}}" {{$model->expire == \App\Model\Icon::EXPIRE['no'] ? 'selected' : ''}}>
                                            {{trans('default.take_up') ?? '上架'}}
                                        </option>
                                        <option value="{{\App\Model\Icon::EXPIRE['yes']}}" {{$model->expire == \App\Model\Icon::EXPIRE['yes'] ? 'selected' : ''}}>
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
        var selDiv = "";
        document.addEventListener("DOMContentLoaded", init, false);
        function init() {
            document.querySelector('#customFile').addEventListener('change', handleFileSelect, false);
            selDiv = document.querySelector("#selectedFiles");
        }
        function handleFileSelect(e) {
            var files = e.target.files;
            for(var i=0; i<files.length; i++) {
                var f = files[i];
                if(!f.type.match("image.*")) {
                    continue;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    var html = "<img src=\"" + e.target.result + "\" style='width:100px;'  >" ;
                    selDiv.innerHTML = html;
                }
                $('#modelImage').hide();
                reader.readAsDataURL(f);
            }
        }
    </script>

@endsection