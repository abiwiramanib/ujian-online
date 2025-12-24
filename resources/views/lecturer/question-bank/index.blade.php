<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Semua Soal Anda</h3>
                            <p class="mt-1 text-sm text-gray-600">Pusat semua soal yang pernah Anda buat di semua ujian.
                            </p>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('lecturer.question-bank.index') }}">
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="w-full md:w-1/3">
                                <label for="subject" class="block text-sm font-medium text-gray-700">Filter per Mata
                                    Pelajaran:</label>
                                <select name="subject" id="subject"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    onchange="this.form.submit()">
                                    <option value="">Semua Mata Pelajaran</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}"
                                            {{ request('subject') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <!-- Questions Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Teks Soal</th>
                                    <th scope="col" class="px-6 py-3">Ujian</th>
                                    <th scope="col" class="px-6 py-3">Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($questions as $question)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ Str::limit($question->question_text, 80) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $question->exam->title }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $question->exam->subject->name }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada soal ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $questions->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
