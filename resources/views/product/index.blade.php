@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Products
                    <a href="{{ route("product.import") }}" class="pull-right"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span></a>
                </div>

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
                    @if (count($products))
                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{--<a href="{{ route("product.create") }}" class="pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>--}}</th>
                                <td><!-- image --></td>
                                @foreach ($fields as $field=>$label)
                                    <th>{{ $label }}</th>
                                @endforeach
                                <th>Supplier</th>
                                <th></th>
                            </tr>
                            </thead>

                        @foreach ($products as $product)
                            <tr>
                                <td><a href="{{ route("product.edit", ['product' => $product->product_id]) }}" class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
                                <td><img src="{{ $product->getThumbnail() }}"/></td>
                                @foreach ($fields as $field=>$label)
                                    @if (in_array($productService->getFieldType($field), ['float', 'integer']))
                                        <td>{{ number_format($product->$field, 2) }}</td>
                                    @else
                                        <td>{{ $product->$field }}</td>
                                    @endif
                                @endforeach

                                <td>{{ $product->supplier->company }}</td>
                                <td><a href="{{ route("product.delete", ['product' => $product->product_id]) }}" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
                            </tr>
                        @endforeach
                        </table>
                    @else
                        No Products Found<br>
                        <a href="{{ route("product.import") }}" class=""><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> click here to add new product. </a>
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
