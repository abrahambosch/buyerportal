@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Products
                    <a title="Product Import" href="{{ route("supplier_product.import") }}" class="pull-right"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span></a>
                    <a title="Image Import" href="{{ route("supplier_product.image_import") }}" class="pull-right"><span class="glyphicon glyphicon-import" aria-hidden="true"></span></a>
                </div>

                <div class="panel-body">
                    <div id="filters">
                        <div class="form-group{{ $errors->has('supplier') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Filter by Buyer</label>
                            <div class="col-md-6">
                                <select name="buyer" id="buyer" class="form-control">
                                    <option value="">Show all buyers</option>
                                    @foreach ($supplier->users as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $buyer_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
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
                                <th><a href="{{ route("supplier_product.create") }}" class="pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
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
                                <td><a href="{{ route("supplier_product.edit", ['product' => $product->product_id]) }}" class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
                                <td><img src="{{ $product->getThumbnail() }}"/></td>
                                @foreach ($fields as $field=>$label)
                                    @if (in_array($productService->getFieldType($field), ['float', 'integer']))
                                        <td>{{ number_format($product->$field, 2) }}</td>
                                    @else
                                        <td>{{ $product->$field }}</td>
                                    @endif
                                @endforeach
                                <td>{{ $product->supplier->company }}</td>
                                <td><a href="{{ route("supplier_product.delete", ['product' => $product->product_id]) }}" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
                            </tr>
                        @endforeach
                        </table>
                    @else
                        No Products Found<br>
                        <a href="{{ route("supplier_product.create") }}" class=""><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> click here to add new product. </a>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(function(){
            $("#buyer").on("change", function(e) {
                var buyer = $(this).val();
                var url = "/supplier_product/buyer/" + buyer;
                if (buyer != "") {
                    window.location.href = url;
                }
                else {
                    window.location.href = "/supplier_product";
                }

            });
        });

    </script>
@endsection
