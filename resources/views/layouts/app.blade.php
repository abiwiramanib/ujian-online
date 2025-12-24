<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
    <div x-data="{ 
        sidebarOpen: false,
        addModalOpen: false, 
        editModalOpen: false, 
        deleteModalOpen: false, 
        finishModalOpen: false, 
        publishModalOpen: false,
        editExam: {}, 
        deleteExam: {}, 
        finishExam: {},
        publishExam: {},
        openEditModal(exam) {
            this.editExam = exam;
            this.editModalOpen = true;
        },
        openDeleteModal(exam) {
            this.deleteExam = exam;
            this.deleteModalOpen = true;
        },
        openFinishModal(exam) {
            this.finishExam = exam;
            this.finishModalOpen = true;
        },
        openPublishModal(exam) {
            this.publishExam = exam;
            this.publishModalOpen = true;
        }
    }">
        <div x-init="$el.addEventListener('toggle-sidebar', () => sidebarOpen = !sidebarOpen)"
            class="min-h-screen bg-gray-100">
            <!-- Sidebar -->
            <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }" 
                class="w-64 transform transition-transform duration-300 ease-in-out md:translate-x-0 fixed h-screen z-30">
                @auth
                    @include('layouts.sidebar')
                @endauth
            </aside>

            <!-- Overlay for mobile -->
            <div x-show="sidebarOpen" 
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false" 
                class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
                x-cloak></div>

            <!-- Main content -->
            <div class="md:ml-64 flex-1 flex flex-col">
                            @include('layouts.navigation')
                
                            <!-- Page Heading -->
                            @if (isset($header))
                                <header class="bg-white shadow-sm">
                                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                        {{ $header }}
                                    </div>
                                </header>
                            @endif
                
                            <!-- Page Content -->
                            <main class="flex-1">
                                {{ $slot }}
                            </main>            </div>
        </div>

        <!-- Global Toast Notification -->
        <div x-data="{ toastShow: false, toastMessage: '', toastType: 'success' }" 
            x-init="
                let message = '';
                let type = 'success';

                // Priority 1: Check sessionStorage from AJAX calls
                if (sessionStorage.getItem('toastMessage')) {
                    message = sessionStorage.getItem('toastMessage');
                    type = sessionStorage.getItem('toastType') || 'success';
                    sessionStorage.removeItem('toastMessage');
                    sessionStorage.removeItem('toastType');
                } 
                // Priority 2: Check Laravel session flash
                else {
                    @if (session('success'))
                        message = '{{ session('success') }}';
                        type = 'success';
                    @elseif (session('status'))
                        message = '{{ session('status') }}';
                        type = 'success';
                    @elseif (session('error'))
                        message = '{{ session('error') }}';
                        type = 'error';
                    @endif
                }

                // If a message was found, show the toast
                if (message) {
                    toastMessage = message;
                    toastType = type;
                    setTimeout(() => toastShow = true, 200);
                    setTimeout(() => toastShow = false, 3200);
                }
            "
            class="fixed top-5 right-5 z-50 w-full max-w-xs">
            <div x-show="toastShow"
                x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                style="display: none;">
                <x-toast />
            </div>
        </div>

        {{-- Modals Slot --}}
        {{ $modals ?? '' }}
    </div>

    @stack('scripts')
</body>
</html>
