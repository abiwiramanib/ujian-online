<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hasil Ujian: {{ $exam->title }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                Mata Pelajaran: {{ $exam->subject->name }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('lecturer.exams.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                    &larr; Kembali ke Daftar Ujian
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Daftar Peserta</h3>
                        <p class="mt-1 text-sm text-gray-600">Daftar mahasiswa yang telah menyelesaikan ujian ini.</p>
                    </div>

                    <!-- Results Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Mahasiswa</th>
                                    <th scope="col" class="px-6 py-3">Waktu Mulai</th>
                                    <th scope="col" class="px-6 py-3">Waktu Selesai</th>
                                    <th scope="col" class="px-6 py-3 text-right">Skor</th>
                                    <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sessions as $session)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $session->student->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $session->start_time->format('d M Y, H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $session->end_time->format('d M Y, H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            {{ round($session->score) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('lecturer.exams.answers', $session) }}" class="text-blue-600 hover:underline">Lihat Jawaban</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada mahasiswa yang menyelesaikan ujian ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $sessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
