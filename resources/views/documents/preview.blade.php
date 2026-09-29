<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} – Document Preview</title>
    <style>
        * { box-sizing:border-box; }
        body { margin:0; background:#eef1f4; color:#202124; font-family:Arial,sans-serif; }
        header { position:sticky; top:0; display:flex; justify-content:space-between; align-items:center; gap:16px; padding:12px 20px; border-bottom:1px solid #d8dde3; background:#fff; }
        h1 { min-width:0; margin:0; overflow:hidden; color:#252a30; font-size:16px; text-overflow:ellipsis; white-space:nowrap; }
        a { flex-shrink:0; padding:8px 12px; border-radius:6px; background:#15803d; color:#fff; font-size:13px; font-weight:600; text-decoration:none; }
        iframe { display:block; width:min(860px, calc(100% - 32px)); height:calc(100vh - 92px); margin:10px auto; border:0; background:#fff; box-shadow:0 1px 5px #0002; }
        @media(max-width:600px) { header { padding:10px 12px; } iframe { width:100%; height:calc(100vh - 62px); margin:0; } }
    </style>
</head>
<body>
    <header>
        <h1>{{ $title }}</h1>
        <a href="{{ $downloadUrl }}">Download</a>
    </header>
    <iframe sandbox="" title="{{ $title }}" srcdoc="{{ $content }}"></iframe>
</body>
</html>