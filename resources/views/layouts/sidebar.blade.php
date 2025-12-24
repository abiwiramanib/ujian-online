<!-- Unified Sidebar -->
<div class="h-full flex flex-col bg-white text-gray-600 border-r border-gray-200">
    <!-- Logo -->
    <div class="p-4 border-b border-gray-200">
        <a href="
            @if(Auth::user()->role === 'admin') {{ route('admin.dashboard') }}
            @elseif(Auth::user()->role === 'dosen') {{ route('lecturer.dashboard') }}
            @else {{ route('dashboard') }}
            @endif
        " class="flex items-center justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="SIMUJOL Logo" class="h-9 w-auto">
            <span class="text-2xl font-bold text-indigo-600 ml-3">SIMUJOL</span>
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-2 py-4 space-y-1">
        @if(Auth::user()->role === 'admin')
            {{-- Admin Links --}}
            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Dasbor
            </a>
            <div class="mt-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen Sistem</p>
                <div class="mt-1 space-y-1">
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                        <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Pengguna
                    </a>
                    <a href="{{ route('admin.subjects.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.subjects.*') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                        <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" /></svg>
                        Mata Pelajaran
                    </a>
                    <a href="{{ route('admin.subject-requests.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.subject-requests.*') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                        <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        Permintaan MK
                    </a>
                </div>
            </div>

        @elseif(Auth::user()->role === 'dosen')
            {{-- Lecturer Links --}}
            <a href="{{ route('lecturer.dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('lecturer.dashboard') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Dasbor
            </a>

            <!-- Exam Management Group -->
            <div class="mt-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen Ujian</p>
                <div class="mt-1 space-y-1">
                    <a href="{{ route('lecturer.exams.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('lecturer.exams.*') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                        <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Kelola Ujian
                    </a>
                    <a href="{{ route('lecturer.question-bank.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('lecturer.question-bank.*') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 mr-3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 0 0 0-8.25H6.75Zm0 0v.38m0 16.122h6.747a4.5 4.5 0 0 0 0-9.001h-7.5v9h.753Zm0 0v-.37m0-15.751h6a3.75 3.75 0 1 1 0 7.5h-6m0-7.5v7.5m0 0v8.25m0-8.25h6.375a4.125 4.125 0 0 1 0 8.25H6.75m.747-15.38h4.875a3.375 3.375 0 0 1 0 6.75H7.497v-6.75Zm0 7.5h5.25a3.75 3.75 0 0 1 0 7.5h-5.25v-7.5Z" />
                        </svg>
                        Bank Soal
                    </a>
                </div>
            </div>

            <!-- Class Management Group -->
            <div class="mt-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen Kelas</p>
                <div class="mt-1 space-y-1">
                    <a href="{{ route('lecturer.subjects.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('lecturer.subjects.*') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 mr-3">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        Kelola Mata Pelajaran
                    </a>
                </div>
            </div>

        @else
            {{-- Student Links --}}
            <a href="{{ route('dashboard') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Dasbor
            </a>
            <a href="{{ route('student.exams.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.exams.*') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h2" /></svg>
                Ujian Tersedia
            </a>
            <a href="{{ route('student.results.index') }}" class="flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('student.results.*') ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-gray-100 hover:text-indigo-700' }}">
                <svg class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                Hasil Ujian
            </a>
        @endif
    </nav>
</div>
