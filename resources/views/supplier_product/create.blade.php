@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Create a Product</div>

                <div class="panel-body">
                    <form action="{{ route("supplier_product.store") }}" method="POST" class="form-horizontal">
                        {!! csrf_field() !!}

                        @foreach ($fields as $field=>$label)
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

                        <div class="form-group{{ $errors->has('supplier_id') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Supplier</label>
                            <div class="col-md-6">
                                <select name="supplier_id" id="supplier_id" class="form-control">
                                    <option value="">No suppliers</option>
                                    @foreach ($user->suppliers as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == old('supplier_id')) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('supplier_id'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('supplier_id') }}</strong>
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
        </div>
    </div>
</div>
@endsection
