@extends('layouts.app') @section('title', 'File Manager – NEKJOUL IMANAGE')
@section('content')

<div style="max-width: 640px; margin: 40px auto; text-align: center">
    <div
        style="
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #f0fdf4;
            display: flex;
            align-items: center;
            justify-content: center;
        "
    >
        <svg aria-hidden="true" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h6l2 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="M2 10h20"/></svg>
    </div>
    <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 8px">
        File Manager
    </h1>
    <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px">
        Upload and manage your lease agreements, receipts, and documents.
    </p>
    <div
        style="
            display: inline-block;
            padding: 8px 20px;
            background: #f0fdf4;
            color: #16a34a;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        "
    >
        Coming soon
    </div>
</div>

@endsection
