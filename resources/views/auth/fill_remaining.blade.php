@extends('layouts.master')


@section('content')

    <div class="col-md-6 col-md-offset-2" style="margin-top: 20px;">
        <form action="{{ route('auth.fill.post') }}" method="post" class="form-horizontal" role="form">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-group">
                <legend>Fill in your password to create your account</legend>
            </div>

            <div class="form-group">
                <label for="email" class="col-sm-2 control-label">Email address</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" id="email" value="{{ $user->email }}" disabled>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="password" id="password" required
                           placeholder="Enter your password">
                </div>

            </div>
                <div class="form-group">
                    <label for="password_confirmation" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required
                               placeholder="Enter your password">
                    </div>

                </div>
            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>

@stop