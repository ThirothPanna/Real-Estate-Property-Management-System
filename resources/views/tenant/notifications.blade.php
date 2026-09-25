@extends('layouts.app')

@section('title', 'Notifications – TenantCloud')

@section('content')

    <div class="welcome">
        <h1>Notifications</h1>
        <p>All your account activity in one place.</p>
    </div>

    <div class="panel">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="margin:0;">All Notifications</h3>
            <button class="btn btn-outline" style="font-size:13px; padding:8px 14px;">Mark all read</button>
        </div>

        <div class="empty">You don't have any notifications yet.</div>
    </div>

@endsection