<div class="bg-white shadow">
    <div class="container mx-auto">
        <div class="flex justify-between items-center py-4 px-2">
            <h1 class="text-xl font-semibold">NisitDeeden</h1>

            <div class="flex">
                @guest
                <a href="{{ url('login') }}"
                   class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 mr-1 lg:px-5 py-2 lg:py-2.5 sm:mr-2 lg:mr-2 focus:outline-none">
                    Login
                </a>
                <a href="{{ url('register') }}"
                   class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 mr-1 lg:px-5 py-2 lg:py-2.5 sm:mr-2 lg:mr-2 focus:outline-none ">
                    Register
                </a>
                @endguest
                @auth()
                    <form class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-4 mr-1 lg:px-5 py-2 lg:py-2.5 sm:mr-2 lg:mr-2 focus:outline-none "
                          method="POST"
                          action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" >
                            Log Out
                        </button>
                    </form>

                <button class="text-gray-500 hover:text-gray-600 py-2" id="open-sidebar" >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                    @endauth
            </div>
        </div>
    </div>
</div>
