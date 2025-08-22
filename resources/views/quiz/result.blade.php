@extends('layouts.app')

@section('title', 'Hasil Kuis')

@section('content')
    <div class="card" style="text-align: center;">
        <div class="card-body">
            <h1>Hasil Kuis Anda</h1>
            
            @if ($isPassed)
                <div class="alert alert-success">
                    <h2>Selamat, Anda Lulus!</h2>
                </div>
                <p style="font-size: 1.5rem;">Skor Anda: <strong>{{ round($score) }}%</strong></p>
                <p>Anda telah memenuhi nilai minimum kelulusan ({{ $passing_score }}%). Modul ini telah ditandai selesai.</p>
                <a href="{{ route('modules.index', $module->course) }}" class="btn btn-primary" style="margin-top: 1rem;">Kembali ke Daftar Modul</a>
            @else
                <div class="alert alert-danger">
                    <h2>Maaf, Anda Belum Lulus.</h2>
                </div>
                <p style="font-size: 1.5rem;">Skor Anda: <strong>{{ round($score) }}%</strong></p>
                <p>Anda harus mencapai nilai minimum {{ $passing_score }}% untuk melanjutkan. Silakan coba lagi.</p>
                <a href="{{ route('quiz.show', $module) }}" class="btn btn-secondary" style="margin-top: 1rem;">Coba Lagi Kuis</a>
            @endif
        </div>
    </div>
@endsection