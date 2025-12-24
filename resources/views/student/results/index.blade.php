<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Ujian Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Riwayat Ujian Selesai</h3>
                    
                    <!-- Results Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Ujian</th>
                                    <th scope="col" class="px-6 py-3">Mata Pelajaran</th>
                                    <th scope="col" class="px-6 py-3">Dosen Pengampu</th>
                                    <th scope="col" class="px-6 py-3">Tanggal Selesai</th>
                                    <th scope="col" class="px-6 py-3">Soal</th>
                                    <th scope="col" class="px-6 py-3 text-right">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($completedSessions as $session)
                                    @php
                                        $totalQuestions = $session->exam->questions_count;
                                        $correctQuestions = $totalQuestions > 0 ? round(($session->score / 100) * $totalQuestions) : 0;
                                    @endphp
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $session->exam->title }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $session->exam->subject->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $session->exam->lecturer->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $session->end_time->format('d F Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($session->isGradingComplete())
                                                {{ $correctQuestions }} / {{ $totalQuestions }}
                                            @else
                                                <span class="text-xs italic text-gray-500">Menunggu Penilaian</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-lg
                                            @if($session->score >= 80) text-green-600
                                            @elseif($session->score >= 60) text-yellow-600
                                            @else text-red-600
                                            @endif">
                                            @if ($session->isGradingComplete())
                                                {{ round($session->score) }}
                                            @else
                                                <span class="text-sm font-normal italic text-gray-500">Menunggu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Anda belum memiliki riwayat ujian yang selesai.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($completedSessions->hasPages())
                        <div class="mt-6">
                            {{ $completedSessions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>