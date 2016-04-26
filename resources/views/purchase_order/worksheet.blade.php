@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Commands </div>

                <div class="panel-body">
                    <p>Worksheet # {{ $ethercalc_id }}</p>
                    <ul class="nav nav-pills nav-stacked">
                        <li role="presentation" class="active"><a href="#">Add Products</a></li>
                        {{-- <li role="presentation"><a href="#" id="blank_sheet_btn">New Blank Sheet</a></li> --}}
                        <li role="presentation"><a href="#" id="burlington_btn">Load Burlington Template</a></li>
                        <li role="presentation"><a href="#">Messages</a></li>
                    </ul>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Chat Window </div>

                <div class="panel-body">
                    chat here ... blah blah
                </div>
            </div>

        </div>

        <div class="col-md-8">
            <iframe src="{{ $iframeurl }}" width="1000" height="800" class="pull-left"></iframe>
        </div>
    </div>
</div>
    <script>
        $("#burlington_btn").on("click", function (e){
            console.log("burlington btn pressed");
            e.preventDefault();
            $.ajax({
                dataType: "json",
                url: '{{ route("purchase_order.getNewWorksheet", ['purchase_order_id' => $purchase_order->id]) }}',
                data: {}
            }).done(function (data){
                console.log("return data", data);
            });
        });

        $("#blank_sheet_btn").on("click", function (e){
            console.log("blank_sheet_btn pressed");
            e.preventDefault();
            $.ajax({
                dataType: "json",
                url: '{{ route("purchase_order.getNewRoom", ['purchase_order_id' => $purchase_order->id]) }}',
                data: {}
            }).done(function (data){
                console.log("return data", data);
            });
        });
    </script>
@endsection
