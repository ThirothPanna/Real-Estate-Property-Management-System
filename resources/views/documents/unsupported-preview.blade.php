@extends('layouts.app')

@section('title', $title . ' – Document')

@section('content')
    <main style="max-width:680px;margin:40px auto;padding:28px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;">
        <h1 style="margin:0;color:#111827;font-size:21px;">{{ $title }}</h1>
        <p style="margin:12px 0 20px;color:#4b5563;line-height:1.6;">This legacy Word file cannot be previewed in the browser. Download it to open it in a word processor.</p>
        <a href="{{ $downloadUrl }}" style="display:inline-block;padding:9px 14px;border-radius:7px;background:#15803d;color:#fff;font-size:14px;font-weight:600;text-decoration:none;">Download document</a>
    </main>
@endsection