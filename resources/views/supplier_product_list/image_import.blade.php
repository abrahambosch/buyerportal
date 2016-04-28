@extends('layouts.app')

@section('content')


<script>
    window.product_list_id = {{ $product_list->id }};
</script>

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Product List
                    <a href="{{ route("supplier_product_list.image_import", ['id' => $product_list->id]) }}" class="pull-right"><span class="glyphicon glyphicon-import" aria-hidden="true"></span></a>
                </div>

                <div class="panel-body">
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
                                @foreach ($supplier->users as $s)
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
                        <label for="task" class="col-sm-3 control-label">
                            <a class="btn btn-default" href="{{ route("supplier_product_list.edit", ['id' => $product_list->id]) }}">Back</a>
                        </label>
                        <div class="col-sm-6">
                            {{-- <input type="submit" value="Save"/> --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials/fileupload')


@endsection
