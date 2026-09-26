@extends('layouts.app')

@section('title', 'Landlord Dashboard – NEKJOUL IMANAGE')

@section('content')

    <style>
        /* ============ KPI CARDS ============ */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .kpi-card {
            border-radius: 16px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
            box-shadow: 0 4px 12px rgba(0,0,0,.06);
        }
        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0,0,0,.12);
        }
        .kpi-card.green  { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .kpi-card.teal   { background: linear-gradient(135deg, #14b8a6, #0d9488); }
        .kpi-card.blue   { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .kpi-card.amber  { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .kpi-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            opacity: .92;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .kpi-label .icon { font-size: 18px; opacity: .85; }
        .kpi-value { font-size: 32px; font-weight: 800; line-height: 1; margin-bottom: 16px; }
        .kpi-bar {
            height: 6px;
            background: rgba(255,255,255,.28);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .kpi-bar-fill {
            height: 100%;
            background: #fff;
            border-radius: 6px;
            transition: width .6s ease;
        }
        .kpi-sub { font-size: 12px; opacity: .88; }

        /* ============ LAYOUT ============ */
        .chart-row-1 {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .chart-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        .chart-panel {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .chart-panel h3 { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .chart-panel .panel-sub { font-size: 12px; color: #6b7280; margin-bottom: 16px; }

        .chart-canvas-wrap { position: relative; height: 300px; }
        .chart-canvas-wrap.small { height: 320px; }

        /* ============ PERIOD TOGGLE ============ */
        .period-toggle {
            display: inline-flex;
            background: #f3f4f6;
            border-radius: 10px;
            padding: 4px;
            gap: 4px;
        }
        .period-toggle a {
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-decoration: none;
            border-radius: 6px;
            transition: background .15s, color .15s;
        }
        .period-toggle a:hover { color: #16a34a; }
        .period-toggle a.active {
            background: #fff;
            color: #16a34a;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }

        /* ============ DONUTS ============ */
        .donut-stack { display: flex; flex-direction: column; gap: 22px; }
        .donut-item { display: flex; align-items: center; gap: 18px; }
        .donut-ring {
            width: 90px;
            height: 90px;
            position: relative;
            flex-shrink: 0;
        }
        .donut-ring canvas { width: 90px !important; height: 90px !important; }
        .donut-info { flex: 1; }
        .donut-info .title { font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px; }
        .donut-info .sub { font-size: 12px; color: #6b7280; }

        /* ============ ACTIVITY ============ */
        .activity-list { display: flex; flex-direction: column; }
        .activity-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .activity-icon.payment { background: #dcfce7; }
        .activity-icon.request { background: #fef3c7; }
        .activity-icon.lease   { background: #dbeafe; }
        .activity-icon.system  { background: #e0e7ff; }

        .activity-body { flex: 1; min-width: 0; }
        .activity-title { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 2px; }
        .activity-sub {
            font-size: 12px;
            color: #6b7280;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .activity-time { font-size: 11px; color: #9ca3af; flex-shrink: 0; padding-top: 2px; }

        /* ============ HEADER ============ */
        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }
        .dash-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .dash-header p  { color: #6b7280; font-size: 14px; }

        @media (max-width: 1100px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .chart-row-1 { grid-template-columns: 1fr; }
            .chart-row-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .kpi-grid { grid-template-columns: 1fr; }
            .donut-item { flex-direction: column; text-align: center; }
        }
    </style>

    {{-- ============ HEADER ============ --}}
    <div class="dash-header">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome back, {{ auth()->user()->name }} 🔑</p>
        </div>

        <div class="period-toggle">
            <a href="{{ route('landlord.dashboard', ['period' => 'day']) }}"
               class="{{ $period === 'day' ? 'active' : '' }}">Today</a>
            <a href="{{ route('landlord.dashboard', ['period' => 'month']) }}"
               class="{{ $period === 'month' ? 'active' : '' }}">Month</a>
            <a href="{{ route('landlord.dashboard', ['period' => 'year']) }}"
               class="{{ $period === 'year' ? 'active' : '' }}">Year</a>
        </div>
    </div>

    {{-- ============ KPI CARDS ============ --}}
    <div class="kpi-grid">

        <div class="kpi-card green">
            <div class="kpi-label">
                <span>Properties</span>
                <span class="icon">🏠</span>
            </div>
            <div class="kpi-value">{{ $totalProps }}</div>
            <div class="kpi-bar">
                <div class="kpi-bar-fill" style="width: {{ $occupancyRate }}%"></div>
            </div>
            <div class="kpi-sub">{{ $occupied }} of {{ $totalProps }} occupied</div>
        </div>

        <div class="kpi-card teal">
            <div class="kpi-label">
                <span>Monthly Revenue</span>
                <span class="icon">💰</span>
            </div>
            <div class="kpi-value">${{ number_format($thisMonthCollected, 0) }}</div>
            <div class="kpi-bar">
                <div class="kpi-bar-fill" style="width: {{ $collectionRate }}%"></div>
            </div>
            <div class="kpi-sub">Collected this month</div>
        </div>

        <div class="kpi-card blue">
            <div class="kpi-label">
                <span>Active Tenants</span>
                <span class="icon">👥</span>
            </div>
            <div class="kpi-value">{{ $activeTenantCount }}</div>
            <div class="kpi-bar">
                <div class="kpi-bar-fill" style="width: {{ $totalProps > 0 ? round(($activeTenantCount / $totalProps) * 100) : 0 }}%"></div>
            </div>
            <div class="kpi-sub">{{ $activeTenantCount }} of {{ $totalProps }} units</div>
        </div>

        <div class="kpi-card amber">
            <div class="kpi-label">
                <span>Open Requests</span>
                <span class="icon">🛠</span>
            </div>
            <div class="kpi-value">{{ $pendingRequests }}</div>
            <div class="kpi-bar">
                <div class="kpi-bar-fill" style="width: {{ $totalRequests > 0 ? round(($pendingRequests / $totalRequests) * 100) : 0 }}%"></div>
            </div>
            <div class="kpi-sub">{{ $pendingRequests }} pending</div>
        </div>

    </div>

    {{-- ============ ROW 1 ============ --}}
    <div class="chart-row-1">

        <div class="chart-panel">
            <h3>Income vs Requests</h3>
            <div class="panel-sub">Last {{ count($labels) }} day(s)</div>
            <div class="chart-canvas-wrap">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <div class="chart-panel">
            <h3>Breakdown</h3>
            <div class="panel-sub">Key performance metrics</div>

            <div class="donut-stack">
                <div class="donut-item">
                    <div class="donut-ring"><canvas id="donut1"></canvas></div>
                    <div class="donut-info">
                        <div class="title">Occupancy</div>
                        <div class="sub">{{ $occupied }} of {{ $totalProps }} units</div>
                    </div>
                </div>

                <div class="donut-item">
                    <div class="donut-ring"><canvas id="donut2"></canvas></div>
                    <div class="donut-info">
                        <div class="title">Collected</div>
                        <div class="sub">${{ number_format($thisMonthCollected, 0) }} of ${{ number_format($expectedMonthly, 0) }}</div>
                    </div>
                </div>

                <div class="donut-item">
                    <div class="donut-ring"><canvas id="donut3"></canvas></div>
                    <div class="donut-info">
                        <div class="title">Resolved</div>
                        <div class="sub">{{ $resolvedRequests }} of {{ $totalRequests }} requests</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ============ ROW 2 ============ --}}
    <div class="chart-row-2">

        <div class="chart-panel">
            <h3>Monthly Revenue</h3>
            <div class="panel-sub">Last 12 months</div>
            <div class="chart-canvas-wrap small">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <div class="chart-panel">
            <h3>Recent Activity</h3>
            <div class="panel-sub">Latest updates in your portfolio</div>

            @if ($recentActivity->isEmpty())
                <div style="text-align:center; color:#9ca3af; padding:40px 0; font-size:14px;">
                    No activity yet.
                </div>
            @else
                <div class="activity-list">
                    @foreach ($recentActivity as $n)
                        <div class="activity-item">
                            <div class="activity-icon {{ $n->icon }}">
                                @if ($n->icon === 'payment') 💳
                                @elseif ($n->icon === 'request') 🛠
                                @elseif ($n->icon === 'lease') 📄
                                @else 🔔
                                @endif
                            </div>
                            <div class="activity-body">
                                <div class="activity-title">{{ $n->title }}</div>
                                @if ($n->body)
                                    <div class="activity-sub">{{ $n->body }}</div>
                                @endif
                            </div>
                            <div class="activity-time">{{ $n->created_at->diffForHumans(null, true) }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- ============ CHARTS ============ --}}
    <script>
        Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
        Chart.defaults.color = '#6b7280';

        // ---------- LINE ----------
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [
                    {
                        label: 'Income',
                        data: @json($incomeSeries),
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34,197,94,.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Requests',
                        data: @json($requestsSeries),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, padding: 14, font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 12, weight: '700' },
                        bodyFont: { size: 12 },
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 0, autoSkipPadding: 20 } },
                    y: { position: 'left', grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, callback: v => '$' + v } },
                    y1: { position: 'right', grid: { display: false }, ticks: { font: { size: 11 }, precision: 0 } }
                }
            }
        });

        // ---------- DONUTS ----------
        function makeDonut(id, percent, color) {
            const ctx = document.getElementById(id).getContext('2d');
            const remainder = 100 - percent;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [percent, remainder],
                        backgroundColor: [color, '#f3f4f6'],
                        borderWidth: 0,
                        cutout: '75%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    animation: { animateRotate: true, animateScale: false, duration: 800 },
                },
                plugins: [{
                    id: 'centerText',
                    afterDraw: (chart) => {
                        const { ctx, chartArea } = chart;
                        if (!chartArea) return;
                        const cx = (chartArea.left + chartArea.right) / 2;
                        const cy = (chartArea.top + chartArea.bottom) / 2;

                        ctx.save();
                        ctx.fillStyle = color;
                        ctx.font = '700 18px "Segoe UI", sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(percent + '%', cx, cy);
                        ctx.restore();
                    }
                }]
            });
        }

        makeDonut('donut1', {{ $occupancyRate }},  '#22c55e');
        makeDonut('donut2', {{ $collectionRate }}, '#3b82f6');
        makeDonut('donut3', {{ $resolvedRate }},   '#f59e0b');

        // ---------- BAR ----------
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                    label: 'Revenue',
                    data: @json($monthlyRevenue),
                    backgroundColor: '#22c55e',
                    hoverBackgroundColor: '#16a34a',
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 28,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: { label: (ctx) => '$' + Number(ctx.parsed.y).toLocaleString() }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, callback: v => '$' + v } }
                }
            }
        });
    </script>

@endsection