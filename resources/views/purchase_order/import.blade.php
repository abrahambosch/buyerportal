@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Import Offers/Purchase Orders</div>

                <div class="panel-body">
                    <form action="{{ route("purchase_order.importSave") }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('buyer') ? ' has-error' : '' }}">

                                @if ($user->user_type == 'buyer')
                                    <label class="col-md-4 control-label">Supplier</label>
                                    <div class="col-md-6">
                                        <select name="seller" id="seller" class="form-control">
                                            <option value="">Show all suppliers</option>
                                            @foreach ($user->sellers as $s)
                                                <option value="{{ $s->id }}" @if ($s->id == $seller_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('buyer'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('buyer') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                @endif

                                @if ($user->user_type == 'seller')
                                    <label class="col-md-4 control-label">Buyer</label>
                                    <div class="col-md-6">
                                        <select name="buyer" id="buyer" class="form-control">
                                            <option value="">Show all buyers</option>
                                            @foreach ($seller->users as $s)
                                                <option value="{{ $s->id }}" @if ($s->id == $buyer_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('seller'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('seller') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                @endif

                        </div>

                        <div class="form-group{{ $errors->has('import_type') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Import Type</label>
                            <div class="col-md-6">
                                <select name="import_type" id="import_type" class="form-control">
                                    <option value="">Simple CSV</option>
                                    <option value="berlington" @if ($import_type=="berlington") selected @endif>Burlington CSV</option>
                                </select>
                                @if ($errors->has('import_type'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('import_type') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('importFile') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">CSV File</label>
                            <div class="col-md-6">
                                <input type="file" class="form-control" name="importFile"/>
                                @if ($errors->has('importFile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('importFile') }}</strong>
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
