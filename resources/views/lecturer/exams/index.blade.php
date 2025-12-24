<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-5 shadow-lg rounded-lg flex items-center justify-between">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Ujian</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="bg-white p-5 shadow-lg rounded-lg flex items-center justify-between">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 truncate">Draft</dt>
                        <dd class="mt-1 text-3xl font-semibold text-yellow-500">{{ $stats['draft'] }}</dd>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="bg-white p-5 shadow-lg rounded-lg flex items-center justify-between">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 truncate">Dipublikasikan</dt>
                        <dd class="mt-1 text-3xl font-semibold text-green-500">{{ $stats['published'] }}</dd>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="bg-white p-5 shadow-lg rounded-lg flex items-center justify-between">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 truncate">Selesai</dt>
                        <dd class="mt-1 text-3xl font-semibold text-red-500">{{ $stats['finished'] }}</dd>
                    </div>
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-semibold text-2xl text-gray-900">
                                Manajemen Ujian
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">Buat, kelola, dan pantau semua ujian Anda.</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <button @click="addModalOpen = true"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Buat Ujian Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <form id="filter-form" method="GET" action="{{ route('lecturer.exams.index') }}">
                    <div class="bg-gray-50 px-4 py-4 flex flex-col sm:flex-row gap-4 items-center">
                        <div class="flex-1 w-full sm:w-auto">
                            <label for="search" class="sr-only">Search</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Cari ujian atau mata kuliah...">
                            </div>
                        </div>
                        <div class="flex gap-4 w-full sm:w-auto">
                            <select name="status"
                                class="block w-full sm:w-auto text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                                <option value="published" @selected(request('status') == 'published')>Dipublikasikan</option>
                                <option value="finished" @selected(request('status') == 'finished')>Selesai</option>
                            </select>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Exams Table & Pagination Container -->
                <div id="exam-list-container">
                    @include('lecturer.exams._exam-list')
                </div>
            </div>
        </div>
    </div>

    <x-slot name="modals">
        <!-- Add Exam Modal -->
        <div x-show="addModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="addModalOpen" x-transition.opacity @click="addModalOpen = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                <div x-show="addModalOpen" x-transition
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form action="{{ route('lecturer.exams.store') }}" method="POST">
                        @csrf
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Buat Ujian Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Judul
                                    Ujian</label>
                                <input type="text" name="title" id="title" required
                                    class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="e.g., Ujian Tengah Semester">
                            </div>
                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700">Mata
                                    Kuliah</label>
                                <select id="subject_id" name="subject_id" required
                                    class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Pilih mata kuliah</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700">Durasi
                                    (menit)</label>
                                <input type="number" name="duration" id="duration" required min="1"
                                    class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="60">
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi
                                    <span class="text-gray-400">(opsional)</span></label>
                                <textarea id="description" name="description" rows="3"
                                    class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Masukkan deskripsi atau instruksi ujian..."></textarea>
                            </div>
                        </div>
                        <div class="mt-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                            <button type="button" @click="addModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Exam Modal -->
        <div x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModalOpen" x-transition.opacity @click="editModalOpen = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
                <div x-show="editModalOpen" x-transition
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form :action="'/lecturer/exams/' + editExam.id" method="POST">
                        @csrf
                        @method('PUT')
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ubah Ujian</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="edit_title" class="block text-sm font-medium text-gray-700">Judul
                                    Ujian</label>
                                <input type="text" id="edit_title" name="title" x-model="editExam.title"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="edit_subject_id" class="block text-sm font-medium text-gray-700">Mata
                                    Kuliah</label>
                                <select id="edit_subject_id" name="subject_id" x-model="editExam.subject_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="">Pilih Mata Kuliah</option>
                                    @foreach ($subjects as $subject)
                                        <option :value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="edit_duration" class="block text-sm font-medium text-gray-700">Durasi
                                    (menit)</label>
                                <input type="number" id="edit_duration" name="duration" x-model="editExam.duration"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="edit_description"
                                    class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea id="edit_description" name="description" x-model="editExam.description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan
                                Perubahan</button>
                            <button type="button" @click="editModalOpen = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Exam Modal -->
        <div x-show="deleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex min-h-screen items-center justify-center">
                <div x-show="deleteModalOpen" x-transition.opacity @click="deleteModalOpen = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <div x-show="deleteModalOpen" x-transition
                    class="bg-white rounded-lg shadow-xl transform sm:max-w-lg sm:w-full p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Ujian</h3>
                            <p class="text-sm text-gray-500">Anda yakin ingin menghapus ujian <strong
                                    x-text="deleteExam.title"></strong>? Semua data terkait ujian ini akan dihapus
                                permanen. Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                    <form :action="'/lecturer/exams/' + deleteExam.id" method="POST"
                        class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Hapus</button>
                        <button type="button" @click="deleteModalOpen = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Publish Exam Modal -->
        <div x-show="publishModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex min-h-screen items-center justify-center">
                <div x-show="publishModalOpen" x-transition.opacity @click="publishModalOpen = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <div x-show="publishModalOpen" x-transition
                    class="bg-white rounded-lg shadow-xl transform sm:max-w-lg sm:w-full p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" stroke="currentColor" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Publikasikan Ujian</h3>
                            <p class="text-sm text-gray-500">Anda yakin ingin mempublikasikan ujian <strong x-text="publishExam.title"></strong>? Setelah dipublikasikan, token akan dibuat dan ujian akan dapat diakses oleh mahasiswa.</p>
                        </div>
                    </div>
                    <form :action="'/lecturer/exams/' + publishExam.id + '/publish'" method="POST"
                        class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">Ya, Publikasikan</button>
                        <button type="button" @click="publishModalOpen = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Finish Exam Modal -->
        <div x-show="finishModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex min-h-screen items-center justify-center">
                <div x-show="finishModalOpen" x-transition.opacity @click="finishModalOpen = false"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <div x-show="finishModalOpen" x-transition
                    class="bg-white rounded-lg shadow-xl transform sm:max-w-lg sm:w-full p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-yellow-600" stroke="currentColor" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Akhiri Ujian</h3>
                            <p class="text-sm text-gray-500">Anda yakin ingin mengakhiri ujian <strong
                                    x-text="finishExam.title"></strong>? Mahasiswa tidak akan bisa memulai atau
                                melanjutkan ujian ini lagi.</p>
                        </div>
                    </div>
                    <form :action="'/lecturer/exams/' + finishExam.id + '/finish'" method="POST"
                        class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-500 text-base font-medium text-white hover:bg-yellow-600 sm:ml-3 sm:w-auto sm:text-sm">Ya,
                            Akhiri Ujian</button>
                        <button type="button" @click="finishModalOpen = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterForm = document.getElementById('filter-form');
            const container = document.getElementById('exam-list-container');

            if (filterForm) {
                filterForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    performFilter();
                });

                const inputs = filterForm.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        // Automatically filter on change for select, but require submit for search
                        if (this.type === 'select-one') {
                            performFilter();
                        }
                    });
                });
            }

            function performFilter() {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                const url = `${filterForm.action}?${params.toString()}`;

                // Add a loading indicator
                container.style.opacity = '0.5';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                    // Update browser URL
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error('Error filtering exams:', error);
                    // Maybe show an error message to the user
                })
                .finally(() => {
                    container.style.opacity = '1';
                });
            }

            // Handle pagination links using event delegation
            document.addEventListener('click', function(event) {
                // Check if the clicked element is a pagination link within the container
                if (event.target.closest('#exam-list-container .pagination a')) {
                    event.preventDefault();
                    const link = event.target.closest('a');
                    const url = link.href;
                    
                    container.style.opacity = '0.5';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        window.history.pushState({}, '', url);
                    })
                    .catch(error => console.error('Error fetching page:', error))
                    .finally(() => {
                        container.style.opacity = '1';
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
