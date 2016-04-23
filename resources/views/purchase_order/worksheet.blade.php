@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Commands </div>

                <div class="panel-body">
                    <ul class="nav nav-pills nav-stacked">
                        <li role="presentation" class="active"><a href="#">Add Products</a></li>
                        <li role="presentation"><a href="#">Profile</a></li>
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
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Worksheet </div>

                <div class="panel-body">
                    <iframe src="{{ $iframeurl }}" width="1000" height="800"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>

    </script>
@endsection
