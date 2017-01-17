@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Account Created</div>
                <div class="panel-body">
                    <p>
                        Your account has been created and is awaiting approval. The
                        community administrator has been notified. When your
                        membership is approved, you will receive a second email.
                    </p>

                    <p>
                        <a class="btn btn-success" href="/login">Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
