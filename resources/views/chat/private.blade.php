@extends('layouts.app')

@section('content')
<chat-private-component :user="{{ $user }}"></chat-private-component>
@endsection
