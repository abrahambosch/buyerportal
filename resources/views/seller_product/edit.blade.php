@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Product</div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-3">
                            <img src="{{ $product->getFeaturedImageUrl() }}" width="200"/>
                        </div>
                        <div class="col-md-9">
                            <form action="{{ route("seller_product.update", ['product' => $product->product_id]) }}" method="POST" class="form-horizontal">
                                {{ method_field('PUT') }}
                                {!! csrf_field() !!}


                                @foreach (['product_name' => 'Name', 'product_description' => 'Description', 'sku' => 'SKU', 'upc'=>'UPC', 'style' => 'Vendor Style Number', 'gtin' => 'GTIN', 'cost' => 'Cost', 'price' => 'Price'] as $field => $label)
                                    <div class="form-group{{ $errors->has($field) ? ' has-error' : '' }}">
                                        <label class="col-md-4 control-label">{{ $label }}</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="{{ $field }}" value="{{ $product->$field }}">

                                            @if ($errors->has($field))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first($field) }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <div class="form-group{{ $errors->has('seller_id') ? ' has-error' : '' }} clearfix">
                                    <label class="col-md-4 control-label">Supplier</label>
                                    <div class="col-md-6">
                                        <select name="seller_id" id="seller_id" class="form-control">
                                            <option value="">No Supplier</option>
                                            @foreach ($user->sellers as $s)
                                                <option value="{{ $s->id }}" @if ($s->id == $product->seller_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('seller_id'))
                                            <span class="help-block">
                                                    <strong>{{ $errors->first('seller_id') }}</strong>
                                                </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="task" class="col-sm-3 control-label"></label>
                                    <div class="col-sm-6">
                                        <input type="submit" value="Save"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row">
                        <!-- images -->
                        @foreach ($product->images as $i)
                            <div class="col-md-3 col-sm-4 col-xs-6"><img class="img-responsive" src="{{ $i->url }}"/></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
