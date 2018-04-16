@extends('client.template.client_template')


@section('content')
    <div class="container">

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">



                    <div class="alert alert-danger" role="alert">
                        <strong>
                            {{ $error or '' }}
                        </strong>
                    </div>

                </div>


            </div>
        </div>
    </div>
    <script>
        var form = document.createElement("form");
        form.setAttribute("method", "POST");
        form.setAttribute("action", "https://sep.shaparak.ir/Payment.aspx");
        form.setAttribute("target", "_self");
                @if(empty($error))
        var params = {
            Amount: '{{$amount}}',
            MID: '10917062',
            ResNum: '{{$order_id}}',
            RedirectURL: '{{URL::to('/').'/client/callback'}}',
        };

         @endif

        for(var key in params){

            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }


        document.body.appendChild(form);

        form.submit();

        document.body.removeChild(form);
    </script>
@endsection








