@extends('layouts.email')

@section('content')
    <div class="email-content">
        <h1>{{ $subject }}</h1>
        <div class="message">
            {{ $content }}
        </div>
    </div>
@endsection
