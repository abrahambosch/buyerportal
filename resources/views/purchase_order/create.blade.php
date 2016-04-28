@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Create Offer</div>
                <p>To create an offer, first choose a supplier. </p>

                <div class="panel-body">
                    <form action="{{ route("purchase_order.store") }}" method="POST" class="form-horizontal">
                        {!! csrf_field() !!}

                        @foreach (['po_num' => 'Name', 'order_date' => 'Date'] as $field => $label)
                            <div class="form-group{{ $errors->has($field) ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">{{ $label }}</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="{{ $field }}" value="{{ old($field) }}">

                                    @if ($errors->has($field))
                                        <span class="help-block">
                                            <strong>{{ $errors->first($field) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if ($user->user_type == 'buyer')
                            <div class="form-group{{ $errors->has('supplier_id') ? ' has-error' : '' }} clearfix">
                                <label class="col-md-4 control-label">Supplier</label>
                                <div class="col-md-6">
                                    <select name="supplier_id" id="supplier_id" class="form-control">
                                        <option value="">No Supplier</option>
                                        @foreach ($user->suppliers as $s)
                                            <option value="{{ $s->id }}"  >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('supplier_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('supplier_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($user->user_type == 'supplier')
                            <div class="form-group{{ $errors->has('buyer_id') ? ' has-error' : '' }} clearfix">
                                <label class="col-md-4 control-label">Filter by Buyer</label>
                                <div class="col-md-6">
                                    <select name="buyer_id" id="buyer_id" class="form-control">
                                        <option value="">Show all buyers</option>
                                        @foreach ($supplier->users as $s)
                                            <option value="{{ $s->id }}"  >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('supplier'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('supplier') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="task" class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <input type="submit" value="Save"/>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
