@extends('layouts.master')

@section('styles')
<style>
    .dashboard-inicio {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .logo-inicio {
        max-width: 520px;
        width: 80%;
        opacity: 0.35;
        filter: drop-shadow(0 8px 32px rgba(0,0,0,0.18));
        transition: opacity 0.3s ease;
    }
    .logo-inicio:hover {
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="dashboard-inicio">
    <img src="{{ asset('build/assets/images/Logo.png') }}"
         alt="Logo Soda"
         class="logo-inicio">
</div>
@endsection
