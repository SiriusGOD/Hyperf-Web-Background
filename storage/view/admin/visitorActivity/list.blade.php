@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <form action="/admin/visitor_activity/list" method="get" class="form-inline" enctype="multipart/form-data">
                                <div class="form-group mx-1">
                                    <label for="exampleInputEmail1" class="mx-1">{{trans('default.start_time') ?? '開始時間'}}</label>
                                    <input type="text" class="form-control" name="start_time" placeholder="name" value="{{$start_time ?? \Carbon\Carbon::yesterday()}}">
                                </div>
                                <div class="form-group mx-1">
                                    <label for="exampleInputEmail1" class="mx-1">{{trans('default.end_time') ?? '結束時間'}}</label>
                                    <input type="text" class="form-control" name="end_time" id="name" placeholder="name" value="{{$end_time ?? \Carbon\Carbon::now()}}">
                                </div>
                                <div class="form-group mx-1">
                                    <label for="exampleInputEmail1" class="mx-1">{{trans('default.attribution_web') ?? '歸屬網站'}}</label>
                                    <select class="form-control form-control-lg" name="site_id">
                                        <option value=""></option>
                                        @foreach(make(\App\Service\SiteService::class)->getSiteModels() as $site)
                                            <option value="{{$site->id}}" {{($site_id ?? '') == $site->id ? 'selected' : ''}}>
                                                {{$site->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary mx-1">{{trans('default.submit') ?? '送出'}}</button>
                            </form>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="example2" class="table table-bordered table-hover dataTable dtr-inline"
                                       aria-describedby="example2_info">
                                    <thead>
                                    <tr>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Browser: activate to sort column ascending">{{trans('default.date') ?? '日期'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Engine version: activate to sort column ascending">{{trans('default.visit_count') ?? '造訪數'}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($models as $model)
                                        <tr class="odd">
                                            <td class="sorting_1 dtr-control">{{ $model->visit_date}}</td>
                                            <td>{{ $model->total}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th rowspan="1" colspan="1">{{trans('default.date') ?? '日期'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.visit_count') ?? '造訪數'}}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
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
                showDropdowns: true,
                locale: {
                    format: 'YYYY-M-DD'
                }
            });
            $('input[name="end_time"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'YYYY-M-DD'
                }
            });
        });
    </script>
@endsection