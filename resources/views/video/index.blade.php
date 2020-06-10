@extends('layouts.app')

@section('content')
<video-component :user="{{ $user }}"></video-component>
@endsection
