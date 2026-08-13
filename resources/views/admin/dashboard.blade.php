@extends('admin.layouts.app')

@section('content')
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
      <h2>Recent enquiries</h2>
      <a href="{{ route('admin.enquiries.index') }}" class="ad-link">View all</a>
    </div>
    <div class="ad-table-wrap">
      <table class="ad-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Name</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentEnquiries as $enquiry)
            <tr>
              <td><a href="{{ route('admin.enquiries.show', $enquiry) }}">{{ $enquiry->reference }}</a></td>
              <td>{{ $enquiry->full_name }}</td>
              <td><span class="ad-pill {{ $enquiry->status }}">{{ $enquiry->displayStatus() }}</span></td>
            </tr>
          @empty
            <tr><td colspan="3" class="muted">No enquiries yet.</td></tr>
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
    <h2>Branding Enquiries (Last 6 Months)</h2>
    <div class="ad-chart-wrap">
      <canvas id="enquiriesChart" height="280"></canvas>
    </div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
  const sales = @json($salesChart);
  const enquiries = @json($enquiriesChart);

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

  const salesCtx = document.getElementById('salesChart');
  if (salesCtx) {
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: sales.labels,
        datasets: [{
          label: 'Sales Amount',
          data: sales.values,
          borderColor: '#38a169',
          backgroundColor: '#38a169',
          pointBackgroundColor: '#38a169',
          pointBorderColor: '#38a169',
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
              label: (ctx) => `Sales Amount: £${Number(ctx.parsed.y).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
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

  const enquiriesCtx = document.getElementById('enquiriesChart');
  if (enquiriesCtx) {
    new Chart(enquiriesCtx, {
      type: 'line',
      data: {
        labels: enquiries.labels,
        datasets: [{
          label: 'Enquiries',
          data: enquiries.values,
          borderColor: '#e85820',
          backgroundColor: '#e85820',
          pointBackgroundColor: '#e85820',
          pointBorderColor: '#e85820',
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
              label: (ctx) => `Enquiries: ${ctx.parsed.y}`
            }
          }
        },
        scales: {
          ...chartDefaults.scales,
          y: {
            ...chartDefaults.scales.y,
            title: { display: true, text: 'Enquiries', color: '#9a958c', font: { size: 12 } },
            ticks: {
              ...chartDefaults.scales.y.ticks,
              stepSize: 1,
              precision: 0
            }
          }
        }
      }
    });
  }
})();
</script>
@endsection
