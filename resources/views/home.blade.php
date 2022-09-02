@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if(Session::has('message'))
                    <div class="alert alert-primary" role="alert">
                        {{Session::get('message')}}
                    </div>
                    @else
                    <div class="alert alert-primary" role="alert">
                        Hi {{ $user->name }}!
                    </div>
                    @endif
                    <div class="row">
                        <ul>
                            <li><a href="{{ route('edit') }}">Edit Details</a></li>
                            <li><a href="{{ route('editpassword') }}">Edit Password</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
