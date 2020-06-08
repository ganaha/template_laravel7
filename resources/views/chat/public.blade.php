@extends('layouts.app')

@section('content')
<chat-public-component username="{{ $username }}"></chat-public-component>
@endsection
