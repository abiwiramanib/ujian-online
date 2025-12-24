<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Jawaban Ujian
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                <strong>Ujian:</strong> {{ $session->exam->title }} | <strong>Mahasiswa:</strong> {{ $session->student->name }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('lecturer.exams.results', $session->exam) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    &larr; Kembali ke Hasil Ujian
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 space-y-8">

                    @foreach ($orderedQuestions as $index => $question)
                        <div class="border rounded-lg p-6">
                            <div class="flex justify-between items-start">
                                <h4 class="font-semibold text-gray-800">Soal #{{ $index + 1 }}</h4>
                            </div>
                            
                            <div class="mt-4 text-gray-700 prose max-w-none">
                                {!! $question->question_text !!}
                            </div>

                            @if($question->image_path)
                                <div class="mt-4">
                                    <img src="{{ asset('storage/' . $question->image_path) }}" alt="Gambar Soal" class="max-w-xs rounded-md">
                                </div>
                            @endif

                            <div class="mt-6 border-t pt-6">
                                @php
                                    $studentAnswer = $studentAnswers->get($question->id);
                                @endphp

                                @if ($question->type == 'multiple_choice')
                                    <h5 class="font-semibold text-sm text-gray-600 mb-4">Jawaban Mahasiswa:</h5>
                                    <div class="space-y-3">
                                        @foreach ($question->options as $option)
                                            @php
                                                $isStudentChoice = $studentAnswer && $studentAnswer->option_id == $option->id;
                                                $isCorrect = $option->is_correct;
                                                
                                                $optionClass = 'border-gray-300'; // Default
                                                if ($isStudentChoice && $isCorrect) {
                                                    $optionClass = 'border-green-500 bg-green-50'; // Jawaban benar
                                                } elseif ($isStudentChoice && !$isCorrect) {
                                                    $optionClass = 'border-red-500 bg-red-50'; // Jawaban salah
                                                } elseif ($isCorrect) {
                                                    $optionClass = 'border-green-500'; // Kunci jawaban (jika mahasiswa salah)
                                                }
                                            @endphp
                                            <div class="flex items-center p-3 border rounded-md {{ $optionClass }}">
                                                @if ($isStudentChoice)
                                                    <svg class="w-5 h-5 mr-3 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                @else
                                                    <div class="w-5 h-5 mr-3"></div>
                                                @endif
                                                <span class="flex-grow">{{ $option->option_text }}</span>
                                                @if ($isCorrect)
                                                    <span class="text-xs font-semibold text-green-700 ml-4">(Kunci Jawaban)</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif ($question->type == 'essay')
                                    <h5 class="font-semibold text-sm text-gray-600 mb-2">Jawaban Mahasiswa:</h5>
                                    <div class="p-4 border rounded-md bg-gray-50 text-gray-800 prose max-w-none mb-4">
                                        {!! nl2br(e($studentAnswer->answer_text ?? 'Tidak dijawab')) !!}
                                    </div>

                                    @if ($studentAnswer)
                                    <div class="mt-4 pt-4 border-t">
                                        <h5 class="font-semibold text-sm text-gray-600 mb-3">Penilaian Esai:</h5>
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-grow">
                                                @if (is_null($studentAnswer->is_correct))
                                                    <span class="text-sm font-medium px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">Belum Dinilai</span>
                                                @elseif ($studentAnswer->is_correct)
                                                    <span class="text-sm font-medium px-3 py-1 rounded-full bg-green-100 text-green-800">Benar</span>
                                                @else
                                                    <span class="text-sm font-medium px-3 py-1 rounded-full bg-red-100 text-red-800">Salah</span>
                                                @endif
                                            </div>
                                            @if (is_null($studentAnswer->is_correct))
                                                <form action="{{ route('lecturer.answers.grade', $studentAnswer) }}" method="POST" class="flex items-center space-x-2">
                                                    @csrf
                                                    <button type="submit" name="is_correct" value="1" class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-md hover:bg-green-600">
                                                        Tandai Benar
                                                    </button>
                                                    <button type="submit" name="is_correct" value="0" class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-md hover:bg-red-600">
                                                        Tandai Salah
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
