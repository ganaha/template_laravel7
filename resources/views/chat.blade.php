@extends('layouts.app')

@section('content')
<chat-component username="{{ $username }}"></chat-component>
@endsection
