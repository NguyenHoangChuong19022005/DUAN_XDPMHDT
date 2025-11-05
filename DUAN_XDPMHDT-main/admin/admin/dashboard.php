<div class="content-wrapper">
    <div class="content-header">
        <h1>Admin Dashboard</h1>
    </div>
    <section class="content">
        <div class="row">
            <!-- 4 Cards realtime -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="total-users">0</h3>
                        <p>Tổng Users</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <!-- Tương tự cho scholarships, applications, revenue -->
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Realtime Stats</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="realtimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Realtime chart với Socket.io
const ctx = document.getElementById('realtimeChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: { labels: [], datasets: [{ label: 'Active Users', data: [] }] },
    options: { scales: { y: { beginAtZero: true } } }
});

// Socket.io update realtime
const socket = io();
socket.on('stats_update', (data) => {
    chart.data.labels.push(new Date().toLocaleTimeString());
    chart.data.datasets[0].data.push(data.active);
    chart.update();
    document.getElementById('total-users').textContent = data.total;
});
</script>