@extends('layouts.app')

@section('title', 'Kuis: ' . $quiz->title)

@section('content')
    <h1>{{ $quiz->title }}</h1>
    <p>{{ $quiz->description }}</p>

    <form action="{{ route('quiz.submit', $module) }}" method="POST">
        @csrf
        <div class="quiz-questions">
            @foreach ($quiz->questions as $index => $question)
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-body">
                        <h2 class="card-title"><strong>Pertanyaan {{ $index + 1 }}:</strong> {{ $question->question_text }}</h2>
                        
                        <div class="form-group">
                            @foreach ($question->answers as $answer)
                                <div class="answer-option">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" id="answer-{{ $answer->id }}" required>
                                    <label for="answer-{{ $answer->id }}">{{ $answer->answer_text }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary btn-block">Selesaikan Kuis</button>
    </form>
@endsection