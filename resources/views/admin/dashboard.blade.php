@extends('admin.layouts.app')

@section('content')
<div class="ad-dash-filter">
  <form method="GET" action="{{ route('admin.dashboard') }}" id="dashRangeForm" class="ad-dash-filter-form">
    <label class="ad-label" for="dashRange">Date range</label>
    <div class="ad-dash-filter-row">
      <input
        class="ad-input"
        type="text"
        id="dashRange"
        name="range"
        value="{{ $dateFrom }} to {{ $dateTo }}"
        placeholder="Select date range"
        readonly
        autocomplete="off"
      >
      <button type="submit" class="ad-btn sm"><i class="bi bi-funnel"></i> Apply</button>
      <a href="{{ route('admin.dashboard') }}" class="ad-btn ghost sm">Reset</a>
    </div>
    <p class="ad-muted" style="font-size:.78rem;margin:.4rem 0 0">Showing stats for {{ $dateRangeLabel }}. Charts always show the last 6 months.</p>
  </form>
</div>

<div class="ad-kpis">
  <div class="ad-kpi">
    <span>Total Sales</span>
    <b>£{{ number_format($stats['total_sales'], 2) }}</b>
  </div>
  <div class="ad-kpi">
    <span>Total Orders</span>
    <b>{{ number_format($stats['total_orders']) }}</b>
  </div>
  <div class="ad-kpi">
    <span>Total Customers</span>
    <b>{{ number_format($stats['total_customers']) }}</b>
  </div>
  <div class="ad-kpi">
    <span>Total Branding Enquiries</span>
    <b>{{ number_format($stats['total_enquiries']) }}</b>
  </div>
</div>

<div class="ad-grid-2">
  <section class="ad-panel">
    <div class="ad-panel-head">
      <h2>Recent orders</h2>
      <a href="{{ route('admin.orders.index') }}" class="ad-link">View all</a>
    </div>
    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead>
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentOrders as $order)
            <tr>
              <td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td>
              <td>{{ $order->name }}</td>
              <td>£{{ number_format($order->total, 2) }}</td>
              <td><span class="ad-pill {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="4" class="muted">No orders yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <section class="ad-panel">
    <div class="ad-panel-head">
      <h2>Recent uniform orders</h2>
      <a href="{{ route('admin.school-uniform-orders.index') }}" class="ad-link">View all</a>
    </div>
    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead>
          <tr>
            <th>Order</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentUniformOrders as $order)
            <tr>
              <td><a href="{{ route('admin.school-uniform-orders.show', $order) }}">{{ $order->order_number }}</a></td>
              <td>{{ $order->customer_name }}</td>
              <td>£{{ number_format($order->total, 2) }}</td>
              <td><span class="ad-pill {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="4" class="muted">No uniform orders yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
</div>

<div class="ad-charts">
  <section class="ad-chart-card">
    <h2>Sales Overview (Last 6 Months)</h2>
    <div class="ad-chart-wrap">
      <canvas id="salesChart" height="280"></canvas>
    </div>
  </section>
  <section class="ad-chart-card">
    <h2>Uniform Shop Sales (Last 6 Months)</h2>
    <div class="ad-chart-wrap">
      <canvas id="uniformSalesChart" height="280"></canvas>
    </div>
  </section>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/themes/dark.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
  const sales = @json($salesChart);
  const uniformSales = @json($uniformSalesChart);

  const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#16191e',
        titleColor: '#9a958c',
        bodyColor: '#f4f1ec',
        borderColor: 'rgba(255,255,255,.14)',
        borderWidth: 1,
        padding: 12,
        displayColors: true,
        callbacks: {}
      }
    },
    scales: {
      x: {
        grid: { color: 'rgba(255,255,255,.06)', drawBorder: false },
        ticks: { color: '#9a958c', font: { size: 11 } }
      },
      y: {
        beginAtZero: true,
        grid: { color: 'rgba(255,255,255,.06)', drawBorder: false },
        ticks: { color: '#9a958c', font: { size: 11 } }
      }
    }
  };

  function moneyChart(ctx, data, colour, label) {
    if (!ctx) return;
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [{
          label,
          data: data.values,
          borderColor: colour,
          backgroundColor: colour,
          pointBackgroundColor: colour,
          pointBorderColor: colour,
          pointRadius: 5,
          pointHoverRadius: 7,
          borderWidth: 3,
          tension: 0.35,
          fill: false
        }]
      },
      options: {
        ...chartDefaults,
        plugins: {
          ...chartDefaults.plugins,
          tooltip: {
            ...chartDefaults.plugins.tooltip,
            callbacks: {
              label: (c) => `${label}: £${Number(c.parsed.y).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
            }
          }
        },
        scales: {
          ...chartDefaults.scales,
          y: {
            ...chartDefaults.scales.y,
            title: { display: true, text: 'Amount (£)', color: '#9a958c', font: { size: 12 } },
            ticks: {
              ...chartDefaults.scales.y.ticks,
              callback: (v) => '£' + Number(v).toLocaleString()
            }
          }
        }
      }
    });
  }

  moneyChart(document.getElementById('salesChart'), sales, '#38a169', 'Sales Amount');
  moneyChart(document.getElementById('uniformSalesChart'), uniformSales, '#e85820', 'Uniform Sales');

  const rangeInput = document.getElementById('dashRange');
  if (rangeInput && window.flatpickr) {
    flatpickr(rangeInput, {
      mode: 'range',
      dateFormat: 'Y-m-d',
      defaultDate: [@json($dateFrom), @json($dateTo)],
      maxDate: 'today',
      onClose(selectedDates, dateStr) {
        if (selectedDates.length === 2) {
          rangeInput.value = dateStr;
        }
      }
    });
  }
})();
</script>
@endsection
