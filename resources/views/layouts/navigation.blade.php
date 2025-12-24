<!-- Top Navigation -->
<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side: Hamburger and Title -->
            <div class="flex items-center">
                <!-- Hamburger Menu -->
                <div class="flex items-center md:hidden">
                    <button @click="$dispatch('toggle-sidebar')" 
                            class="inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Breadcrumb Navigation -->
                <div class="flex-1 ms-4 md:ms-0">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            @php
                                $routeName = Route::currentRouteName();
                                $userRole = Auth::user()->role;
                            @endphp

                            <!-- Dashboard (Home) -->
                            <li class="inline-flex items-center">
                                <a href="
                                    @if ($userRole === 'admin')
                                        {{ route('admin.dashboard') }}
                                    @elseif ($userRole === 'dosen')
                                        {{ route('lecturer.dashboard') }}
                                    @else
                                        {{ route('dashboard') }}
                                    @endif
                                " 
                                   class="inline-flex items-center text-sm font-medium {{ 
                                       (request()->routeIs('admin.dashboard') || request()->routeIs('lecturer.dashboard') || request()->routeIs('dashboard'))
                                       ? 'text-gray-900' 
                                       : 'text-gray-500 hover:text-gray-700' 
                                   }}">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                    </svg>
                                    Dashboard
                                </a>
                            </li>

                            @if(!in_array($routeName, ['admin.dashboard', 'lecturer.dashboard', 'dashboard']))
                                <!-- Separator -->
                                <li>
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>

                                        @php
                                            $breadcrumbName = '';
                                            $breadcrumbRoute = '';
                                            
                                            // Map route names to breadcrumb labels
                                            $routeMap = [
                                                // Lecturer routes
                                                'lecturer.exams.index' => 'Ujian',
                                                'lecturer.exams.create' => 'Ujian > Buat Baru',
                                                'lecturer.exams.edit' => 'Ujian > Edit',
                                                'lecturer.exams.questions.index' => 'Ujian > Kelola Soal',
                                                'lecturer.exams.results' => 'Ujian > Hasil',
                                                'lecturer.exams.logs' => 'Ujian > Log',
                                                'lecturer.question-bank.index' => 'Bank Soal',
                                                'lecturer.question-bank.create' => 'Bank Soal > Tambah',
                                                'lecturer.question-bank.edit' => 'Bank Soal > Edit',
                                                'lecturer.subjects.index' => 'Mata Kuliah',
                                                'lecturer.subjects.enroll' => 'Mata Kuliah > Kelola Peserta',
                                                
                                                // Student routes
                                                'student.exams.index' => 'Ujian',
                                                'student.exams.start' => 'Ujian > Mulai',
                                                'student.exams.take' => 'Ujian > Sedang Berlangsung',
                                                'student.results.index' => 'Hasil Ujian',
                                                'student.results.show' => 'Hasil Ujian > Detail',
                                                
                                                // Admin routes
                                                'admin.users.index' => 'Pengguna',
                                                'admin.users.create' => 'Pengguna > Tambah',
                                                'admin.users.edit' => 'Pengguna > Edit',
                                                'admin.subjects.index' => 'Mata Pelajaran',
                                                'admin.subjects.create' => 'Mata Pelajaran > Tambah',
                                                'admin.subjects.edit' => 'Mata Pelajaran > Edit',
                                                
                                                // Profile routes
                                                'profile.edit' => 'Profil',
                                            ];

                                            $parentRouteMap = [
                                                'Ujian' => 'lecturer.exams.index',
                                                'Bank Soal' => 'lecturer.question-bank.index',
                                                'Mata Kuliah' => 'lecturer.subjects.index',
                                                'Pengguna' => 'admin.users.index',
                                            ];
                                            
                                            $breadcrumbName = $routeMap[$routeName] ?? ucfirst(str_replace('.', ' > ', str_replace($userRole . '.', '', $routeName)));
                                        @endphp

                                        @if(str_contains($breadcrumbName, '>'))
                                            @php
                                                $parts = explode(' > ', $breadcrumbName);
                                                $firstPart = $parts[0];
                                                $lastPart = end($parts);
                                                $parentRoute = $parentRouteMap[$firstPart] ?? '#';
                                            @endphp
                                            <a href="{{ $parentRoute !== '#' ? route($parentRoute) : '#' }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-gray-700 md:ml-2">{{ $firstPart }}</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">{{ $lastPart }}</span>
                                        @else
                                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">{{ $breadcrumbName }}</span>
                                        @endif
                                    </div>
                                </li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Right side: Notifications and User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Notifications Button -->
                <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <!-- Notification Badge -->
                    <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- User Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <!-- User Avatar -->
                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white text-sm font-medium">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        
                        <!-- User Info (hidden on mobile) -->
                        <div class="hidden md:block text-left">
                            <div class="text-sm font-medium text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</div>
                        </div>

                        <!-- Dropdown Arrow -->
                        <svg class="hidden md:block h-4 w-4 text-gray-400 transition-transform duration-200"
                             :class="{ 'rotate-180': open }"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                        
                        <!-- Profile Link -->
                        <a href="{{ route('profile.edit') }}" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profil
                            </div>
                        </a>

                        <!-- Settings Link -->
                        <a href="#" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Pengaturan
                            </div>
                        </a>

                        <!-- Divider -->
                        <div class="border-t border-gray-100"></div>

                        <!-- Logout Form -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
