@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">FAQ</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                        <h3>شرایط همکاری با ما</h3>


                        <div>
                            {!! $workWithUsCondition->value or 'تعیین نشده'!!}
                        </div>



                </div>
            </div>
        </div>
    </div>
</div>
@endsection
