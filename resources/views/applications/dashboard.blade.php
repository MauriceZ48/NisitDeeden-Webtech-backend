@extends('layouts.main')


@section('content')
    <h1 class="text-4xl text-center text-blue-600">
        Dashboard Hula
    </h1>

    <div class="bg-gray-50 p-8">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium tracking-wide">Total Applications</h3>
                        <div class="flex items-baseline gap-2 mt-2">
                            <span class="text-3xl font-bold text-gray-900">1,245</span>
                            <span class="text-green-600 text-sm font-semibold flex items-center">
              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"></path></svg>
              +12%
            </span>
                        </div>
                        <p class="text-gray-400 text-xs mt-1">From last academic year</p>
                    </div>
                    <div class="bg-blue-50 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium tracking-wide">Pending Review</h3>
                        <div class="flex items-baseline gap-2 mt-2">
                            <span class="text-3xl font-bold text-gray-900">42</span>
                            <span class="text-gray-500 text-sm font-medium">Applications</span>
                        </div>
                        <p class="text-gray-400 text-xs mt-1">Requires immediate attention</p>
                    </div>
                    <div class="bg-amber-50 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-gray-500 text-sm font-medium tracking-wide">Awarded</h3>
                        <div class="flex items-baseline gap-2 mt-2">
                            <span class="text-3xl font-bold text-gray-900">850</span>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">Students</span>
                        </div>
                        <p class="text-gray-400 text-xs mt-1">Total grant value: $1.2M</p>
                    </div>
                    <div class="bg-green-50 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-8">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Recent Applications</h2>
            <div class="flex gap-4">
                <input type="text" placeholder="Search..." class="border rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">Export</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 font-medium">Student Name</th>
                    <th class="px-6 py-4 font-medium">Category</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">Alice Johnson</div>
                        <div class="text-xs text-gray-500">ID: #882341</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">Dean's List</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Approved</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">Oct 24, 2023</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
