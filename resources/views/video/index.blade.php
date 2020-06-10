@extends('layouts.app')

@section('content')
<video-component :user="{{ $user }}" :others="{{ $others }}"></video-component>
@endsection
