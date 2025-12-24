<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h3 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600 mt-1">Selamat belajar dan semoga berhasil dalam ujian Anda.</p>
                    
                    <div class="mt-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('student.exams.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700">
                            Lihat Ujian Tersedia
                        </a>
                        <a href="{{ route('student.results.index') }}" class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-800 bg-white hover:bg-gray-50">
                            Lihat Hasil Ujian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
