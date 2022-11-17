@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <form action="/admin/share_count/index" method="get" class="form-inline" enctype="multipart/form-data">
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
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->


        </div>
        <!-- /.col -->
    </div>

    <script>
        const labels = @json($labels);

        const data = {
            labels: labels,
            datasets: [{
                label: "{{trans('default.sharecount_control.sharecount_chart_label') ?? '每日分享功能點擊數'}}",
                borderColor: 'rgb(75, 192, 192)',
                fill: false,
                data: @json($data),
            }]
        };

        const config = {
            type: 'line',
            data: data,
            options: {}
        };

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

            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
        });
    </script>
@endsection