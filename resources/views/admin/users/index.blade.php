<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div x-data="{ addModalOpen: false, editModalOpen: false, deleteModalOpen: false, editUser: {}, deleteUser: {} }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Daftar Pengguna</h3>
                            <p class="mt-1 text-sm text-gray-600">Daftar semua pengguna di sistem termasuk nama, email, dan peran mereka.</p>
                        </div>
                        <button @click="addModalOpen = true" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Tambah Pengguna Baru
                        </button>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama</th>
                                    <th scope="col" class="px-6 py-3">Peran</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-500"><span class="text-sm font-medium leading-none text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span></span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full @if($user->role == 'admin') bg-red-100 text-red-800 @elseif($user->role == 'dosen') bg-blue-100 text-blue-800 @else bg-green-100 text-green-800 @endif">
                                                {{ $user->role }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end space-x-3">
                                                <button @click="editUser = {{ json_encode($user) }}; editModalOpen = true" class="text-gray-400 hover:text-gray-600" title="Ubah">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L14.732 3.732z"></path></svg>
                                                </button>
                                                <button @click="deleteUser = {{ json_encode($user) }}; deleteModalOpen = true" class="text-gray-400 hover:text-red-600" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b"><td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada pengguna.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $users->links() }}</div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <template x-teleport="body">
            <div x-show="addModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50" @click.self="addModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Pengguna Baru</h3>
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="add_name" class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" id="add_name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="mt-4">
                            <label for="add_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="add_email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="mt-4">
                            <label for="add_password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" id="add_password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="mt-4">
                            <label for="add_role" class="block text-sm font-medium text-gray-700">Peran</label>
                            <select id="add_role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button type="button" @click="addModalOpen = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Batal</button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Edit User Modal -->
        <template x-teleport="body">
            <div x-show="editModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50" @click.self="editModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Pengguna</h3>
                    <form :action="'/admin/users/' + editUser.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" id="edit_name" x-model="editUser.name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="mt-4">
                            <label for="edit_email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="edit_email" x-model="editUser.email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="mt-4">
                            <label for="edit_role" class="block text-sm font-medium text-gray-700">Peran</label>
                            <select id="edit_role" x-model="editUser.role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="admin">Admin</option>
                                <option value="dosen">Dosen</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button type="button" @click="editModalOpen = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Batal</button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Delete User Modal -->
        <template x-teleport="body">
            <div x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="deleteModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                    <p class="mt-2 text-sm text-gray-600">Apakah Anda yakin ingin menghapus pengguna <strong x-text="deleteUser.name"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                    <form :action="'/admin/users/' + deleteUser.id" method="POST" class="mt-6">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-red-500">Hapus Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
