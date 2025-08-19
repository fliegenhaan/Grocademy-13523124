<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $module->title }}</title>
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 1000px; margin: auto; padding: 20px; }
        .content-viewer { width: 100%; height: 600px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('modules.index', $module->course) }}">&larr; Kembali ke Daftar Modul</a>
        <h1>{{ $module->title }}</h1>
        <p>{{ $module->description }}</p>
        <hr>

        {{-- Cek apakah ada konten PDF --}}
        @if($module->pdf_content)
            <h3>Materi PDF</h3>
            <iframe src="{{ $module->pdf_content }}" class="content-viewer"></iframe>
        @endif

        {{-- Cek apakah ada konten Video --}}
        @if($module->video_content)
            <h3>Materi Video</h3>
            {{-- Asumsi video_content adalah URL embed (cth: dari YouTube) --}}
            <iframe src="{{ $module->video_content }}" class="content-viewer" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        @endif

        @if(!$module->pdf_content && !$module->video_content)
            <p>Tidak ada konten yang bisa ditampilkan untuk modul ini.</p>
        @endif
    </div>
</body>
</html>