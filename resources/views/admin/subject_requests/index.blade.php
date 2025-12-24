<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permintaan Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div x-data="{ rejectModalOpen: false, approveModalOpen: false, selectedRequest: {} }" class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium">Daftar Permintaan</h3>
                        <p class="mt-1 text-sm text-gray-600">Review dan proses permintaan mata pelajaran baru dari para dosen.</p>
                    </div>

                    <!-- Requests Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Dosen</th>
                                    <th scope="col" class="px-6 py-3">Detail Mata Pelajaran</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $request)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $request->lecturer->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $request->lecturer->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $request->name }}</div>
                                            <div class="text-xs text-gray-500">Kode: {{ $request->code ?: 'N/A' }}</div>
                                            @if($request->description)
                                            <p class="mt-1 text-xs text-gray-600 max-w-xs truncate" title="{{ $request->description }}">
                                                {{ $request->description }}
                                            </p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($request->status == 'approved') bg-green-100 text-green-800
                                                @elseif($request->status == 'rejected') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $request->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if ($request->status == 'pending')
                                                <div class="flex items-center justify-end space-x-4">
                                                    <button @click="selectedRequest = {{ json_encode($request) }}; approveModalOpen = true" class="font-medium text-green-600 hover:underline">
                                                        Setujui
                                                    </button>
                                                    <button @click="selectedRequest = {{ json_encode($request) }}; rejectModalOpen = true" class="font-medium text-red-600 hover:underline">
                                                        Tolak
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">Telah diproses</span>
                                                @if($request->status == 'rejected' && $request->admin_notes)
                                                <p class="mt-1 text-xs text-red-500 max-w-xs truncate" title="Alasan: {{ $request->admin_notes }}">
                                                    Alasan: {{ $request->admin_notes }}
                                                </p>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            Belum ada permintaan mata pelajaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Request Modal -->
        <template x-teleport="body">
            <div x-show="approveModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50" @click.self="approveModalOpen = false" x-cloak>
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900">Setujui Permintaan Mata Pelajaran</h3>
                    <p class="mt-2 text-sm text-gray-600">Anda yakin ingin menyetujui dan membuat mata pelajaran <strong x-text="selectedRequest.name"></strong>?</p>
                    
                    <form :action="'/admin/subject-requests/' + selectedRequest.id + '/approve'" method="POST" class="mt-6">
                        @csrf
                        @method('PATCH')
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="approveModalOpen = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Batal</button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">Ya, Setujui</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <!-- Reject Request Modal -->
        <template x-teleport="body">
            <div x-show="rejectModalOpen" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50" @click.self="rejectModalOpen = false" x-cloak>
                <div class="bg-white rounded-lg shadow-xl p-6 md:p-8 w-full max-w-md">
                    <h3 class="text-lg font-medium text-gray-900">Tolak Permintaan Mata Pelajaran</h3>
                    <p class="mt-1 text-sm text-gray-600">Anda akan menolak mata pelajaran <strong x-text="selectedRequest.name"></strong>.</p>
                    
                    <form :action="'/admin/subject-requests/' + selectedRequest.id + '/reject'" method="POST" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="admin_notes" class="block text-sm font-medium text-gray-700">Alasan Penolakan (Opsional)</label>
                            <textarea id="admin_notes" name="admin_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        </div>
                        <div class="mt-6 flex justify-end space-x-4">
                            <button type="button" @click="rejectModalOpen = false" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Batal</button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">Tolak Permintaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
