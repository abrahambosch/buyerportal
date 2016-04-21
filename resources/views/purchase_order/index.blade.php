@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Offers
                    <a title="Product Import" href="{{ route("purchase_order.import") }}" class="pull-right"><span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span></a>
                </div>
                <div class="panel-body">
                    <div id="filters">
                        <div class="form-group{{ $errors->has('seller') ? ' has-error' : '' }} clearfix">
                            <label class="col-md-4 control-label">Filter by Supplier</label>
                            <div class="col-md-6">
                                @if ($user->user_type == 'buyer')
                                <select name="seller" id="seller" class="form-control">
                                    <option value="">Show all suppliers</option>
                                    @foreach ($user->sellers as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $seller_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                    @endforeach
                                </select>
                                @endif
                                @if ($errors->has('buyer'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('buyer') }}</strong>
                                    </span>
                                @endif
                                @if ($user->user_type == 'seller')
                                <select name="buyer" id="buyer" class="form-control">
                                    <option value="">Show all buyers</option>
                                    @foreach ($seller->users as $s)
                                        <option value="{{ $s->id }}" @if ($s->id == $buyer_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
                                    @endforeach
                                </select>
                                @endif
                                @if ($errors->has('seller'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('seller') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if (count($results))
                        <table class="table">
                            <thead>
                            <tr>
                                <th><a href="{{ route("purchase_order.create") }}" class="pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a></th>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Company</th>
                            </tr>
                            </thead>

                        @foreach ($results as $po)
                            <tr>
                                <td><a href="{{ route("purchase_order.edit", ['purchase_order' => $po->id]) }}" class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
                                <td>{{ $po->id }}</td>
                                <td>{{ $po->list_name }}</td>
                                <td>{{ $po->seller->company }}</td>
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
            $("#seller").on("change", function(e) {
                var seller = $(this).val();
                var url = "/purchase_order/seller/" + seller;
                if (seller != "") {
                    window.location.href = url;
                }
                else {
                    window.location.href = "/purchase_order";
                }

            });
        });

    </script>
@endsection
