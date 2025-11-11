<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4">

        <!-- Bar Chart: Most Watched -->
        <div class="col-xl-6 col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"> {{ __('admin.mostWatchedContent') }}</h5>
                    <i class="ti ti-bar-chart text-muted"></i>
                </div>
                <div class="card-body">
                    <canvas id="barChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Horizontal Bar Chart: Most Downloaded -->
        <div class="col-xl-6 col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('admin.mostDownloaded') }}</h5>
                    <i class="ti ti-download text-muted"></i>
                </div>
                <div class="card-body">
                    <canvas id="horizontalBarChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Line Chart: Daily Interactions -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"> {{ __('admin.dailyInteractions') }}</h5>
                    <i class="ti ti-activity text-muted"></i>
                </div>
                <div class="card-body">
                    <canvas id="lineChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Horizontal Bar Chart: Most Added -->
<div class="col-xl-6 col-12 mb-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('admin.mostAddedWatchlists') }}</h5>
            <i class="ti ti-list-check text-muted"></i>
        </div>
        <div class="card-body">
            <canvas id="mostAddedChart" class="chartjs" data-height="350"></canvas>
        </div>
    </div>
</div>

<!-- Radar Chart: Most Saved by Users -->
<div class="col-xl-6 col-12 mb-6">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('admin.mostSavedDataUsers') }}</h5>
            <i class="ti ti-users text-muted"></i>
        </div>
        <div class="card-body">
            <canvas id="mostSavedChart" class="chartjs" data-height="350"></canvas>
        </div>
    </div>
</div>

        <!-- Radar Chart: Top Rated -->
        <div class="col-lg-6 col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('admin.topRated') }}</h5>
                    <i class="ti ti-star text-muted"></i>
                </div>
                <div class="card-body">
                    <canvas id="radarChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Polar Area Chart: Active Users -->
        <div class="col-lg-6 col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('admin.mostActiveUsers') }}</h5>
                    <i class="ti ti-users text-muted"></i>
                </div>
                <div class="card-body">
                    <canvas id="polarChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Doughnut Chart: Overall Summary -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('admin.overallSummary') }}</h5>
                    <i class="ti ti-chart-donut text-muted"></i>
                </div>
                <div class="card-body">
                    <canvas id="doughnutChart" height="100"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>


@push('scripts')
<script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dailyData = @json($dailyInteractions);
    const topWatched = @json($topWatched);
    const topDownloaded = @json($topDownloaded);
    const topRated = @json($topRated);
    const activeUsers = @json($activeUsers);

    // Line Chart - Daily Interactions
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: dailyData.map(i => i.date),
            datasets: [{
                label: 'Daily Interactions',
                data: dailyData.map(i => i.total),
                borderColor: '#7367f0',
                backgroundColor: 'rgba(115, 103, 240, 0.15)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Bar Chart - Most Watched
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: topWatched.map(i => i.content_type + ' #' + i.content_id),
            datasets: [{
                label: 'Most Watched',
                data: topWatched.map(i => i.total),
                backgroundColor: '#28c76f'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Horizontal Bar Chart - Most Downloaded
    new Chart(document.getElementById('horizontalBarChart'), {
        type: 'bar',
        data: {
            labels: topDownloaded.map(i => i.content_type + ' #' + i.content_id),
            datasets: [{
                label: 'Most Downloaded',
                data: topDownloaded.map(i => i.total),
                backgroundColor: '#ff9f43'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });

    // Radar Chart - Top Rated
    new Chart(document.getElementById('radarChart'), {
        type: 'radar',
        data: {
            labels: topRated.map(i => i.content_type + ' #' + i.content_id),
            datasets: [{
                label: 'Average Rating',
                data: topRated.map(i => i.avg_rating),
                borderColor: '#00cfe8',
                backgroundColor: 'rgba(0, 207, 232, 0.3)',
                pointBackgroundColor: '#00cfe8'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    // Polar Area Chart - Active Users
    new Chart(document.getElementById('polarChart'), {
        type: 'polarArea',
        data: {
            labels: activeUsers.map(i => i.first_name+' '+i.last_name),
            datasets: [{
                label: 'Activity Level',
                data: activeUsers.map(i => i.total_activity),
                backgroundColor: [
                    '#7367f0', '#28c76f', '#ff9f43', '#ea5455', '#00cfe8',
                    '#ffc107', '#1E88E5', '#9C27B0', '#26A69A', '#F4511E'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Doughnut Chart - Overall Summary
    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Watched', 'Downloaded', 'Rated'],
            datasets: [{
                data: [
                    topWatched.reduce((sum, i) => sum + i.total, 0),
                    topDownloaded.reduce((sum, i) => sum + i.total, 0),
                    topRated.reduce((sum, i) => sum + (i.total ?? 0), 0)
                ],
                backgroundColor: ['#7367f0', '#28c76f', '#ff9f43']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    const mostAddedData = @json($mostAdded);
const mostSavedDataUsers = @json($mostSavedByUsers);

// Horizontal Bar Chart - Most Added
new Chart(document.getElementById('mostAddedChart'), {
    type: 'bar',
    data: {
        labels: mostAddedData.map(i => `#${i.content_type+' / '+i.content_id}`),
        datasets: [{
            label: 'Total Additions',
            data: mostAddedData.map(i => i.total),
            backgroundColor: '#7367f0'
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        scales: { x: { beginAtZero: true } },
        plugins: { legend: { display: false } }
    }
});

// Radar Chart - Most Saved by Users
new Chart(document.getElementById('mostSavedChart'), {
    type: 'radar',
    data: {
        labels: mostSavedDataUsers.map(i => `#${i.content_type+' / '+i.content_id}`),
        datasets: [{
            label: 'Unique Users Saved',
            data: mostSavedDataUsers.map(i => i.unique_users),
            borderColor: '#28c76f',
            backgroundColor: 'rgba(40, 199, 112, 0.3)',
            pointBackgroundColor: '#28c76f'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } }
    }
});


});
</script>
@endpush
