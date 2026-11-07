@extends('admin.layouts.app')

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
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary btn-icon"><i class="bi bi-people"></i>Users</a>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary btn-icon"><i class="bi bi-sliders"></i>Settings</a>
    </div>
</div>

<div class="row g-3 mb-3">
    @foreach($overviewCards as $card)
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card dashboard-kpi {{ $card['accent'] }}">
                <div class="card-body d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="kpi-label">{{ $card['label'] }}</div>
                        <div class="kpi-value">{{ number_format($card['value']) }}</div>
                        {{-- <div class="kpi-note">{{ $card['delta'] }} {{ $card['note'] }}</div> --}}
                    </div>
                    <span class="kpi-icon"><i class="bi {{ $card['icon'] }} fs-4"></i></span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-xl-12">
        <div class="card content-card dashboard-panel">
            <div class="card-header d-flex justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="card-title mb-0">Family Growth</h2>
                    <small class="text-success fw-semibold">
                        <i class="bi bi-arrow-up-right"></i>
                        12.4% this month
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge text-bg-light border text-secondary">Monthly</span>
                    <span class="badge text-bg-light border text-secondary">Live data</span>
                </div>
            </div>
            <div class="card-body">
                <div class="dashboard-chart">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="col-xl-4">
        <div class="card content-card dashboard-panel">
            <div class="card-header">
                <h2 class="card-title mb-0">User Status</h2>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="dashboard-chart dashboard-chart--sm">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="dashboard-summary mt-3">
                    @foreach($miniStats as $item)
                        <div class="summary-item">
                            <div class="label">{{ $item['label'] }}</div>
                            <div class="value">{{ $item['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div> --}}
</div>

<div class="row g-3 mt-1">
    <div class="col-xl-12">
        <div class="card content-card dashboard-panel">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-title mb-0">Last 7 Days Family Growth</h2>
                <span class="badge text-bg-light border text-secondary">Processed volume</span>
            </div>
            <div class="card-body">
                <div class="dashboard-chart dashboard-chart--sm">
                    <canvas id="emailChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-xl-4">
        <div class="card content-card dashboard-panel">
            <div class="card-header">
                <h2 class="card-title mb-0">Operations Timeline</h2>
            </div>
            <div class="card-body">
                <div class="dashboard-timeline">
                    @foreach($timeline as $item)
                        <div class="timeline-item">
                            <div class="time">{{ $item['time'] }}</div>
                            <div>
                                <div class="title">{{ $item['title'] }}</div>
                                <div class="text">{{ $item['text'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div> --}}
</div>

@endsection

@push('scripts')
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
