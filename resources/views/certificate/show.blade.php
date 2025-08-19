<!DOCTYPE html>
<html>
<head>
    <title>Sertifikat Kelulusan</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; text-align: center; }
        .certificate-container { border: 10px solid #787878; width: 90%; margin: 50px auto; padding: 30px; }
        h1 { font-size: 50px; color: #333; }
        h3 { font-size: 25px; }
    </style>
</head>
<body>
    <div class="certificate-container">
        <h3>Sertifikat Kelulusan</h3>
        <h1>Grocademy</h1>
        <p>Dengan bangga diberikan kepada:</p>
        <h2>{{ $user->first_name }} {{ $user->last_name }}</h2>
        <p>atas keberhasilannya menyelesaikan kursus:</p>
        <h3>{{ $course->title }}</h3>
        <p>Instruktur: {{ $course->instructor }}</p>
        <br>
        <p>Diselesaikan pada: {{ $completionDate }}</p>
    </div>
</body>
</html>