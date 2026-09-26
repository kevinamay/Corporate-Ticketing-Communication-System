@extends('layouts.auth')

@section('content')
    <livewire:reset-password :token="$token" />
@endsection
