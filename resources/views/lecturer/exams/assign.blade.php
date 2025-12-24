<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Tugaskan Mahasiswa untuk Ujian') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Ujian: <span class="font-medium">{{ $exam->title }}</span> | Mata Kuliah: <span class="font-medium">{{ $exam->subject->name }}</span>
                </p>
            </div>
            <a href="{{ route('lecturer.exams.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <form action="{{ route('lecturer.exams.assign.store', $exam) }}" method="POST">
                    @csrf
                    <div x-data="{
                        students: {{ json_encode($students->pluck('id')) }},
                        selected: {{ json_encode($assignedStudentIds) }},
                        toggleAll() {
                            if (this.selected.length === this.students.length) {
                                this.selected = [];
                            } else {
                                this.selected = [...this.students];
                            }
                        },
                        get isAllSelected() {
                            return this.selected.length === this.students.length;
                        }
                    }">
                        <!-- Card Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="font-semibold text-2xl text-gray-900">
                                        Pilih Mahasiswa
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600">Pilih mahasiswa yang akan diizinkan untuk mengerjakan ujian ini.</p>
                                </div>
                                <div class="mt-4 sm:mt-0">
                                    <button type="button" @click="toggleAll()" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        <span x-show="!isAllSelected">Pilih Semua</span>
                                        <span x-show="isAllSelected" style="display: none;">Batal Pilih Semua</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Student List -->
                        <div class="p-6 max-h-96 overflow-y-auto">
                            @if($students->isEmpty())
                                <p class="text-center text-gray-500 py-8">Tidak ada mahasiswa yang terdaftar di mata kuliah ini.</p>
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                    @foreach ($students as $student)
                                        <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-400 transition-colors duration-150">
                                            <input type="checkbox" name="students[]" value="{{ $student->id }}" x-model="selected" class="h-5 w-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            <div class="ml-3">
                                                <span class="font-medium text-sm text-gray-800">{{ $student->name }}</span>
                                                <span class="block text-xs text-gray-500">{{ $student->npm }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-gray-50 px-6 py-4 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50" :disabled="students.length === 0">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
