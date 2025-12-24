<x-exam-layout>
    <div class="py-8 md:py-12" x-data="{ 
            warningModalOpen: false,
            terminationModalOpen: false,
            timeUpModalOpen: false,
            leaveCount: 0,
            violationReason: '',
            resizeWarningModalOpen: false,
            resizeCountdown: 5,
            resizeInterval: null,
            totalQuestions: {{ $session->exam->questions->count() }},
            answeredQuestions: [],
            flaggedQuestions: [],
            activeQuestionIndex: 0,
            examStarted: false,
            fullscreenEnabled: document.fullscreenElement !== null,

            init() {
                this.updateAnswered();
                this.flaggedQuestions = JSON.parse(localStorage.getItem('flagged_questions_{{ $session->id }}')) || [];
                document.addEventListener('fullscreenchange', () => this.checkFullscreen());
            },
            startExam() {
                const elem = document.documentElement;
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.mozRequestFullScreen) { /* Firefox */
                    elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) { /* IE/Edge */
                    elem.msRequestFullscreen();
                }
                this.examStarted = true;
            },
            checkFullscreen() {
                this.fullscreenEnabled = document.fullscreenElement !== null;
                if (!this.fullscreenEnabled && this.examStarted) {
                    this.handleViolation('Keluar dari mode layar penuh');
                } else if (this.fullscreenEnabled && this.examStarted) {
                    // User is back in fullscreen, clear any resize warnings
                    this.resizeWarningModalOpen = false;
                    if (this.resizeInterval) {
                        clearInterval(this.resizeInterval);
                        this.resizeInterval = null;
                    }
                    this.resizeCountdown = 5; // Reset countdown
                }
            },
            handleResize() {
                if (!this.examStarted || document.fullscreenElement || this.resizeWarningModalOpen) {
                    return;
                }
                
                this.resizeWarningModalOpen = true;
                this.resizeInterval = setInterval(() => {
                    this.resizeCountdown--;
                    if (this.resizeCountdown <= 0) {
                        clearInterval(this.resizeInterval);
                        document.getElementById('exam-form').submit();
                    }
                }, 1000);
            },
            updateAnswered() {
                const mcq = Array.from(document.querySelectorAll('input[type=radio]:checked')).map(el => el.name.match(/\[(\d+)\]/)[1]);
                const essay = Array.from(document.querySelectorAll('textarea[name^=answers_text]')).filter(el => el.value.trim() !== '').map(el => el.name.match(/\[(\d+)\]/)[1]);
                this.answeredQuestions = [...new Set([...mcq, ...essay])];
            },
            isAnswered(questionId) {
                return this.answeredQuestions.includes(String(questionId));
            },
            isFlagged(questionId) {
                return this.flaggedQuestions.includes(questionId);
            },
            toggleFlag(questionId) {
                const index = this.flaggedQuestions.indexOf(questionId);
                if (index === -1) {
                    this.flaggedQuestions.push(questionId);
                } else {
                    this.flaggedQuestions.splice(index, 1);
                }
                localStorage.setItem('flagged_questions_{{ $session->id }}', JSON.stringify(this.flaggedQuestions));
            },
            nextQuestion() {
                if (this.activeQuestionIndex < this.totalQuestions - 1) {
                    this.activeQuestionIndex++;
                }
            },
            prevQuestion() {
                if (this.activeQuestionIndex > 0) {
                    this.activeQuestionIndex--;
                }
            },
            handleViolation(reason) {
                if (!this.examStarted) return; // Don't track violations before exam starts

                this.leaveCount++;
                this.violationReason = reason;
            
                // Log the violation starting from the first offense
                axios.post('{{ route('student.exams.log_cheating', $session) }}', { reason: reason })
                    .catch(error => console.error('Error logging cheat attempt:', error));

                if (this.leaveCount >= 3) {
                    this.warningModalOpen = false;
                    this.terminationModalOpen = true;
                    setTimeout(() => {
                        document.getElementById('exam-form').submit();
                    }, 3000);
                } else {
                    this.warningModalOpen = true;
                }
            },
            autoSaveAnswer(questionId, optionId) {
                if (!optionId) return;
                axios.post('{{ route('student.exams.autosave', $session) }}', {
                    question_id: questionId,
                    option_id: optionId
                })
                .then(response => {
                    console.log(`Answer for question ${questionId} auto-saved.`);
                })
                .catch(error => {
                    console.error(`Autosave error for question ${questionId}:`, error);
                });
            },
            autoSaveEssay(questionId, answerText) {
                axios.post('{{ route('student.exams.autosave', $session) }}', {
                    question_id: questionId,
                    answer_text: answerText
                })
                .then(response => {
                    console.log(`Essay answer for question ${questionId} auto-saved.`);
                })
                .catch(error => {
                    console.error(`Autosave error for question ${questionId}:`, error);
                });
            }
         }" @violation.window="handleViolation($event.detail.reason)" @time-up.window="timeUpModalOpen = true; setTimeout(() => document.getElementById('exam-form').submit(), 3000)" @window-resized.window="handleResize()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div x-show="!examStarted" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-80 z-50 flex items-center justify-center" style="display: none;">
                <div class="bg-white text-center p-8 rounded-lg shadow-2xl max-w-lg">
                    <h2 class="text-2xl font-bold mb-4">Persiapan Memulai Ujian</h2>
                    <p class="text-gray-600 mb-6">Ujian ini akan berjalan dalam mode layar penuh (fullscreen) untuk menjaga integritas. Anda dilarang keluar dari mode layar penuh atau berpindah ke aplikasi lain selama ujian berlangsung.</p>
                    <p class="text-sm text-red-600 mb-6">Setiap upaya keluar dari layar penuh atau meninggalkan halaman akan dicatat sebagai pelanggaran.</p>
                    <button @click="startExam()" class="w-full bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors">
                        Mulai Ujian & Masuk Layar Penuh
                    </button>
                </div>
            </div>

            <div x-show="examStarted" class="md:grid md:grid-cols-3 md:gap-8" style="display: none;">
                <!-- Main Content: Questions -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 md:p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                    {{ $session->exam->title }}
                                </h2>
                                <div class="text-sm">
                                    Soal <span x-text="activeQuestionIndex + 1"></span> dari <span x-text="totalQuestions"></span>
                                </div>
                            </div>

                            <form id="exam-form" action="{{ route('student.exams.submit', $session) }}" method="POST">
                                @csrf
                                <div class="min-h-[20rem]">
                                    @foreach ($session->exam->questions as $index => $question)
                                        <div x-show="activeQuestionIndex === {{ $index }}" class="p-4" x-transition>
                                            <div class="flex justify-between items-center">
                                                <p class="font-semibold text-gray-800">Soal #{{ $index + 1 }}</p>
                                                <button @click.prevent="toggleFlag({{ $question->id }})" class="p-1 rounded-full hover:bg-gray-100" title="Tandai Soal">
                                                    <svg x-show="!isFlagged({{ $question->id }})" class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                                    </svg>
                                                    <svg x-show="isFlagged({{ $question->id }})" style="display: none;" class="w-5 h-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                                    </svg>
                                                </button>
                                            </div>
                                            @if($question->image_path)
                                                <img src="{{ Storage::url($question->image_path) }}" alt="Gambar Soal" class="mt-4 mb-4 rounded-lg max-w-xs h-auto mx-auto">
                                            @endif
                                            <p class="text-gray-700 text-lg">{{ $question->question_text }}</p>
                                            <div class="mt-6 space-y-4">
                                                @if($question->type === 'multiple_choice')
                                                    @foreach ($question->options as $option)
                                                        <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-400">
                                                            <input @change="updateAnswered(); autoSaveAnswer({{ $question->id }}, $event.target.value)" 
                                                                type="radio" 
                                                                name="answers[{{ $question->id }}]" 
                                                                value="{{ $option->id }}" 
                                                                class="h-4 w-4 text-indigo-600 border-gray-300"
                                                                {{ optional($savedAnswers->get($question->id))->option_id == $option->id ? 'checked' : '' }}>
                                                            <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                                                        </label>
                                                    @endforeach
                                                @else
                                                    <textarea name="answers_text[{{ $question->id }}]" 
                                                              rows="8" 
                                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                                              placeholder="Ketik jawaban esai Anda di sini..."
                                                              @input.debounce.1000ms="updateAnswered(); autoSaveEssay({{ $question->id }}, $event.target.value)">{{ optional($savedAnswers->get($question->id))->answer_text ?? '' }}</textarea>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="mt-8 pt-6 border-t flex justify-between items-center">
                                    <button @click.prevent="prevQuestion()" x-show="activeQuestionIndex > 0" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                                        &larr; Sebelumnya
                                    </button>
                                    <div x-show="activeQuestionIndex === 0" class="w-36">&nbsp;</div> <!-- Placeholder for spacing -->

                                    <div x-show="answeredQuestions.length === totalQuestions" x-transition>
                                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-base text-white uppercase tracking-widest hover:bg-green-500">
                                            Selesaikan Ujian
                                        </button>
                                    </div>

                                    <button @click.prevent="nextQuestion()" x-show="activeQuestionIndex < totalQuestions - 1" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                        Selanjutnya &rarr;
                                    </button>
                                    <div x-show="activeQuestionIndex === totalQuestions - 1" class="w-36">&nbsp;</div> <!-- Placeholder for spacing -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Question Navigator -->
                <div class="md:col-span-1">
                    <div class="sticky top-28">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-4">
                                <!-- Timer -->
                                <div x-data="timer(new Date('{{ $session->start_time->toIso8601String() }}').getTime() + {{ $session->exam->duration }} * 60 * 1000)" x-init="init();" class="bg-white p-3 rounded-lg shadow-inner border w-full mb-4">
                                    <div class="text-sm font-medium text-gray-600 text-center">Sisa Waktu</div>
                                    <div class="text-2xl font-bold text-center tracking-wider" :class="remaining < 300 ? 'text-red-600' : 'text-gray-900'">
                                        <span x-text="time().hours"></span>:<span x-text="time().minutes"></span>:<span x-text="time().seconds"></span>
                                    </div>
                                </div>

                                <h3 class="font-semibold text-lg text-gray-800 mb-4">Navigasi Soal</h3>
                                <div class="p-3 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg" role="alert">
                                    Progress: <span class="font-bold" x-text="answeredQuestions.length"></span> / <span class="font-bold" x-text="totalQuestions"></span> soal.
                                </div>
                                <div class="grid grid-cols-5 gap-2">
                                    @foreach ($session->exam->questions as $index => $question)
                                        <button @click.prevent="activeQuestionIndex = {{ $index }}" 
                                           class="w-10 h-10 rounded-md flex items-center justify-center font-bold text-sm border-2 transition relative"
                                           :class="{
                                               'bg-blue-500 border-blue-600 text-white': isAnswered({{ $question->id }}) && !isFlagged({{ $question->id }}),
                                               'bg-yellow-400 border-yellow-500 text-white': isFlagged({{ $question->id }}),
                                               'bg-white border-gray-300 text-gray-700 hover:bg-gray-100': !isAnswered({{ $question->id }}) && !isFlagged({{ $question->id }}),
                                               'ring-2 ring-offset-1 ring-indigo-500': activeQuestionIndex === {{ $index }}
                                           }">
                                            {{ $index + 1 }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cheating Warning Modal -->
        <template x-teleport="body">
            <div x-show="warningModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" @click.self="warningModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md text-center">
                    <div class="flex justify-center mx-auto w-16 h-16 bg-yellow-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-yellow-500 self-center" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">PERINGATAN #<span x-text="leaveCount"></span></h3>
                    <p class="mt-2 text-sm text-gray-600">Pelanggaran terdeteksi: <strong x-text="violationReason"></strong>. Tindakan ini tercatat oleh sistem.</p>
                    
                    <div x-show="leaveCount > 0" class="mt-4 p-3 bg-red-100 text-red-800 text-sm rounded-lg" style="display: none;">
                        <p class="font-bold">Ini adalah pelanggaran ke-<span x-text="leaveCount"></span> dari 3. Ujian akan dihentikan pada pelanggaran ke-3.</p>
                    </div>

                    <div class="mt-6">
                        <button type="button" @click="warningModalOpen = false" class="w-full px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-yellow-600">
                            Saya Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Resize Warning Modal -->
        <template x-teleport="body">
            <div x-show="resizeWarningModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md text-center">
                    <div class="flex justify-center mx-auto w-16 h-16 bg-orange-100 rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-orange-500 self-center">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">KEMBALI KE LAYAR PENUH</h3>
                    <p class="mt-2 text-sm text-gray-600">Anda telah mengubah ukuran layar. Harap kembali ke mode layar penuh untuk melanjutkan ujian.</p>
                    <div class="my-4 text-6xl font-bold text-red-600" x-text="resizeCountdown"></div>
                    <p class="mt-2 text-xs text-gray-500">Jika tidak, ujian akan dihentikan secara otomatis.</p>
                    <div class="mt-6">
                        <button type="button" @click="startExam()" class="w-full px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-700">
                            Kembali ke Layar Penuh
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Termination Modal -->
        <template x-teleport="body">
            <div x-show="terminationModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md text-center">
                    <div class="flex justify-center mx-auto w-16 h-16 bg-red-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-red-600 self-center" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">UJIAN DIHENTIKAN</h3>
                    <p class="mt-2 text-sm text-gray-600">Anda telah melakukan pelanggaran sebanyak 3 kali. Ujian Anda dihentikan dan akan diselesaikan secara otomatis.</p>
                    <p class="mt-4 text-xs text-gray-500">Halaman akan dialihkan dalam beberapa detik...</p>
                </div>
            </div>
        </template>

        <!-- Time's Up Modal -->
        <template x-teleport="body">
            <div x-show="timeUpModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md text-center">
                    <div class="flex justify-center mx-auto w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-blue-600 self-center" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">WAKTU HABIS</h3>
                    <p class="mt-2 text-sm text-gray-600">Waktu pengerjaan ujian telah berakhir. Ujian Anda akan diselesaikan secara otomatis.</p>
                    <p class="mt-4 text-xs text-gray-500">Halaman akan dialihkan dalam beberapa detik...</p>
                </div>
            </div>
        </template>
    </div>

    <script>
        function timer(expiry) {
            return {
                expiry: expiry,
                remaining: null,
                timeUp: false,
                init() {
                    this.setRemaining();
                    setInterval(() => {
                        this.setRemaining();
                    }, 1000);
                },
                setRemaining() {
                    const diff = this.expiry - new Date().getTime();
                    if (diff < 0 && !this.timeUp) {
                        this.timeUp = true;
                        window.dispatchEvent(new CustomEvent('time-up'));
                    }
                    this.remaining = parseInt(diff / 1000);
                },
                time() {
                    return {
                        days: Math.floor(this.remaining / 86400),
                        hours: Math.floor((this.remaining % 86400) / 3600).toString().padStart(2, '0'),
                        minutes: Math.floor((this.remaining % 3600) / 60).toString().padStart(2, '0'),
                        seconds: Math.floor(this.remaining % 60).toString().padStart(2, '0'),
                    }
                },
            }
        }

        // Cheat Detection
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') {
                window.dispatchEvent(new CustomEvent('violation', { detail: { reason: 'Berpindah tab atau aplikasi' } }));
            }
        });

        window.addEventListener('resize', function () {
            window.dispatchEvent(new CustomEvent('window-resized'));
        });

        // Prevent Back Button
        history.pushState(null, '', location.href);
        window.addEventListener('popstate', function () {
            history.pushState(null, '', location.href);
            window.dispatchEvent(new CustomEvent('violation', { detail: { reason: 'Mencoba menggunakan tombol kembali' } }));
        });
    </script>
</x-exam-layout>
