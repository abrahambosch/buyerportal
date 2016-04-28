@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Product Lists</div>

                <div class="panel-body">
                    <div id="filters">
                        <div class="form-group{{ $errors->has('supplier') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Filter by Supplier</label>
                            <div class="col-md-6">
                                <select name="supplier" id="supplier" class="form-control">
                                    <option value="">Show all suppliers</option>
                                    @foreach ($user->suppliers as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $supplier_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('supplier'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('supplier') }}</strong>
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
                                <td>{{ $product_list->supplier->company }}</td>
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
            $("#supplier").on("change", function(e) {
                var supplier = $(this).val();
                var url = "/product/supplier/" + supplier;
                if (supplier != "") {
                    window.location.href = url;
                }
                else {
                    window.location.href = "/product";
                }

            });
        });

    </script>
@endsection
