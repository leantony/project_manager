@extends('layouts.master')

@section('content')
<div class="col-sm-4 col-sm-offset-4" style="margin-top: 20px;">
    <div class="well">
        <h3>Login/register</h3>
        <a href="#">
            Login/register using github
        </a>
        <form action="{{ route('auth.login') }}">
            <div class="form-group">
                <input type="email" class="form-control" placeholder="Email" ng-model="auth.email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Password" ng-model="auth.password">
            </div>
            <button class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
@stop