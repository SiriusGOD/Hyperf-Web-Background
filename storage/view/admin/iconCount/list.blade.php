@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <form action="/admin/icon_count/list" method="get" class="form-inline" enctype="multipart/form-data">
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
                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example2"
                                            rowspan="1"
                                            colspan="1" aria-sort="ascending"
                                            aria-label="Rendering engine: activate to sort column descending">{{trans('default.id') ?? '序號'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Browser: activate to sort column ascending">{{trans('default.name') ?? '名稱'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Engine version: activate to sort column ascending">
                                            {{trans('default.click_time') ?? '點擊時間'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Engine version: activate to sort column ascending">
                                            {{trans('default.click_count') ?? '點擊數'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Engine version: activate to sort column ascending">
                                            {{trans('default.attribution_web') ?? '歸屬網站'}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($datas as $model)
                                        <tr class="odd">
                                            <td class="sorting_1 dtr-control">{{ $model->id}}</td>
                                            <td>{{ $model->icon->name}}</td>
                                            <td>{{ $model->date}}</td>
                                            <td>{{ $model->count}}</td>
                                            <td>{{ $model->site->name ?? trans('default.unattribution_web')}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th rowspan="1" colspan="1">{{trans('default.id') ?? '序號'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.name') ?? '名稱'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.click_time') ?? '點擊時間'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.click_count') ?? '點擊數'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.attribution_web') ?? '歸屬網站'}}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                {{trans('default.table_page_info',[
                                        'page' => $page,
                                        'total' => $total,
                                        'last_page' => $last_page,
                                        'step' => $step,
                                    ]) ?? '顯示第 $page 頁
                                    共 $total 筆
                                    共 $last_page 頁
                                    每頁顯示 $step 筆'}}
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    <ul class="pagination">
                                        <li class="paginate_button page-item previous {{$page <= 1 ? 'disabled' : ''}}" id="example2_previous">
                                            <a href="{{$prev}}"
                                               aria-controls="example2" data-dt-idx="0" tabindex="0"
                                               class="page-link">{{trans('default.pre_page') ?? '上一頁'}}</a>
                                        </li>
                                        <li class="paginate_button page-item next {{$last_page <= $page  ? 'disabled' : ''}}" id="example2_next">
                                            <a href="{{$next}}"
                                               aria-controls="example2"
                                               data-dt-idx="7"
                                               tabindex="0"
                                               class="page-link">{{trans('default.next_page') ?? '下一頁'}}</a>
                                        </li>
                                    </ul>



                                </div>
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