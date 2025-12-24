<!-- Exams Table -->
<div class="overflow-x-auto">
    <table class="min-w-full">
        <thead class="border-b border-gray-200">
            <tr>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ujian</th>
                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mata Kuliah</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Soal</th>
                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($exams as $exam)
                <tr class="odd:bg-white even:bg-gray-50 hover:bg-indigo-50 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-800">{{ $exam->title }}</div>
                        @if ($exam->status == 'published' && $exam->token)
                            <div class="text-xs text-gray-500 mt-1">Token: <code class="text-xs bg-gray-200 px-2 py-1 rounded font-mono">{{ $exam->token }}</code></div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $exam->subject->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $exam->duration }} min</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">{{ $exam->questions_count }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @switch($exam->status)
                                @case('draft') bg-yellow-100 text-yellow-800 @break
                                @case('published') bg-green-100 text-green-800 @break
                                @case('finished') bg-red-100 text-red-800 @break
                            @endswitch">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="text-gray-500 hover:text-gray-700 focus:outline-none transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                @if ($exam->status == 'draft')
                                    <div x-data="{ tooltip: false }" class="relative">
                                        <button 
                                            @if($exam->questions_count > 0)
                                                @click="openPublishModal(JSON.parse(`{{ json_encode($exam) }}`))"
                                            @else
                                                disabled
                                                @mouseenter="tooltip = true"
                                                @mouseleave="tooltip = false"
                                            @endif
                                            class="flex items-center w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ __('Publikasikan') }}</span>
                                        </button>
                                        <div x-show="tooltip" x-transition style="display: none;" class="absolute z-10 w-48 p-2 -mt-4 text-sm leading-tight text-white transform -translate-x-full -translate-y-1/2 bg-black rounded-lg shadow-lg">
                                            Ujian tidak memiliki soal.
                                        </div>
                                    </div>
                                @endif

                                @if ($exam->status == 'published')
                                    <button @click="openFinishModal(JSON.parse(`{{ json_encode($exam) }}`))" class="flex items-center w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        <span>{{ __('Akhiri Ujian') }}</span>
                                    </button>
                                    <div class="border-t border-gray-100"></div>
                                    <x-dropdown-link :href="route('lecturer.exams.assign', $exam)" class="flex items-center">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <span>{{ __('Tugaskan Mahasiswa') }}</span>
                                    </x-dropdown-link>
                                    <div class="border-t border-gray-100"></div>
                                @endif
                                
                                <!-- Management Actions -->
                                <x-dropdown-link :href="route('lecturer.exams.questions.index', $exam)" class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                                    <span>{{ __('Kelola Soal') }}</span>
                                </x-dropdown-link>

                                <!-- Reporting Actions -->
                                <div class="border-t border-gray-100"></div>
                                <x-dropdown-link :href="route('lecturer.exams.results', $exam)" class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    <span>{{ __('Lihat Hasil') }}</span>
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('lecturer.exams.logs', $exam)" class="flex items-center">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <span>{{ __('Lihat Log') }}</span>
                                </x-dropdown-link>

                                <!-- Destructive Actions -->
                                <div class="border-t border-gray-100"></div>
                                <button @click="openEditModal(JSON.parse(`{{ json_encode($exam) }}`))" class="flex items-center w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z"></path></svg>
                                    <span>{{ __('Ubah') }}</span>
                                </button>
                                <button @click="openDeleteModal(JSON.parse(`{{ json_encode($exam) }}`))" class="flex items-center w-full px-4 py-2 text-start text-sm leading-5 text-red-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>{{ __('Hapus') }}</span>
                                </button>
                            </x-slot>
                        </x-dropdown>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-24 text-center">
                        <div class="max-w-md mx-auto">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">
                                @if(request()->has('search') || request()->has('status'))
                                    Tidak ada ujian yang cocok
                                @else
                                    Belum ada ujian
                                @endif
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request()->has('search') || request()->has('status'))
                                    Coba ubah kata kunci pencarian atau filter status Anda.
                                @else
                                    Mulai dengan membuat ujian pertama Anda.
                                @endif
                            </p>
                            @if(!request()->has('search') && !request()->has('status'))
                            <div class="mt-6">
                                <button @click="addModalOpen = true" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                    Buat Ujian Pertama
                                </button>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if ($exams->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $exams->links() }}
    </div>
@endif
