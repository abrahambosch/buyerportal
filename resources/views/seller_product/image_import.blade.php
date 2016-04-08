@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Image Import</div>

                <div class="panel-body">


                    <div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }} clearfix">
                        <label class="col-md-4 control-label">Buyer</label>
                        <div class="col-md-6">
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">No Buyer</option>
                                @foreach ($seller->users as $s)
                                    <option value="{{ $s->id }}" @if ($s->id == $buyer_id) selected @endif >{{ $s->company }} - {{ $s->first_name }} {{ $s->last_name }}</option>
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
                            <a href="{{ url()->previous() }}" class="btn btn-default">Back</a>
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
