<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Notices</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.noticeFilterRoute = "{{ route('notices.filter') }}";
        window.csrfToken = "{{ csrf_token() }}";
    </script>
</head>

<body class="bg-gray-50">
    <div id="app" class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1 space-y-4 sm:space-y-0 sm:flex sm:space-x-4">
                        <select data-filter="categories"
                            class="w-full sm:w-48 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                            <option value="All">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>

                        <select data-filter="department"
                            class="px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                            <option value="All">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>

                        <input type="date" data-filter="date"
                            class="w-full sm:w-48 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors text-gray-400">
                    </div>
                    <input type="text" data-filter="search" placeholder="Search notices..."
                        class="w-full sm:w-64 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-lg font-semibold">Notices</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Published At</th>
                                </tr>
                            </thead>
                            <tbody id="noticesTable" class="divide-y divide-gray-100"></tbody>
                        </table>
                        <div id="pagination" class="flex justify-center p-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
