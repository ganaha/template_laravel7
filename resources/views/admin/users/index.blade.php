@extends('layouts.admin')

@section('header')
    <div class="page-header clearfix">
        <h1>
            <i class="glyphicon glyphicon-align-justify"></i> User
            <a class="btn btn-success pull-right" href="{{ route('admin.users.create') }}"><i class="glyphicon glyphicon-plus"></i> Create</a>
        </h1>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if($users->count())
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">name</th>
                            <th class="text-center">email</th>
                            
                            <th class="text-center">OPTIONS</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="text-center"><strong>{{ $user->id }}</strong></td>
                                <td class="text-center"><strong>{{ $user->name }}</strong></td>
                                <td class="text-center"><strong>{{ $user->email }}</strong></td>

                                <td class="text-center">
                                    <chat-pusher-component id="{{ $user->id }}"></chat-pusher-component>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3 class="text-center alert alert-info">Empty!</h3>
            @endif

        </div>
    </div>

@endsection
