@extends('errors.layout')
@section('code', '419')
@section('title', 'Page expired')
@section('message', 'The page was open for too long, so the form expired for your security. Please go back, refresh and try again.')
@section('actions')
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Go back</a>
@endsection
