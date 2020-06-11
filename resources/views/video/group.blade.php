@extends('layouts.app')

@section('content')
<video-group-component :user="{{ $user }}"></video-group-component>
@endsection
