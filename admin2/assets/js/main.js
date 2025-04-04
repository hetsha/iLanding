document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    document.getElementById('sidebarCollapse').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Project form validation
    const projectForm = document.getElementById('projectForm');
    if (projectForm) {
        projectForm.addEventListener('submit', function(event) {
            if (!projectForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            projectForm.classList.add('was-validated');
        });
    }

    // Dynamic profit calculation
    const incomeInput = document.getElementById('income');
    const expensesInput = document.getElementById('expenses');
    const profitDisplay = document.getElementById('profit');

    if (incomeInput && expensesInput && profitDisplay) {
        const calculateProfit = () => {
            const income = parseFloat(incomeInput.value) || 0;
            const expenses = parseFloat(expensesInput.value) || 0;
            const profit = income - expenses;
            profitDisplay.textContent = profit.toFixed(2);
            profitDisplay.classList.remove('text-success', 'text-danger');
            profitDisplay.classList.add(profit >= 0 ? 'text-success' : 'text-danger');
        };

        incomeInput.addEventListener('input', calculateProfit);
        expensesInput.addEventListener('input', calculateProfit);
    }

    // DataTables initialization
    const tables = document.querySelectorAll('.datatable');
    tables.forEach(table => {
        new DataTable(table, {
            responsive: true,
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
        });
    });
});

// Chart initialization function
function initializeCharts() {
    // Project Statistics Chart
    const projectStats = document.getElementById('projectStatistics');
    if (projectStats) {
        new Chart(projectStats, {
            type: 'bar',
            data: {
                labels: projectStats.dataset.labels.split(','),
                datasets: [{
                    label: 'Income',
                    data: projectStats.dataset.income.split(','),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }, {
                    label: 'Expenses',
                    data: projectStats.dataset.expenses.split(','),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Export to PDF function
function exportToPDF(elementId, filename) {
    const element = document.getElementById(elementId);
    html2pdf()
        .from(element)
        .save(filename);
}

// Export to Excel function
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
    XLSX.writeFile(wb, filename);
}