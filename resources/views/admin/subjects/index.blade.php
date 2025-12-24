<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div x-data="{ addModalOpen: false, editModalOpen: false, deleteModalOpen: false, editSubject: {}, deleteSubject: {} }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Daftar Mata Pelajaran</h3>
                            <p class="mt-1 text-sm text-gray-600">Daftar semua mata pelajaran yang ada di sistem.</p>
                        </div>
                        <button @click="addModalOpen = true" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Tambah Mata Pelajaran
                        </button>
                    </div>

                    <!-- Subjects Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama</th>
                                    <th scope="col" class="px-6 py-3">Dosen Pengampu</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subjects as $subject)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $subject->name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $subject->lecturer->name ?? 'Belum Ditugaskan' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end space-x-3">
                                                <button @click="editSubject = {{ json_encode($subject) }}; editModalOpen = true" class="text-gray-400 hover:text-gray-600" title="Ubah">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L14.732 3.732z"></path></svg>
                                                </button>
                                                <button @click="deleteSubject = {{ json_encode($subject) }}; deleteModalOpen = true" class="text-gray-400 hover:text-red-600" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada mata pelajaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $subjects->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Subject Modal -->
        <template x-teleport="body">
            <div x-show="addModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50" @click.self="addModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Mata Pelajaran Baru</h3>
                    <form action="{{ route('admin.subjects.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Mata Pelajaran</label>
                            <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                        <div class="mt-4">
                            <label for="user_id" class="block text-sm font-medium text-gray-700">Tugaskan ke Dosen</label>
                            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">Pilih Dosen</option>
                                @foreach ($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Edit Subject Modal -->
        <template x-teleport="body">
            <div x-show="editModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50" @click.self="editModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Mata Pelajaran</h3>
                    <form :action="'/admin/subjects/' + editSubject.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Nama Mata Pelajaran</label>
                            <input type="text" id="edit_name" name="name" x-model="editSubject.name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div class="mt-4">
                            <label for="edit_description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea id="edit_description" name="description" x-model="editSubject.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                        <div class="mt-4">
                            <label for="edit_user_id" class="block text-sm font-medium text-gray-700">Tugaskan ke Dosen</label>
                            <select id="edit_user_id" name="user_id" x-model="editSubject.user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                <option value="">Pilih Dosen</option>
                                @foreach ($lecturers as $lecturer)
                                    <option :value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Delete Subject Modal -->
        <template x-teleport="body">
            <div x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="deleteModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                    <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin menghapus mata pelajaran <strong x-text="deleteSubject.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                    <form :action="'/admin/subjects/' + deleteSubject.id" method="POST" class="mt-6">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-red-500">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
