<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Soal Ujian') }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">
            Ujian: <span class="font-semibold">{{ $exam->title }}</span>
        </p>
    </x-slot>

        <div x-data="{ 

            addModalOpen: false,

            editModalOpen: false,

            deleteModalOpen: false,

            isSubmitting: false,

            newQuestion: {

                question_text: '',

                type: 'multiple_choice',

                options: ['', ''],

                correct_option: 0,

                image_path: null

            },

            editQuestion: { id: null, question_text: '', type: 'multiple_choice', options: [], correct_option: null, image_path: null, delete_image: false },

            deleteQuestion: {},

    

            addOption(form) { this[form].options.push(''); },

            removeOption(form, index) {

                if (this[form].options.length > 2) {

                    const wasCorrect = this[form].correct_option === index;

                    this[form].options.splice(index, 1);

                    if (wasCorrect) {

                        this[form].correct_option = 0;

                    } else if (this[form].correct_option > index) {

                        this[form].correct_option--;

                    }

                }

            },

            resetNewForm() {

                this.newQuestion = { question_text: '', type: 'multiple_choice', options: ['', ''], correct_option: 0, image_path: null };

                if (this.$refs.newImageInput) this.$refs.newImageInput.value = '';

            },

            openEditModal(question) {

                this.editQuestion.id = question.id;

                this.editQuestion.question_text = question.question_text;

                this.editQuestion.type = question.type;

                this.editQuestion.image_path = question.image_path;

                this.editQuestion.options = question.options.map(opt => opt.option_text);

                this.editQuestion.correct_option = question.options.findIndex(opt => opt.is_correct);

                this.editQuestion.delete_image = false;

                this.editModalOpen = true;

                this.$nextTick(() => {

                    if (this.$refs.editImageInput) this.$refs.editImageInput.value = '';

                });

            },

            submitEditForm() {

                this.isSubmitting = true;

                const formData = new FormData(this.$refs.editForm);

                

                // Append Alpine data, as x-model doesn't update the form's value attribute for native submission

                formData.set('question_text', this.editQuestion.question_text);

                formData.set('type', this.editQuestion.type);

                formData.set('delete_image', this.editQuestion.delete_image ? '1' : '0');

                

                // Clear old options and append new ones

                formData.delete('options[]');

                if (this.editQuestion.type === 'multiple_choice') {

                    this.editQuestion.options.forEach((option, index) => {

                        formData.append(`options[${index}]`, option);

                    });

                    formData.set('correct_option', this.editQuestion.correct_option);

                }

    

                fetch(`/lecturer/questions/${this.editQuestion.id}`, {

                    method: 'POST',

                    headers: {

                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),

                        'Accept': 'application/json',

                    },

                    body: formData,

                })

                .then(response => {

                    if (!response.ok) {

                        // Handle validation errors or other server errors

                        return response.json().then(data => Promise.reject(data));

                    }

                    return response.json();

                })

                            .then(data => {

                                sessionStorage.setItem('toastMessage', data.message || 'Perubahan berhasil disimpan!');

                                sessionStorage.setItem('toastType', 'success');

                                window.location.reload();

                            })

                .catch(error => {

                    console.error('Error:', error);

                    alert('Gagal menyimpan perubahan. Periksa konsol untuk detail.');

                    this.isSubmitting = false;

                });

            }

        }" class="py-12">

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-4">

                    <a href="{{ route('lecturer.exams.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">

                        &larr; Kembali ke Daftar Ujian

                    </a>

                </div>

    

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                    <div class="p-6 md:p-8">

                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">

                            <div>

                                <h3 class="text-lg font-medium text-gray-900">Daftar Soal</h3>

                                <p class="mt-1 text-sm text-gray-600">Daftar semua soal untuk ujian ini.</p>

                            </div>

                            <button @click="addModalOpen = true" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">

                                Tambah Soal Baru

                            </button>

                        </div>

    

                        <!-- Questions List -->

                        <div class="space-y-6">

                            @forelse ($exam->questions as $index => $question)

                                <div class="bg-gray-50 p-4 rounded-lg shadow-sm">

                                    <div class="flex justify-between items-start">

                                        <div class="prose max-w-none">

                                            <div class="flex items-center gap-4">

                                                <p class="font-semibold m-0">Soal #{{ $index + 1 }}:</p>

                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $question->type === 'essay' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">

                                                    {{ $question->type === 'essay' ? 'Esai' : 'Pilihan Ganda' }}

                                                </span>

                                            </div>

                                            @if($question->image_path)

                                                <img src="{{ Storage::url($question->image_path) }}" alt="Gambar Soal" class="mt-4 max-h-48 rounded-lg shadow-sm">

                                            @endif

                                            <p class="mt-2">{{ $question->question_text }}</p>

                                        </div>

                                        <div class="flex-shrink-0 ml-4 flex space-x-2">

                                            <button @click="openEditModal({{ json_encode($question) }})" class="text-gray-400 hover:text-gray-600" title="Ubah">

                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L14.732 3.732z"></path></svg>

                                            </button>

                                            <button @click="deleteQuestion = {{ json_encode($question) }}; deleteModalOpen = true" class="text-gray-400 hover:text-red-600" title="Hapus">

                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>

                                            </button>

                                        </div>

                                    </div>

                                    @if($question->type === 'multiple_choice')

                                    <div class="mt-4 pl-6 border-l-2 border-gray-200">

                                        <p class="text-sm font-medium text-gray-600 mb-2">Pilihan Jawaban:</p>

                                        <div class="space-y-2">

                                            @foreach ($question->options as $option)

                                                <div class="flex items-center">

                                                    @if ($option->is_correct)

                                                        <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>

                                                        <span class="font-semibold text-green-700">{{ $option->option_text }}</span>

                                                    @else

                                                        <svg class="h-5 w-5 text-gray-400 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>

                                                        <span>{{ $option->option_text }}</span>

                                                    @endif

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                    @endif

                                </div>

                            @empty

                                <div class="text-center py-12">

                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">

                                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />

                                    </svg>

                                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada soal</h3>

                                    <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan soal pertama untuk ujian ini.</p>

                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

    

            <!-- Add Question Modal -->

            <template x-teleport="body">

                <div x-show="addModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50" @click.self="addModalOpen = false; resetNewForm()">

                    <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-2xl">

                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Soal Baru</h3>

                        

                        <!-- Validation Errors -->

                        @if ($errors->any() && session('modal') === 'add')

                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">

                                <strong class="font-bold">Oops!</strong>

                                <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>

                                <ul class="mt-3 list-disc list-inside text-sm">

                                    @foreach ($errors->all() as $error)

                                        <li>{{ $error }}</li>

                                    @endforeach

                                </ul>

                            </div>

                        @endif

    

                        <form action="{{ route('lecturer.exams.questions.store', $exam) }}" method="POST" enctype="multipart/form-data">

                            @csrf

                            <input type="hidden" name="modal" value="add">

                            <div class="space-y-6">

                                <div>

                                    <label for="question_text" class="block text-sm font-medium text-gray-700">Teks Soal</label>

                                    <textarea id="question_text" name="question_text" x-model="newQuestion.question_text" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required></textarea>

                                </div>

                                <div>

                                    <label for="image" class="block text-sm font-medium text-gray-700">Gambar (Opsional)</label>

                                    <input type="file" name="image" id="image" x-ref="newImageInput" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">

                                </div>

                                <div>

                                    <label for="type" class="block text-sm font-medium text-gray-700">Tipe Soal</label>

                                    <select id="type" name="type" x-model="newQuestion.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                        <option value="multiple_choice">Pilihan Ganda</option>

                                        <option value="essay">Esai</option>

                                    </select>

                                </div>

                                <fieldset :disabled="newQuestion.type !== 'multiple_choice'">

                                    <div x-show="newQuestion.type === 'multiple_choice'" class="space-y-6">

                                        <div>

                                            <label class="block text-sm font-medium text-gray-700">Pilihan Jawaban (pilih satu yang benar)</label>

                                            <div class="mt-2 space-y-3">

                                                <template x-for="(option, index) in newQuestion.options" :key="index">

                                                    <div class="flex items-center space-x-3">

                                                        <input type="radio" name="correct_option" :value="index" x-model.number="newQuestion.correct_option" class="h-4 w-4 text-indigo-600 border-gray-300">

                                                        <input type="text" :name="'options[' + index + ']'" x-model="newQuestion.options[index]" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="Teks pilihan jawaban" required>

                                                        <button type="button" @click="removeOption('newQuestion', index)" x-show="newQuestion.options.length > 2" class="text-gray-400 hover:text-red-600 p-1 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>

                                                    </div>

                                                </template>

                                            </div>

                                            <button type="button" @click="addOption('newQuestion')" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-500">+ Tambah Pilihan</button>

                                        </div>

                                    </div>

                                </fieldset>

                            </div>

                            <div class="mt-8 flex justify-end space-x-4">

                                <button type="button" @click="addModalOpen = false; resetNewForm()" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Batal</button>

                                <button type="submit" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase">Simpan Soal</button>

                            </div>

                        </form>

                    </div>

                </div>

            </template>

    

            <!-- Edit Question Modal -->

            <template x-teleport="body">

                <div x-show="editModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50" @click.self="editModalOpen = false">

                    <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-2xl">

                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Soal</h3>

    

                        <!-- Validation Errors -->

                        @if ($errors->any() && session('modal') === 'edit' && session('question_id') == $errors->getBag('default')->first('question_id'))

                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">

                                <strong class="font-bold">Oops!</strong>

                                <span class="block sm:inline">Ada beberapa masalah dengan input Anda.</span>

                                <ul class="mt-3 list-disc list-inside text-sm">

                                    @foreach ($errors->all() as $error)

                                        @if(!is_numeric($error))

                                            <li>{{ $error }}</li>

                                        @endif

                                    @endforeach

                                </ul>

                            </div>

                        @endif

    

                        <form x-ref="editForm" @submit.prevent="submitEditForm">

                            <input type="hidden" name="_method" value="PUT">

                            <input type="hidden" name="modal" value="edit">

                            <input type="hidden" name="question_id" :value="editQuestion.id">

                            <div class="space-y-6">

                                <div>

                                    <label for="edit_question_text" class="block text-sm font-medium text-gray-700">Teks Soal</label>

                                    <textarea id="edit_question_text" name="question_text" x-model="editQuestion.question_text" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required></textarea>

                                </div>

                                <div>

                                    <label for="edit_image" class="block text-sm font-medium text-gray-700">Gambar (Opsional)</label>

                                    <input type="file" name="image" id="edit_image" x-ref="editImageInput" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">

                                    <template x-if="editQuestion.image_path">

                                        <div class="mt-2 flex items-center space-x-2">

                                            <img :src="'/storage/' + editQuestion.image_path" alt="Gambar Soal" class="max-h-24 rounded-lg shadow-sm">

                                            <button type="button" @click="editQuestion.delete_image = true; editQuestion.image_path = null" class="text-red-600 hover:text-red-800 text-sm">Hapus Gambar</button>

                                        </div>

                                    </template>

                                    <input type="hidden" name="delete_image" x-model="editQuestion.delete_image">

                                </div>

                                <div>

                                    <label for="edit_type" class="block text-sm font-medium text-gray-700">Tipe Soal</label>

                                    <select id="edit_type" name="type" x-model="editQuestion.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                                        <option value="multiple_choice">Pilihan Ganda</option>

                                        <option value="essay">Esai</option>

                                    </select>

                                </div>

                                <fieldset :disabled="editQuestion.type !== 'multiple_choice'">

                                    <div x-show="editQuestion.type === 'multiple_choice'" class="space-y-6">

                                        <div>

                                            <label class="block text-sm font-medium text-gray-700">Pilihan Jawaban (pilih satu yang benar)</label>

                                            <div class="mt-2 space-y-3">

                                                <template x-for="(option, index) in editQuestion.options" :key="index">

                                                    <div class="flex items-center space-x-3">

                                                        <input type="radio" name="correct_option" :value="index" x-model.number="editQuestion.correct_option" class="h-4 w-4 text-indigo-600 border-gray-300">

                                                        <input type="text" :name="'options[' + index + ']'" x-model="editQuestion.options[index]" class="block w-full rounded-md border-gray-300 shadow-sm" placeholder="Teks pilihan jawaban" required>

                                                        <button type="button" @click="removeOption('editQuestion', index)" x-show="editQuestion.options.length > 2" class="text-gray-400 hover:text-red-600 p-1 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>

                                                    </div>

                                                </template>

                                            </div>

                                            <button type="button" @click="addOption('editQuestion')" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-500">+ Tambah Pilihan</button>

                                        </div>

                                    </div>

                                </fieldset>

                            </div>

                            <div class="mt-8 flex justify-end space-x-4">

                                <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase" :disabled="isSubmitting">Batal</button>

                                <button type="submit" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase inline-flex items-center" :disabled="isSubmitting">

                                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">

                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>

                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>

                                    </svg>

                                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </template>

    

            <!-- Delete Question Modal -->

            <template x-teleport="body">

                <div x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="deleteModalOpen = false">

                    <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">

                        <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>

                        <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.</p>

                        <form :action="`/lecturer/questions/${deleteQuestion.id}`" method="POST" class="mt-6">

                            @csrf

                            @method('DELETE')

                            <div class="flex justify-end space-x-4">

                                <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Batal</button>

                                <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-red-500">Hapus Soal</button>

                            </div>

                        </form>

                    </div>

                </div>

            </template>

        </div>

    </x-app-layout>

    