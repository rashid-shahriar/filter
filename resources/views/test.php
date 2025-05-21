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
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1 space-y-4 sm:space-y-0 sm:flex sm:space-x-4">
                        <select id="categoriesFilter" x-data="{ categorySelected: '' }" x-model="categorySelected"
                            class="w-full sm:w-48 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                            <option value="All">All Categories</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>

                        <select id="departmentFilter"
                            class=" px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
                            <option value="All">All Departments</option>
                            @foreach ($departments as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>

                        <input type="date" id="dateFilter"
                            class="w-full sm:w-48 px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors text-gray-400">
                    </div>
                    <input type="text" id="searchInput" placeholder="Search notices..."
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
                search: document.getElementById('searchInput'),
            };



            Object.values(filters).forEach(input => {
                input.addEventListener('input', debounce(fetchNotices, 300));
            });

            function debounce(func, timeout = 300) {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => func.apply(this, args), timeout);
                };
            }

            function fetchNotices() {
                fetch("{{ route('notices.filter') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            categories: filters.categories.value,
                            department: filters.department.value,
                            date: filters.date.value,
                            search: filters.search.value.trim(),
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateTable(data.notices);
                    })
                    .catch(error => console.error('Error:', error));
            }

            function updateTable(notices) {
                const tbody = document.getElementById('noticesTable');
                tbody.innerHTML = '';

                if (!notices.length) {
                    tbody.innerHTML =
                        `<tr><td colspan="5" class="px-6 py-4 text-center">No notices found</td></tr>`;
                    return;
                }

                const fragment = document.createDocumentFragment();
                notices.forEach((notice, index) => {
                    const tr = document.createElement('tr');
                    tr.classList.add('hover:bg-gray-50');
                    tr.innerHTML = `
                        <td class="px-6 py-4 text-sm">${index + 1}</td>
                        <td class="px-6 py-4 text-sm font-medium">${notice.title}</td>
                        <td class="px-6 py-4 text-sm"><span class="px-2.5 py-1 rounded-full bg-blue-100">${notice.categories}</span></td>
                        <td class="px-6 py-4 text-sm">${notice.department || ''}</td>
                        <td class="px-6 py-4 text-sm">${new Date(notice.published_at).toLocaleDateString()}</td>
                    `;
                    fragment.appendChild(tr);
                });

                tbody.appendChild(fragment);
            }

            fetchNotices();
        });
    </script>

</body>

</html>