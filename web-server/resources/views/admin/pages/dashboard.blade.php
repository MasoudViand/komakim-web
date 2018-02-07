@extends('admin.template.admin_template')

@section('content')

    @if(Session::has('error'))
        <div class="alert alert-danger" role="alert">
            <strong>
                {{ Session::get('error') }}
            </strong>
        </div>
    @endif
    @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            <strong>
                {{ Session::get('success') }}
            </strong>
        </div>
    @endif
    <div class='row'>
        <div class="col-sm-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                <thead>
                <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">تعداد سفارشات امروز </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">تعداد سفارشات دیروز</th>
                </tr>
                </thead>
                <tbody>

                    <tr role="row" class="odd">
                        <td class="sorting_1">{{key_exists('today',$orders)?$orders['today']:0}}</td>
                        <td class="sorting_1">{{key_exists('yesterday',$orders)?$orders['yesterday']:0}}</td>

                    </tr>


                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                <thead>
                <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">درامد ناخالص امروز </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">درامد ناخالص دیروز</th>
                </tr>
                </thead>
                <tbody>

                <tr role="row" class="odd">
                    <td class="sorting_1">{{key_exists('today',$financials)?$financials['today']:0}}</td>
                    <td class="sorting_1">{{key_exists('yesterday',$financials)?$financials['yesterday']:0}}</td>

                </tr>


                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <table id="example1" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
                <thead>
                <tr role="row">
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">تعداد کابران جدید امروز </th>
                    <th class="sorting_asc" tabindex="0" aria-controls="example1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Rendering engine: activate to sort column descending" style="width: 162px;">تعداد کابران جدید دیروز</th>
                </tr>
                </thead>
                <tbody>

                <tr role="row" class="odd">
                    <td class="sorting_1">{{key_exists('today',$users)?$users['today']:0}}</td>
                    <td class="sorting_1">{{key_exists('yesterday',$users)?$users['yesterday']:0}}</td>

                </tr>


                </tbody>
            </table>
        </div>

    </div>
@endsection