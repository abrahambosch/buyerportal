@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">{{ $seller->company }} Product Lists
                    <a href="{{ route("product.import") }}" class="pull-right"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span></a>
                </div>

                <div class="panel-body">
                    <div id="filters">
                        <div class="form-group{{ $errors->has('seller') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Filter by Buyer</label>
                            <div class="col-md-6">
                                <select name="buyer" id="buyer" class="form-control">
                                    <option value="">Show all Buyers</option>
                                    @foreach ($seller->users as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $buyer_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('seller'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('buyer') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if (count($lists))
                        <table class="table">
                            <thead>
                            <tr>
                                <th><a href="{{ route("product_list.create") }}" class="pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Company</th>
                            </tr>
                            </thead>

                        @foreach ($lists as $product_list)
                            <tr>
                                <td><a href="{{ route("product_list.edit", ['product_list' => $product_list->id]) }}" class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
                                <td>{{ $product_list->id }}</td>
                                <td>{{ $product_list->list_name }}</td>
                                <td>{{ $product_list->seller->company }}</td>
                            </tr>
                        @endforeach
                        </table>
                    @else
                        No Product Lists Found<br>
                        <a href="{{ route("product_list.create") }}" class=""><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> click here to add new product list. </a>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(function(){
            $("#seller").on("change", function(e) {
                var seller = $(this).val();
                var url = "/product/seller/" + seller;
                if (seller != "") {
                    window.location.href = url;
                }
                else {
                    window.location.href = "/product";
                }

            });
        });

    </script>
@endsection
