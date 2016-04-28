@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Product List</div>

                <div class="panel-body">
                    <form action="{{ route("product_list.update", ['product_list' => $product_list->id]) }}" method="POST" class="form-horizontal">
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

                        <div class="form-group{{ $errors->has('supplier_id') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Supplier</label>
                            <div class="col-md-6">
                                <select name="supplier_id" id="supplier_id" class="form-control">
                                    <option value="">No Supplier</option>
                                    @foreach ($user->suppliers as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $product_list->supplier_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
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

                        <div>
                            @if (count($product_list->items))
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th><!-- image --></th>
                                        @foreach ($fields as $field=>$label)
                                            <th>{{ $label }}</th>
                                        @endforeach
                                        <th>Supplier</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    @foreach ($product_list->items as $item)
                                        <tr>
                                            <td></td>
                                            <td><img src="{{ $item->product->getThumbnail() }}"/></td>
                                            @foreach ($fields as $field=>$label)
                                                <td>{{ $item->product->$field }}</td>
                                            @endforeach
                                            <td>{{ $item->product->supplier->company }}</td>
                                            <td><a href="{{ route("product_list.destroyItem", ['id' => $item->id]) }}" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                No Items Found<br>
                                <a href="{{ route("product_list.create") }}" class=""><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> click here to add new product. </a>
                            @endif


                        </div>



                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
