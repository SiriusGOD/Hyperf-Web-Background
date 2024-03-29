@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            @if(authPermission('share-create'))
{{--                           <div class="col-sm-12 col-md-12 mb-1">--}}
{{--                               <a class="btn badge-info" href="/admin/share/create">{{trans('default.share_control.share_insert') ?? '新增分享代碼'}}</a>--}}
{{--                           </div>--}}
                            @endif

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
                                            aria-label="Rendering engine: activate to sort column descending">
                                            {{trans('default.id') ?? '序號'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Engine version: activate to sort column ascending">
                                            {{trans('default.share_control.share_code') ?? '分享代碼'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Engine version: activate to sort column ascending">
                                            {{trans('default.status') ?? '狀態'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="Engine version: activate to sort column ascending">
                                            {{trans('default.attribution_web') ?? '歸屬網站'}}
                                        </th>
                                        <th class="sorting" tabindex="0" aria-controls="example2" rowspan="1"
                                            colspan="1"
                                            aria-label="CSS grade: activate to sort column ascending">{{trans('default.action') ?? '動作'}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($datas as $model)
                                        <tr class="odd">
                                            <td class="sorting_1 dtr-control">{{ $model->id}}</td>
                                            <td><a href="{{$share_url_prefix}}?site_id={{$model->site_id}}&share_code={{$model->code}}">{{ $model->code}}</a></td>
                                            <td>{{ $model->status == \App\Model\Share::STATUS['undone'] ? trans('default.status_one') : trans('default.status_second')}}</td>
                                            <td>{{ $model->site->name ?? trans('default.unattribution_web')}}</td>
                                            <td>
                                                @if(authPermission('share-status'))
                                                <div class="row mb-1">
                                                    <form action="/admin/share/status" method="post">
                                                        <input type="hidden" name="id" value="{{$model->id}}" >
                                                        <input type="hidden" name="status" value="{{\App\Model\Share::STATUS['undone']}}" >
                                                        <input type="submit"  class="btn btn-danger" value="{{trans('default.change_status_fail') ?? '改為未完成'}}">
                                                    </form>
                                                </div>

                                                <div class="row mb-1">
                                                    <form action="/admin/share/status" method="post">
                                                        <input type="hidden" name="id" value="{{$model->id}}" >
                                                        <input type="hidden" name="status" value="{{\App\Model\Share::STATUS['done']}}" >
                                                        <input type="submit"  class="btn btn-danger" value="{{trans('default.change_status_true') ?? '改為已完成'}}">
                                                    </form>
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th rowspan="1" colspan="1">{{trans('default.id') ?? '序號'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.share_control.share_code') ?? '分享代碼'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.status') ?? '狀態'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.attribution_web') ?? '歸屬網站'}}</th>
                                        <th rowspan="1" colspan="1">{{trans('default.action') ?? '動作'}}</th>
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
                                        <li class="paginate_button page-item next {{$last_page <= $page ? 'disabled' : ''}}" id="example2_next">
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

@endsection