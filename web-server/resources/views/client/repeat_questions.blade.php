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

                        <h3>سوالات متداول</h3>

                        <ul>
                            @foreach($repeadQuestions as $item)

                               <li><a href="#{{$item->id}}">{{$item->question}}</a> </li>

                            @endforeach
                        </ul>


                        <div>
                        @foreach($repeadQuestions as $item)
                                <div id="{{$item->id}}">

                            <h3>{{$item->question}}</h3>
                            <br>

                                <a name="{{$item->id}}"></a>

                                {{$item->answer}}
                            </div>

                        @endforeach
                    </div>


                        <div id="about"></div>




                </div>
            </div>
        </div>
    </div>
</div>
@endsection
