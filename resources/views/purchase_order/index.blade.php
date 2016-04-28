@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Offers
                    <a title="Offer Import" href="{{ route("purchase_order.import") }}" class="pull-right"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span></a>
                </div>
                <div class="panel-body">
                    <div id="filters">
                        <div class="form-group{{ $errors->has('supplier') ? ' has-error' : '' }} clearfix">
                            @if ($user->user_type == 'buyer')
                            <label class="col-md-4 control-label">Filter by Supplier</label>
                            <div class="col-md-6">

                                <select name="supplier" id="supplier" class="form-control">
                                    <option value="">Show all suppliers</option>
                                    @foreach ($user->suppliers as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $supplier_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('buyer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('buyer') }}</strong>
                                    </span>
                                @endif
                            </div>
                            @endif
                            @if ($user->user_type == 'supplier')
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
                            @endif
                        </div>
                    </div>
                    @if (count($results))
                        <table class="table">
                            <thead>
                            <tr>
                                <th><a href="{{ route("purchase_order.create") }}" class="pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
                                <th>Id</th>
                                <th>Order#</th>
                                <th>Order Date</th>
                                <th>Company</th>
                            </tr>
                            </thead>

                        @foreach ($results as $po)
                            <tr>
                                <td><a href="{{ route("purchase_order.edit", ['purchase_order' => $po->id]) }}" class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
                                <td>{{ $po->id }}</td>
                                <td>{{ $po->po_num }}</td>
                                <td>{{ $po->order_date }}</td>
                                <td>{{ $po->supplier->company }}</td>
                            </tr>
                        @endforeach
                        </table>
                    @else
                        No Offers Found<br>
                        <a href="{{ route("purchase_order.create") }}" class=""><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> click here to add new Offer. </a>
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
                var url = "/purchase_order/supplier/" + supplier;
                if (supplier != "") {
                    window.location.href = url;
                }
                else {
                    window.location.href = "/purchase_order";
                }

            });
        });

    </script>
@endsection
