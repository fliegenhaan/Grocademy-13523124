@extends('layouts.app')

@section('title', 'Modul: ' . $course->title)

@section('content')
    <h1>{{ $course->title }}</h1>
    
    <h3>Progres Belajar Anda</h3>
    <div class="progress-bar-container">
        <div class="progress-bar" style="width: {{ $progressPercentage }}%;">{{ round($progressPercentage) }}%</div>
    </div>

    @if($progressPercentage >= 100)
        <div class="alert alert-success" style="text-align: center; margin: 2rem 0;">
            <p style="margin:0;"><strong>Selamat! Anda telah menyelesaikan kursus ini.</strong></p>
            <a href="{{ route('certificate.download', $course) }}" class="btn btn-primary" style="margin-top: 1rem;">
                Unduh Sertifikat
            </a>
        </div>
    @endif

    <h2 style="margin-top: 2rem;">Daftar Modul</h2>
    <ul class="module-list">
        @foreach ($modules as $module)
            <li class="module-item {{ in_array($module->id, $completedModulesIds) ? 'completed' : '' }}">
                <div class="module-item-title">
                    <a href="{{ route('modules.show', ['module' => $module->id]) }}">
                        <strong>Modul {{ $module->order }}:</strong> {{ $module->title }}
                    </a>
                </div>
                
                @if (in_array($module->id, $completedModulesIds))
                    <span class="module-item-status">✓ Selesai</span>
                @else
                    <form action="{{ route('modules.complete', ['module' => $module->id]) }}" method="POST">
                        @csrf
                        <button type="submit">Tandai Selesai</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
@endsection