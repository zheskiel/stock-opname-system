@extends('layouts.app')

@section('style')
<style>
.card-body .bd-highlight a {
    display:block;
    border: 1px solid #3490dc;
    text-decoration: none;
    color: #fff;
    background-color: #3490dc;
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5">
                <div class="card-header">
                    <div class="text-center">
                        <strong>Homepage</strong>
                    </div>
                </div>

                <div class="card-body">
                    <div class="text-center mt-5 mb-5">
                        welcome
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
