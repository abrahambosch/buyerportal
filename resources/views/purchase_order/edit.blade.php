@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Offer
                    <a title="Offer Import" href="{{ route("purchase_order.import") }}?seller={{ $purchase_order->seller_id }}&buyer={{ $purchase_order->buyer_id }}" class="pull-right"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span></a>
                </div>

                <div class="panel-body">
                    <form action="{{ route("purchase_order.update", ['id' => $purchase_order->id]) }}" method="POST" class="form-horizontal">
                        {{ method_field('PUT') }}
                        {!! csrf_field() !!}


                        @foreach ($fields as $field => $label)
                            <div class="form-group{{ $errors->has($field) ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">{{ $label }}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="{{ $field }}" value="{{ $purchase_order->$field }}">

                                    @if ($errors->has($field))
                                        <span class="help-block">
                                            <strong>{{ $errors->first($field) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if ($user->user_type == 'buyer')
                        <div class="form-group{{ $errors->has('seller_id') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Supplier</label>
                            <div class="col-md-6">
                                <select name="seller_id" id="seller_id" class="form-control">
                                    <option value="">No Supplier</option>
                                    @foreach ($user->sellers as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $purchase_order->seller_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('seller_id'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('seller_id') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('buyer_notes') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Buyer Notes</label>
                            <div class="col-md-6">
                                <textarea name="buyer_notes" rows="5" class="form-control">{{ $purchase_order->buyer_notes }}</textarea>
                            </div>
                        </div>
                        @endif

                        @if ($user->user_type == 'seller')
                            <div class="form-group{{ $errors->has('buyer_id') ? ' has-error' : '' }} clearfix">
                                <label class="col-md-4 control-label">Filter by Buyer</label>
                                <div class="col-md-6">
                                    <select name="buyer_id" id="buyer_id" class="form-control">
                                        <option value="">Show all buyers</option>
                                        @foreach ($seller->users as $s)
                                            <option value="{{ $s->id }}" @if ($s->id == $purchase_order->buyer_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('seller'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('seller') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('seller_notes') ? ' has-error' : '' }} clearfix">
                                <label class="col-md-4 control-label">Supplier Notes</label>
                                <div class="col-md-6">
                                    <textarea name="seller_notes" rows="5" class="form-control">{{ $purchase_order->seller_notes }}</textarea>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="task" class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <input type="submit" value="Save"/>
                            </div>
                        </div>

                        <div>
                            @if (count($purchase_order->items))
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th><a title="Add New Items" href="{{ route("purchase_order.chooseProducts", ['id' => $purchase_order->id]) }}" class="pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
                                        <th><!-- image --></th>
                                        <th>Vendor Style Number</th>
                                        <th>UPC</th>
                                        <th>SKU</th>
                                        <th>GTIN</th>
                                        <th>Description</th>
                                        <th>Cost</th>
                                        <th>Quantity</th>
                                        <th>Supplier</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    @foreach ($purchase_order->items as $item)
                                        <tr>
                                            <td></td>
                                            <td><img src="{{ $item->product->getThumbnail() }}"/></td>
                                            <td>{{ $item->product->style }}</td>
                                            <td>{{ $item->product->upc }}</td>
                                            <td>{{ $item->product->sku }}</td>
                                            <td>{{ $item->product->gtin }}</td>
                                            <td>{{ $item->product->product_description }}</td>
                                            <td>{{ $item->product->unit_cost }}</td>
                                            <td>{{ $item->product->quantity }}</td>
                                            <td>{{ $item->product->seller->company }}</td>
                                            <td><a href="{{ route("purchase_order.destroyItem", ['id' => $item->id]) }}" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                No Items Found<br>
                                <a href="{{ route("purchase_order.create") }}" class=""><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> click here to add new product. </a>
                            @endif


                        </div>



                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
