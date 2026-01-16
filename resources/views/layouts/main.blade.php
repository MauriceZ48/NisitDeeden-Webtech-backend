<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
{{--@include('layouts.sidebar')--}}

<main>

    <div class="bg-gray-100">

        <div class="h-screen flex overflow-hidden bg-gray-200">
            <!-- Sidebar -->
            <div class="absolute bg-gray-800 text-white w-56 min-h-screen overflow-y-auto transition-transform transform -translate-x-full ease-in-out duration-300"
                 id="sidebar">
                <!-- Your Sidebar Content -->
                <div class="p-4">
                    <h1 class="text-2xl font-semibold">Sidebar</h1>
                    <ul class="mt-4">
                        <li class="mb-2"><a href=" {{ url('/') }}" class="block hover:text-indigo-400">Home</a></li>
                        <li class="mb-2"><a href="#" class="block hover:text-indigo-400">About</a></li>
                        <li class="mb-2"><a href="#" class="block hover:text-indigo-400">Services</a></li>
                        <li class="mb-2"><a href="{{ url('/contact') }}" class="block hover:text-indigo-400">Contact</a></li>
                    </ul>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Navbar -->
                <div class="bg-white shadow">
                    <div class="container mx-auto">
                        <div class="flex justify-between items-center py-4 px-2">
                            <h1 class="text-xl font-semibold">NisitDeeden</h1>

                            <div class="flex">
                                <a href="{{ url('login') }}"
                                   class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 mr-1 lg:px-5 py-2 lg:py-2.5 sm:mr-2 lg:mr-2 focus:outline-none">
                                    Login
                                </a>
                                <a href="{{ url('register') }}"
                                   class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 mr-1 lg:px-5 py-2 lg:py-2.5 sm:mr-2 lg:mr-2 focus:outline-none ">
                                    Register
                                </a>
                            <button class="text-gray-500 hover:text-gray-600 py-2" id="open-sidebar" >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Content Body -->
                <div class="flex-1 overflow-auto p-4">
                    @yield('content')
                </div>
            </div>
        </div>

        <script>
            const sidebar = document.getElementById('sidebar');
            const openSidebarButton = document.getElementById('open-sidebar');

            openSidebarButton.addEventListener('click', (e) => {
                e.stopPropagation();
                sidebar.classList.toggle('-translate-x-full');
            });

            // Close the sidebar when clicking outside of it
            document.addEventListener('click', (e) => {
                if (!sidebar.contains(e.target) && !openSidebarButton.contains(e.target)) {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        </script>

    </div>

</main>
</body>
</html>
