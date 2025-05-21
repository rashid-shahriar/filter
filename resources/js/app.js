import "./bootstrap";

document.addEventListener("DOMContentLoaded", function () {
    const filters = {
        categories: document.querySelector('[data-filter="categories"]'),
        department: document.querySelector('[data-filter="department"]'),
        date: document.querySelector('[data-filter="date"]'),
        search: document.querySelector('[data-filter="search"]'),
    };

    let currentPage = 1;
    let perPage = 4; // Keep in sync with backend pagination

    // Debounce only for search and date
    filters.search.addEventListener(
        "input",
        debounce(() => fetchNotices(1), 300)
    );
    filters.date.addEventListener(
        "input",
        debounce(() => fetchNotices(1), 300)
    );
    filters.categories.addEventListener("change", () => fetchNotices(1));
    filters.department.addEventListener("change", () => fetchNotices(1));

    function debounce(func, timeout = 300) {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), timeout);
        };
    }

    function fetchNotices(page = 1) {
        currentPage = page;
        fetch(window.noticeFilterRoute, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": window.csrfToken,
            },
            body: JSON.stringify({
                categories: filters.categories.value,
                department: filters.department.value,
                date: filters.date.value,
                search: filters.search.value.trim(),
                page: page,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                perPage = data.per_page || 4;
                updateTable(data.notices, data.current_page, perPage);
                updatePagination(data.current_page, data.last_page);
            })
            .catch((error) => console.error("Error:", error));
    }

    function updateTable(notices, currentPage, perPage) {
        const tbody = document.getElementById("noticesTable");
        tbody.innerHTML = "";

        if (!notices.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-4 text-center">No notices found</td></tr>`;
            return;
        }

        const fragment = document.createDocumentFragment();
        notices.forEach((notice, index) => {
            const tr = document.createElement("tr");
            tr.classList.add("hover:bg-gray-50");
            tr.innerHTML = `
                <td class="px-6 py-4 text-sm">${
                    index + 1 + (currentPage - 1) * perPage
                }</td>
                <td class="px-6 py-4 text-sm font-medium">${notice.title}</td>
                <td class="px-6 py-4 text-sm"><span class="px-2.5 py-1 rounded-full bg-blue-100">${
                    notice.categories
                }</span></td>
                <td class="px-6 py-4 text-sm">${notice.department || ""}</td>
                <td class="px-6 py-4 text-sm">${new Date(
                    notice.published_at
                ).toLocaleDateString()}</td>
            `;
            fragment.appendChild(tr);
        });
        tbody.appendChild(fragment);
    }

    function updatePagination(current, last) {
        const pagination = document.getElementById("pagination");
        pagination.innerHTML = "";
        if (last <= 1) return;
        const prevBtn = document.createElement("button");
        prevBtn.textContent = "Prev";
        prevBtn.className =
            "px-3 py-1 hover:bg-gray-300 cursor-pointer bg-gray-200";
        prevBtn.disabled = current === 1;
        prevBtn.addEventListener("click", () => fetchNotices(current - 1));
        pagination.appendChild(prevBtn);
        addPageBtn(1, current);
        let start = Math.max(2, current - 1);
        let end = Math.min(last - 1, current + 2);
        if (start > 2) addEllipsis();
        for (let i = start; i <= end; i++) addPageBtn(i, current);
        if (end < last - 1) addEllipsis();
        if (last > 1) addPageBtn(last, current);
        const nextBtn = document.createElement("button");
        nextBtn.textContent = "Next";
        nextBtn.className =
            "px-3 py-1 hover:bg-gray-300 cursor-pointer bg-gray-200";
        nextBtn.disabled = current === last;
        nextBtn.addEventListener("click", () => fetchNotices(current + 1));
        pagination.appendChild(nextBtn);
        function addPageBtn(page, current) {
            const btn = document.createElement("button");
            btn.textContent = page;
            btn.className =
                "px-3 py-1 cursor-pointer " +
                (page === current
                    ? "bg-blue-500 text-white"
                    : "bg-gray-200 hover:bg-gray-300");
            btn.disabled = page === current;
            btn.addEventListener("click", () => fetchNotices(page));
            pagination.appendChild(btn);
        }
        function addEllipsis() {
            const span = document.createElement("span");
            span.textContent = "...";
            span.className = "px-3 py-1 text-gray-400";
            pagination.appendChild(span);
        }
    }

    fetchNotices();
});
