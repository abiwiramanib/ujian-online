<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use App\Models\Exam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Lecturer and store it
        $lecturer = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'dosen@example.com',
            'password' => bcrypt('password'),
            'role' => 'dosen',
        ]);

        // Create Student
        User::create([
            'name' => 'Mahasiswa User',
            'email' => 'mahasiswa@example.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
        ]);

        // Create Subjects for the Lecturer
        $subject1 = Subject::create([
            'name' => 'Pemrograman Web Lanjutan',
            'description' => 'Membahas konsep-konsep modern dalam pengembangan web.',
            'user_id' => $lecturer->id,
        ]);

        $subject2 = Subject::create([
            'name' => 'Struktur Data',
            'description' => 'Mempelajari berbagai struktur data dan algoritmanya.',
            'user_id' => $lecturer->id,
        ]);

        // Create an Exam for the first subject
        $exam1 = Exam::create([
            'title' => 'Ujian Tengah Semester - Konsep Dasar',
            'description' => 'Ujian mencakup materi dari pertemuan 1 hingga 7.',
            'subject_id' => $subject1->id,
            'user_id' => $lecturer->id,
            'duration' => 90,
            'status' => 'published',
        ]);

        // Create Questions and Options for the exam
        $question1 = $exam1->questions()->create([
            'question_text' => 'Manakah dari berikut ini yang merupakan framework PHP?',
        ]);
        $question1->options()->createMany([
            ['option_text' => 'React', 'is_correct' => false],
            ['option_text' => 'Laravel', 'is_correct' => true],
            ['option_text' => 'Vue', 'is_correct' => false],
            ['option_text' => 'Angular', 'is_correct' => false],
        ]);

        $question2 = $exam1->questions()->create([
            'question_text' => 'Apa kepanjangan dari HTML?',
        ]);
        $question2->options()->createMany([
            ['option_text' => 'HyperText Markup Language', 'is_correct' => true],
            ['option_text' => 'High-Level Text Machine Language', 'is_correct' => false],
            ['option_text' => 'Hyper-Transferable Markup Language', 'is_correct' => false],
            ['option_text' => 'Hyper-Textual Machine Learning', 'is_correct' => false],
        ]);
    }
}
