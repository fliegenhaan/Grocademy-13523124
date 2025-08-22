<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        Quiz::query()->delete();
        $modules = Module::all();

        if ($modules->isEmpty()) {
            $this->command->info('Tidak ada modul untuk ditambahkan kuis.');
            return;
        }

        $this->command->info('Membuat kuis untuk beberapa modul...');

        foreach ($modules as $module) {
            if ($module->id % 2 != 0) {
                $quiz = Quiz::create([
                    'module_id' => $module->id,
                    'title' => 'Kuis Pemahaman: ' . $module->title,
                    'description' => 'Uji pemahaman Anda tentang materi yang telah dipelajari di modul ini.',
                    'passing_score' => 75,
                ]);

                for ($i = 1; $i <= 3; $i++) {
                    $question = Question::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => "Manakah pernyataan yang paling tepat mengenai konsep #$i di modul '{$module->title}'?",
                    ]);

                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => 'Ini adalah jawaban yang benar untuk pertanyaan ' . $i . '.',
                        'is_correct' => true,
                    ]);
                    for ($j = 1; $j <= 3; $j++) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => "Ini adalah pilihan jawaban salah #$j.",
                            'is_correct' => false,
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('Seeder kuis berhasil dijalankan!');
    }
}