<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modul: {{ $course->title }}</title>
    <style>
        /* CSS Sederhana */
        body { font-family: sans-serif; }
        .container { max-width: 900px; margin: auto; padding: 20px; }
        .progress-bar-container { width: 100%; background-color: #f1f1f1; border-radius: 5px; }
        .progress-bar { width: {{ $progressPercentage }}%; height: 30px; background-color: #4CAF50; text-align: center; line-height: 30px; color: white; border-radius: 5px; }
        .module-list { list-style: none; padding: 0; margin-top: 20px; }
        .module-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #ddd; margin-bottom: 10px; border-radius: 5px; }
        .completed { background-color: #e8f5e9; border-left: 5px solid #4CAF50; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('courses.show', $course) }}">&larr; Kembali ke Detail Kursus</a>
        <h1>{{ $course->title }}</h1>
        
        <h3>Progres Anda</h3>
        <div class="progress-bar-container">
            <div class="progress-bar">{{ round($progressPercentage) }}%</div>
        </div>
        @if($progressPercentage >= 100)
            <div style="text-align: center; margin: 20px 0;">
                <p style="color: green;"><strong>Selamat! Anda telah menyelesaikan kursus ini.</strong></p>
                <a href="{{ route('certificate.download', $course) }}" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                    Unduh Sertifikat
                </a>
            </div>
        @endif

        <ul class="module-list">
            @foreach ($modules as $module)
                <li class="module-item {{ in_array($module->id, $completedModulesIds) ? 'completed' : '' }}">
                    <a href="{{ route('modules.show', $module) }}" style="text-decoration: none; color: black;">
                        <span>
                            <strong>Modul {{ $module->order }}:</strong> {{ $module->title }}
                        </span>
                    </a>
                    
                    @if (in_array($module->id, $completedModulesIds))
                        <span>✓ Selesai</span>
                    @else
                        <form action="{{ route('modules.complete', $module) }}" method="POST">
                            @csrf
                            <button type="submit">Tandai Selesai</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</body>
</html>