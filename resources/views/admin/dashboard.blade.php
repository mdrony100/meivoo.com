@extends('admin.layouts.app')

@section('dashboard', 'active')

@section('content')
    @php
        $overviewCards = data_get($data, 'overviewCards', []);
        $miniStats = data_get($data, 'miniStats', []);
        $timeline = data_get($data, 'timeline', []);
        $quickActions = data_get($data, 'quickActions', []);
    @endphp

    <div class="dashboard-hero">
        <div>
            {{-- <h1>Dashboard</h1>
        <p>Snapshot of users activity, engagement, and admin operations.</p> --}}
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary btn-icon"><i
                    class="bi bi-people"></i>Users</a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary btn-icon"><i
                    class="bi bi-sliders"></i>Settings</a>
        </div>
    </div>


    <!-- KPI CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-3 text-primary"></i>
                    <h4 class="mt-2 mb-0">{{ $totalUser ?? 0 }}</h4>
                    <small class="text-muted">Total Users</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-diagram-3 fs-3 text-success"></i>
                    <h4 class="mt-2 mb-0">{{ $totalFamilyTrees ?? 0 }}</h4>
                    <small class="text-muted">Family Trees</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-chat-dots fs-3 text-warning"></i>
                    <h4 class="mt-2 mb-0">{{ $totalMessages ?? 0 }}</h4>
                    <small class="text-muted">Messages</small>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-journal-text fs-3 text-danger"></i>
                    <h4 class="mt-2 mb-0">{{ $totalPosts ?? 0 }}</h4>
                    <small class="text-muted">Posts</small>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0">New Family Trees Added</h5>
        </div>

        <div class="card-body" style="height: 500px;">
            <canvas id="familyTreeChart"></canvas>
        </div>

        <div class="card-footer bg-white border-0 text-center text-muted small">
            New family trees created in the last 7 months
        </div>
    </div>

    <!-- ACTIVITY SECTION -->
    {{-- <div class="row g-3">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <strong>New user registered</strong>
                        <div class="text-muted small">2 minutes ago</div>
                    </div>

                    <div class="mb-3">
                        <strong>Family tree created</strong>
                        <div class="text-muted small">10 minutes ago</div>
                    </div>

                    <div>
                        <strong>New message sent</strong>
                        <div class="text-muted small">30 minutes ago</div>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>

                <div class="card-body d-grid gap-2">

                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-people"></i> Manage Users
                    </a>

                    <button class="btn btn-outline-success">
                    <i class="bi bi-diagram-3"></i> Manage Family Trees
                </button>

                    <button class="btn btn-outline-warning">
                    <i class="bi bi-chat-dots"></i> View Messages
                </button>

                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-dark">
                        <i class="bi bi-gear"></i> System Settings
                    </a>

                </div>

            </div>
        </div>

    </div> --}}

@endsection

@push('scripts')
    <script>
        const canvas = document.getElementById('familyTreeChart');
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: @json(data_get($data, 'treeChartMonths', [])),
                datasets: [{
                    label: 'Added Trees',
                    data: @json(data_get($data, 'treeChartValues', [])),
                    backgroundColor: '#6f42c1',
                    borderRadius: 8,
                    maxBarThickness: 100,
                    barPercentage: 0.9,
                    categoryPercentage: 0.9
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: 5,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        },
                        grid: {
                            color: '#e2e8f0'
                        }
                    }
                }
            }
        });
    </script>
    <script>
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(226, 232, 240, .85)'
                    },
                    ticks: {
                        color: '#64748b',
                        precision: 0
                    }
                }
            }
        };

        const growthCanvas = document.getElementById('userGrowthChart');
        const growthCtx = growthCanvas.getContext('2d');
        const growthGradient = growthCtx.createLinearGradient(0, 0, 0, 240);
        growthGradient.addColorStop(0, 'rgba(37, 99, 235, .28)');
        growthGradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(growthCanvas, {
            type: 'line',
            data: {
                labels: @json(data_get($data, 'chartMonths', [])),
                datasets: [{
                    data: @json(data_get($data, 'chartUsers', [])),
                    borderColor: '#2563eb',
                    backgroundColor: growthGradient,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    tension: .38
                }]
            },
            options: commonOptions
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: @json(data_get($data, 'statusLabels', [])),
                datasets: [{
                    data: @json(data_get($data, 'statusValues', [])),
                    backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#0ea5e9'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 16,
                            color: '#475569'
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('emailChart'), {
            type: 'bar',
            data: {
                labels: @json(data_get($data, 'last7DaysLabels', [])),
                datasets: [{
                    data: @json(data_get($data, 'last7DaysEmails', [])),
                    backgroundColor: '#0ea5e9',
                    borderRadius: 8,
                    barThickness: 28
                }]
            },
            options: commonOptions
        });
    </script>
@endpush
