@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <form action="/admin/retention_rate/list" method="get" class="form-inline" enctype="multipart/form-data">
                                <div class="form-group mx-1">
                                    <label for="exampleInputEmail1" class="mx-1">{{trans('default.retentionrate_control.base_date') ?? '基準時間'}}</label>
                                    <input type="text" class="form-control" name="base_time" placeholder="name" value="{{$base_time ?? \Carbon\Carbon::now()->subDays(7)}}">
                                </div>
                                <div class="form-group mx-1">
                                    <label for="exampleInputEmail1" class="mx-1">{{trans('default.attribution_web') ?? '歸屬網站'}}</label>
                                    <select class="form-control form-control-lg" name="site_id" id="site_id">
                                        <option value="">all</option>
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
                                            aria-label="Rendering engine: activate to sort column descending">{{trans('default.retentionrate_control.base_total') ?? '當天不重複用戶'}}
                                        </th>
                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example2"
                                            rowspan="1"
                                            colspan="1" aria-sort="ascending"
                                            aria-label="Rendering engine: activate to sort column descending">{{trans('default.retentionrate_control.next_date') ?? '次日留存率'}}
                                        </th>
                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example2"
                                            rowspan="1"
                                            colspan="1" aria-sort="ascending"
                                            aria-label="Rendering engine: activate to sort column descending">{{trans('default.retentionrate_control.three_date') ?? '3日留存率'}}
                                        </th>
                                        <th class="sorting sorting_asc" tabindex="0" aria-controls="example2"
                                            rowspan="1"
                                            colspan="1" aria-sort="ascending"
                                            aria-label="Rendering engine: activate to sort column descending">{{trans('default.retentionrate_control.seven_date') ?? '7日留存率'}}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="odd">
                                        <td>{{ $data['base_total'] }}</td>
                                        <td>{{ $data['next_rate'] }} %</td>
                                        <td>{{ $data['three_rate'] }} %</td>
                                        <td>{{ $data['seven_rate'] }} %</td>
                                    </tr>
                                    </tbody>
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
            $('input[name="base_time"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                locale: {
                    format: 'YYYY-M-DD'
                }
            });
        });

    </script>
@endsection