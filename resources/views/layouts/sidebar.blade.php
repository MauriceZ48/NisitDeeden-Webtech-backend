<div class="bg-gray-100">
        <!-- Sidebar -->
        <div class="absolute bg-gray-800 text-white w-56 min-h-screen overflow-y-auto transition-transform transform -translate-x-full ease-in-out duration-300"
             id="sidebar">
            <!-- Your Sidebar Content -->
            <div class="p-4">
                <h1 class="text-2xl font-semibold">Sidebar</h1>
                <ul class="mt-4">
                    <li class="mb-2"><a href=" {{ url('/') }}" class="block hover:text-indigo-400">Home</a></li>
                    <li class="mb-2"><a href="{{ url('/applications/dashboard') }}" class="block hover:text-indigo-400">Dashboard</a></li>
                    <li class="mb-2"><a href="#" class="block hover:text-indigo-400">Services</a></li>
                    <li class="mb-2"><a href=" {{ url('/contact') }}" class="block hover:text-indigo-400">Contact</a></li>
                </ul>
            </div>
        </div>
</div>
