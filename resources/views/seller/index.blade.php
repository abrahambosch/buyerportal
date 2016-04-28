@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Suppliers
                    <a href="{{ route("seller.create") }}" class="pull-right"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                </div>

                <div class="panel-body">
                    @if (count($sellers))
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th></th>
                            </tr>
                            </thead>

                        @foreach ($sellers as $user)
                            <tr>
                                <td><a href="{{ route("seller.edit", ['seller' => $user->id]) }}" class="pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a></td>
                                <td>{{ $user->company }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->middle_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{--<a href="{{ route("seller.delete", ['seller' => $user->id]) }}" class="pull-right"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>--}}</td>

                            </tr>
                        @endforeach
                        </table>
                    @else
                        No Suppliers Found
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
