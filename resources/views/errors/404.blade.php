@extends('errors.layout')
@section('code', '404')
@section('title', 'Page not found')
@section('message', 'Sorry, we couldn’t find the page you were looking for. It may have been moved or deleted.')
@section('actions')
    <a href="{{ url('/blog') }}" class="btn btn-secondary">Browse the blog</a>
@endsection
