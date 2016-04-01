@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Product List
                    <a href="{{ route("seller_product_list.image_import", ['id' => $product_list->id]) }}" class="pull-right"><span class="glyphicon glyphicon-import" aria-hidden="true"></span></a>
                </div>

                <div class="panel-body">
                    <form action="{{ route("product_list.update", ['id' => $product_list->id]) }}" method="POST" class="form-horizontal">
                        {{ method_field('PUT') }}
                        {!! csrf_field() !!}


                        @foreach (['list_name' => 'List Name'] as $field => $label)
                            <div class="form-group{{ $errors->has($field) ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">{{ $label }}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="{{ $field }}" value="{{ $product_list->$field }}">

                                    @if ($errors->has($field))
                                        <span class="help-block">
                                            <strong>{{ $errors->first($field) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Buyer</label>
                            <div class="col-md-6">
                                <select name="user_id" id="user_id" class="form-control">
                                    <option value="">No Buyer</option>
                                    @foreach ($seller->users as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $product_list->user_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('user_id'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('user_id') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="task" class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                {{-- <input type="submit" value="Save"/> --}}
                            </div>
                        </div>

                        <div>
                            @if (count($product_list->items))
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th><!-- image column --></th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>UPC</th>
                                        <th>SKU</th>
                                        <th>Cost</th>
                                        <th>Price</th>
                                        <th>Seller</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    @foreach ($product_list->items as $item)
                                        <tr>
                                            <td></td>
                                            <td><img src="{{ $item->product->getThumbnail() }}"/></td>
                                            <td>{{ $item->product->product_name }}</td>
                                            <td>{{ $item->product->product_description }}</td>
                                            <td>{{ $item->product->upc }}</td>
                                            <td>{{ $item->product->sku }}</td>
                                            <td>{{ $item->product->cost }}</td>
                                            <td>{{ $item->product->price }}</td>
                                            <td>{{ $item->product->seller->company }}</td>
                                            <td>{{--<a href="{{ route("product_list.destroyItem", ['id' => $item->id]) }}" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>--}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                No Items Found<br>
                            @endif


                        </div>



                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
