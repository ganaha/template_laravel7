@extends('layouts.app')

@section('content')
<chat-presence-component username="{{ $username }}" id="{{ $id }}"></chat-presence-component>
@endsection
