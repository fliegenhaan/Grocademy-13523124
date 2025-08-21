@extends('layouts.app')

@section('title', $module->title)

@section('content')
    <a href="{{ route('modules.index', $module->course) }}" class="back-link">&larr; Kembali ke Daftar Modul</a>
    
    <div class="module-viewer-container">
        <h1>{{ $module->title }}</h1>
        <p>{{ $module->description }}</p>
        <hr style="margin: 1.5rem 0;">

        @if($module->pdf_content)
            <h3>Materi PDF</h3>
            <iframe src="{{ asset('storage/' . $module->pdf_content) }}" class="content-viewer"></iframe>
        @endif

        @if($module->video_content)
            <h3 style="margin-top: 2rem;">Materi Video</h3>
            <video class="content-viewer" controls>
                <source src="{{ asset('storage/' . $module->video_content) }}" type="video/mp4">
                Browser Anda tidak mendukung tag video.
            </video>
        @endif

        @if(!$module->pdf_content && !$module->video_content)
            <p>Tidak ada konten yang bisa ditampilkan untuk modul ini.</p>
        @endif
    </div>
@endsection