<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Notices</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="bg-gray-50">
    <div id="app" class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col space-y-8">
                <!-- Filters Section -->
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="space-y-4 sm:space-y-0 sm:flex sm:space-x-4 flex-1">
                        <select id="categoriesFilter" x-data="{ categorySelected: '' }" x-model="categorySelected"
                            class="w-full sm:w-48 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                            <option value="All">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>

                        <div x-show="categorySelected === 'Department'" class="w-full sm:w-48">
                            <select id="departmentFilter"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                                <option value="All">All Departments</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>

                        <input type="date" id="dateFilter"
                            class="w-full sm:w-48 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors text-gray-400">
                    </div>

                    <!-- Add this to your filters section -->
                    <div class="flex-1">
                        <input type="text" id="searchInput" placeholder="Search notices..."
                            class="w-full sm:w-64 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                    </div>
                </div>

                <!-- Content Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-800">Notices</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Department</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Published At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="noticesTable">
                                @foreach ($notices as $notice)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $notice->title }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-xs">{{ $notice->categories }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $notice->department ?? '' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{-- {{ $notice->published_at->format('Y-m-d H:i') }}</td> --}}
                                            {{ Carbon\Carbon::parse($notice->published_at)->format('Y-m-d') }}

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- After the table -->
                        <div class="px-6 py-4 " id="paginationContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filters = {
                categories: document.getElementById('categoriesFilter'),
                department: document.getElementById('departmentFilter'),
                date: document.getElementById('dateFilter'),
                search: document.getElementById('searchInput')
            };
            let currentPage = 1;

            document.querySelectorAll('.filter-input').forEach(input => {
                input.addEventListener('change', () => {
                    currentPage = 1;
                    fetchNotices();
                });
            });
            filters.search.addEventListener('input', debounce(fetchNotices, 300));

            function debounce(func, timeout = 300) {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => func.apply(this, args), timeout);
                };
            }

            function fetchNotices(page = 1) {
                currentPage = page;
                fetch("{{ route('notices.filter') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            categories: filters.categories.value,
                            department: filters.department.value,
                            date: filters.date.value,
                            search: filters.search.value.trim(),
                            page: currentPage
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateTable(data.notices);
                        updatePagination(data.pagination);
                    })
                    .catch(error => console.error('Error:', error));
            }

            function updateTable(notices) {
                const tbody = document.getElementById('noticesTable');
                tbody.innerHTML = notices.length ? '' :
                    `<tr><td colspan="5" class="px-6 py-4 text-center">No notices found</td></tr>`;

                notices.forEach((notice, index) => {
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">${index + 1}</td>
                            <td class="px-6 py-4 text-sm font-medium">${notice.title}</td>
                            <td class="px-6 py-4 text-sm"><span class="px-2.5 py-1 rounded-full bg-blue-100">${notice.categories}</span></td>
                            <td class="px-6 py-4 text-sm">${notice.department || ''}</td>
                            <td class="px-6 py-4 text-sm">${new Date(notice.published_at).toLocaleDateString()}</td>
                        </tr>`;
                });
            }

            function updatePagination(paginationHtml) {
                const container = document.getElementById('paginationContainer');
                container.innerHTML = paginationHtml;
                container.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = new URL(this.href).searchParams.get('page');
                        fetchNotices(page);
                    });
                });
            }

            fetchNotices(currentPage);
        });
    </script>

</body>

</html>
