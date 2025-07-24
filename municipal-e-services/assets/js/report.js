    window.applicationsChart = null;
    async function loadStaffReport() {
        const formData = new FormData();
        formData.append('action', 'fetch_report_data');

        try {
            const res = await fetch('../controllers/generateReportController.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                const stats = data.stats;
                document.getElementById('totalApplications').innerText = stats.total_applications;
                document.getElementById('filedCount').innerText = stats.filed;
                document.getElementById('reviewCount').innerText = stats.under_review;
                document.getElementById('returnedCount').innerText = stats.returned;
                document.getElementById('approvedCount').innerText = stats.approved;
                document.getElementById('rejectedCount').innerText = stats.rejected;
                document.getElementById('pendingApprovalCount').innerText = stats.pending_approval; // New line for pending approval

                renderChart(stats);
            } else {
                console.error(data.message || 'Failed to load report data.');
            }
        } catch (error) {
            console.error('Fetch error:', error);
        }
    }

    function renderChart(stats) {
        const ctx = document.getElementById('applicationsChart').getContext('2d');

        // Destroy previous chart if it exists
        if (window.applicationsChart) {
            window.applicationsChart.destroy();
        }

        window.applicationsChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Filed', 'Under Review', 'Returned', 'Approved', 'Rejected', 'Pending Approval'],
                datasets: [{
                    labels: ['Filed', 'Under Review', 'Returned', 'Approved', 'Rejected', 'Pending Approval'],
                    data: [stats.filed, stats.under_review, stats.returned, stats.approved, stats.rejected, stats.pending_approval],
                    backgroundColor: ['#007bff', '#ffc107', '#6c757d', '#28a745', '#dc3545', '#17a2b8'],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#333',
                            font: {
                                size: 20
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                return `${label}: ${value} of total applications`;
                            }
                        }
                    }
                }
            }
        });
    }
    window.onload = loadStaffReport;

    function openReportModal() {
        document.getElementById('reportModal').classList.add('show');
        document.getElementById('reportModalBackdrop').classList.add('show');
        loadStaffReport(); // Call existing chart loader
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.remove('show');
        document.getElementById('reportModalBackdrop').classList.remove('show');
    }