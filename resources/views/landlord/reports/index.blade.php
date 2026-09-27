@extends('layouts.app')

@section('title', 'Reports – NEKJOUL IMANAGE')

@section('content')

    <style>
        .rep-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }
        .rep-header h1 { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .rep-header p  { color: #6b7280; font-size: 14px; }

        .rep-actions { display: flex; gap: 10px; align-items: center; }
        .rep-actions select {
            padding: 9px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            background: #fff;
            cursor: pointer;
            outline: none;
        }
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            background: #22c55e;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-export:hover { background: #16a34a; }

        /* KPI grid */
        .rep-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .rep-kpi {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
            transition: transform .2s, box-shadow .2s;
        }
        .rep-kpi:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0,0,0,.08);
        }
        .rep-kpi .label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .rep-kpi .value {
            font-size: 26px;
            font-weight: 800;
            margin-top: 8px;
        }
        .rep-kpi .sub {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }
        .rep-kpi.green .value  { color: #16a34a; }
        .rep-kpi.teal .value   { color: #0d9488; }
        .rep-kpi.blue .value   { color: #2563eb; }
        .rep-kpi.amber .value  { color: #d97706; }

        /* Chart rows */
        .rep-row {
            display: grid;
            gap: 16px;
            margin-bottom: 16px;
        }
        .rep-row.two { grid-template-columns: 1fr 1fr; }
        .rep-row.wide { grid-template-columns: 2fr 1fr; }

        .rep-panel {
            background: #fff;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .rep-panel h3 {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .rep-panel .panel-sub {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 16px;
        }
        .rep-canvas {
            position: relative;
            height: 300px;
        }

        /* Top properties table */
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th {
            text-align: left;
            color: #6b7280;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        tbody td {
            padding: 14px 8px;
            border-bottom: 1px solid #f3f4f6;
            color: #111827;
        }
        tbody tr:last-child td { border-bottom: none; }

        .rank {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f0fdf4;
            color: #16a34a;
            font-weight: 700;
            font-size: 13px;
        }
        .rank.gold   { background: #fef3c7; color: #d97706; }
        .rank.silver { background: #e5e7eb; color: #6b7280; }
        .rank.bronze { background: #fed7aa; color: #c2410c; }

        .empty {
            text-align: center;
            color: #9ca3af;
            padding: 60px 20px;
            font-size: 14px;
        }

        @media (max-width: 1100px) {
            .rep-kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .rep-row.two, .rep-row.wide { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .rep-kpi-grid { grid-template-columns: 1fr; }
        }
    </style>

    {{-- Header --}}
    <div class="rep-header">
        <div>
            <h1>Reports</h1>
            <p>Portfolio performance for {{ $year }}.</p>
        </div>

        <div class="rep-actions">
            <form method="GET" action="{{ route('landlord.reports.index') }}">
                <select name="year" onchange="this.form.submit()">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('landlord.reports.export', ['year' => $year]) }}" class="btn-export">
                ⬇ Export CSV
            </a>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="rep-kpi-grid">
        <div class="rep-kpi green">
            <div class="label">Total Revenue <span>💰</span></div>
            <div class="value">${{ number_format($totalRevenue, 2) }}</div>
            <div class="sub">{{ $year }} · completed payments</div>
        </div>

        <div class="rep-kpi teal">
            <div class="label">Occupancy Rate <span>🏠</span></div>
            <div class="value">{{ $occupancyRate }}%</div>
            <div class="sub">Currently occupied</div>
        </div>

        <div class="rep-kpi blue">
            <div class="label">Average Rent <span>📊</span></div>
            <div class="value">${{ number_format($avgRent, 2) }}</div>
            <div class="sub">Per property</div>
        </div>

        <div class="rep-kpi amber">
            <div class="label">Collection Rate <span>✅</span></div>
            <div class="value">{{ $collectionRate }}%</div>
            <div class="sub">Of expected yearly rent</div>
        </div>
    </div>

    {{-- Row 1: Revenue bar + Occupancy line --}}
    <div class="rep-row two">
        <div class="rep-panel">
            <h3>Monthly Revenue</h3>
            <div class="panel-sub">{{ $year }}</div>
            <div class="rep-canvas">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="rep-panel">
            <h3>Occupancy Over Time</h3>
            <div class="panel-sub">{{ $year }}</div>
            <div class="rep-canvas">
                <canvas id="occupancyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Row 2: Top properties --}}
    <div class="rep-row wide">
        <div class="rep-panel">
            <h3>Top Properties</h3>
            <div class="panel-sub">Ranked by revenue in {{ $year }}</div>

            @if (empty($topProperties) || $topProperties[0]['revenue'] == 0)
                <div class="empty">No revenue recorded yet for {{ $year }}.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Property</th>
                            <th>Address</th>
                            <th>Tenants</th>
                            <th style="text-align:right;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topProperties as $i => $row)
                            @if ($row['revenue'] > 0)
                                <tr>
                                    <td>
                                        <span class="rank
                                            @if($i === 0) gold
                                            @elseif($i === 1) silver
                                            @elseif($i === 2) bronze
                                            @endif">
                                            {{ $i + 1 }}
                                        </span>
                                    </td>
                                    <td style="font-weight:600;">{{ $row['property']->name }}</td>
                                    <td style="font-size:13px; color:#6b7280;">{{ $row['property']->full_address }}</td>
                                    <td style="font-size:13px;">{{ $row['tenants'] }}</td>
                                    <td style="text-align:right; font-weight:700; color:#16a34a;">
                                        ${{ number_format($row['revenue'], 2) }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <script>
        Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
        Chart.defaults.color = '#6b7280';

        // ---------- Revenue bar chart ----------
        new Chart(document.getElementById('revenueChart').getContext('2d'), {
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
                    maxBarThickness: 32,
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
                        callbacks: {
                            label: (ctx) => '$' + Number(ctx.parsed.y).toLocaleString()
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, callback: v => '$' + v } }
                }
            }
        });

        // ---------- Occupancy line chart ----------
        new Chart(document.getElementById('occupancyChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                    label: 'Occupancy %',
                    data: @json($occupancyByMonth),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,.10)',
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
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
                        callbacks: {
                            label: (ctx) => ctx.parsed.y + '%'
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { size: 11 }, callback: v => v + '%' },
                        min: 0,
                        max: 100,
                    }
                }
            }
        });
    </script>

@endsection