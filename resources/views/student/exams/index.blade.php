<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ujian Tersedia') }}
        </h2>
    </x-slot>

    <div x-data="{ confirmModalOpen: false, confirmExam: {} }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                @forelse ($exams as $exam)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $exam->title }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Mata Pelajaran: <span class="font-semibold">{{ $exam->subject->name }}</span>
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Dosen: {{ $exam->lecturer->name }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Jumlah Soal: {{ $exam->questions()->count() }} | Durasi: {{ $exam->duration }} menit
                                    </p>
                                </div>
                                <div class="mt-4 md:mt-0 flex-shrink-0 flex items-center">
                                    @if ($exam->end_time)
                                        <span class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                                            Ujian Telah Berakhir
                                        </span>
                                    @elseif (in_array($exam->id, $inProgressExamIds))
                                        <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                                                Lanjutkan Ujian
                                            </button>
                                        </form>
                                    @elseif (in_array($exam->id, $completedExamIds))
                                        <span class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                                            Selesai
                                        </span>
                                    @else
                                        <button @click="confirmExam = {{ json_encode($exam) }}; confirmModalOpen = true" type="button" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                            Mulai Kerjakan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center text-gray-500">
                            Tidak ada ujian yang tersedia saat ini.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $exams->links() }}
            </div>
        </div>

        <!-- Confirmation & Token Modal -->
        <template x-teleport="body">
            <div x-show="confirmModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="confirmModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900">Mulai Ujian: <span x-text="confirmExam.title"></span></h3>
                    
                    <form :action="'/student/exams/' + confirmExam.id + '/start'" method="POST" class="mt-6">
                        @csrf

                        <div>
                            <label for="token" class="block text-sm font-medium text-gray-700">Token Ujian</label>
                            <input type="text" id="token" name="token" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm uppercase" placeholder="MASUKKAN TOKEN" required>
                            @error('token')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 text-sm text-gray-600 space-y-3">
                            <p class="font-medium">Perhatian:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Ujian ini hanya dapat dikerjakan <span class="font-semibold">satu kali</span>.</li>
                                <li>Alokasi waktu pengerjaan adalah <strong x-text="confirmExam.duration + ' menit'"></strong>.</li>
                                <li>Waktu akan mulai berjalan setelah Anda menekan tombol "Ya, Mulai Ujian".</li>
                            </ul>
                            <p>Pastikan koneksi internet Anda stabil dan Anda sudah siap. Apakah Anda yakin?</p>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-4">
                            <button type="button" @click="confirmModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-indigo-500">Ya, Mulai Ujian</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
