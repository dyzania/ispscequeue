<?php
$pageTitle = 'Performance Reports';
include __DIR__ . '/../../includes/admin-layout-header.php';

$db = Database::getInstance()->getConnection();

$officeId = $_SESSION['office_id'] ?? 1;

// --- Analytics Calculations ---
// 1. Total Tickets Today
$stmt = $db->prepare("SELECT COUNT(*) as count FROM tickets WHERE DATE(created_at) = CURDATE() AND office_id = ?");
$stmt->execute([$officeId]);
$todayTickets = $stmt->fetch()['count'];

// 2. Average Process Time (Wait Time)
$stmt = $db->prepare("
    SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, called_at)) as avg_wait 
    FROM tickets 
    WHERE status IN ('called', 'serving', 'completed') AND DATE(created_at) = CURDATE() AND office_id = ?
");
$stmt->execute([$officeId]);
$avgWaitTime = round($stmt->fetch()['avg_wait'] ?? 0);

// 3. Average Service Time
$stmt = $db->prepare("
    SELECT AVG(service_time_accumulated) / 60 as avg_service
    FROM tickets 
    WHERE status = 'completed' AND DATE(created_at) = CURDATE() AND office_id = ?
");
$stmt->execute([$officeId]);
$avgServiceTime = round($stmt->fetch()['avg_service'] ?? 0);

// 5. Peak Hours (Today)
$stmt = $db->prepare("
    SELECT DATE_FORMAT(created_at, '%H') as hour_of_day, COUNT(*) as count 
    FROM tickets 
    WHERE DATE(created_at) = CURDATE() AND office_id = ?
    GROUP BY hour_of_day 
    ORDER BY hour_of_day ASC
");
$stmt->execute([$officeId]);
$peakHoursDataRaw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
// Fill missing hours
$peakHours = [];
for($i=8; $i<=17; $i++) {
    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
    $peakHours[$h] = $peakHoursDataRaw[$h] ?? 0;
}

// 6. Status Distribution (Today)
$stmt = $db->prepare("
    SELECT status, COUNT(*) as count 
    FROM tickets 
    WHERE DATE(created_at) = CURDATE() AND office_id = ?
    GROUP BY status
");
$stmt->execute([$officeId]);
$statusStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$statusStats = array_merge(['completed'=>0, 'serving'=>0, 'waiting'=>0, 'cancelled'=>0], $statusStats);

$completionRate = 0;
if (($statusStats['completed'] + $statusStats['cancelled']) > 0) {
    $completionRate = round(($statusStats['completed'] / ($statusStats['completed'] + $statusStats['cancelled'])) * 100);
}


// 8. Wait Time & SLA Metrics
$stmt = $db->prepare("
    SELECT MAX(TIMESTAMPDIFF(MINUTE, created_at, IFNULL(called_at, NOW()))) as max_wait
    FROM tickets 
    WHERE status IN ('waiting', 'called', 'serving') AND DATE(created_at) = CURDATE() AND office_id = ?
");
$stmt->execute([$officeId]);
$longestWait = round($stmt->fetch()['max_wait'] ?? 0);

$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total_completed,
        SUM(CASE WHEN TIMESTAMPDIFF(MINUTE, t.served_at, t.completed_at) <= s.estimated_time THEN 1 ELSE 0 END) as within_sla
    FROM tickets t
    JOIN services s ON t.service_id = s.id
    WHERE t.status = 'completed' AND DATE(t.created_at) = CURDATE() AND t.office_id = ?
");
$stmt->execute([$officeId]);
$slaData = $stmt->fetch();
$slaCompliance = $slaData['total_completed'] > 0 ? round(($slaData['within_sla'] / $slaData['total_completed']) * 100) : 0;

$stmt = $db->prepare("
    SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, completed_at)) as avg_turnaround
    FROM tickets 
    WHERE status = 'completed' AND DATE(created_at) = CURDATE() AND office_id = ?
");
$stmt->execute([$officeId]);
$avgTurnaround = round($stmt->fetch()['avg_turnaround'] ?? 0);

$currentQueueSize = $statusStats['waiting'];

// 10. Service Performance
$stmt = $db->prepare("
    SELECT s.service_name, s.service_code, COUNT(t.id) as count,
           AVG(CASE WHEN t.status='completed' THEN t.service_time_accumulated / 60 ELSE NULL END) as avg_svc_time
    FROM tickets t
    JOIN services s ON t.service_id = s.id
    WHERE DATE(t.created_at) = CURDATE() AND t.office_id = ?
    GROUP BY s.service_name, s.service_code
");
$stmt->execute([$officeId]);
$serviceStats = $stmt->fetchAll();

// Prepare JSON data for charts
$chartData = [
    'peakHours' => [
        'labels' => array_keys($peakHours),
        'data' => array_values($peakHours)
    ],
    'servicePerformance' => [
        'labels' => array_column($serviceStats, 'service_code'),
        'names' => array_column($serviceStats, 'service_name'),
        'volume' => array_column($serviceStats, 'count'),
        'avgTime' => array_map(function($v) { return round($v ?? 0); }, array_column($serviceStats, 'avg_svc_time'))
    ]
];
?>

<!-- Add Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-12">
    <!-- Page Header with Premium Accent -->
    <div class="relative py-8">
        <div class="absolute inset-0 bg-gradient-to-r from-primary-600/5 via-indigo-600/5 to-secondary-600/5 blur-3xl opacity-50"></div>
        <div class="relative text-center">
            <p class="text-[11px] font-black uppercase tracking-[0.4em] text-primary-500 mb-3 animate-pulse">Operations Intelligence</p>
            <h1 class="text-5xl font-black text-slate-900 font-heading tracking-tight leading-none mb-4">Admin Dashboard</h1>
            <div class="w-16 h-1 bg-primary-600 mx-auto rounded-full"></div>
        </div>
    </div>

    <!-- Core Metrics Grid (Glassmorphism inspired) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Metric Card Template -->
        <?php 
        $metrics = [
            ['label' => 'Total Volume', 'val' => $todayTickets, 'unit' => '', 'icon' => 'fa-users', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
            ['label' => 'Queue Size', 'val' => $currentQueueSize, 'unit' => '', 'icon' => 'fa-list-ol', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
            ['label' => 'Avg Wait Time', 'val' => $avgWaitTime, 'unit' => 'm', 'icon' => 'fa-hourglass-half', 'bg' => 'bg-slate-50', 'text' => 'text-slate-600'],
            ['label' => 'Longest Wait', 'val' => $longestWait, 'unit' => 'm', 'icon' => 'fa-stopwatch', 'bg' => 'bg-rose-50', 'text' => 'text-rose-600'],
            ['label' => 'Avg Service', 'val' => $avgServiceTime, 'unit' => 'm', 'icon' => 'fa-tools', 'bg' => 'bg-cyan-50', 'text' => 'text-cyan-600'],
            ['label' => 'Turnaround', 'val' => $avgTurnaround, 'unit' => 'm', 'icon' => 'fa-sync-alt', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
            ['label' => 'Completion', 'val' => $completionRate, 'unit' => '%', 'icon' => 'fa-check-circle', 'bg' => 'bg-primary-50', 'text' => 'text-primary-600'],
            ['label' => 'SLA Met', 'val' => $slaCompliance, 'unit' => '%', 'icon' => 'fa-clipboard-check', 'bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
        ];

        foreach ($metrics as $m): ?>
        <div class="glass-card bg-white/70 backdrop-blur-md rounded-2xl p-6 border border-white shadow-xl shadow-slate-200/40 transform transition-all hover:scale-[1.03] hover:shadow-2xl group flex flex-col items-center text-center space-y-3">
            <div class="w-12 h-12 <?php echo $m['bg']; ?> <?php echo $m['text']; ?> rounded-xl flex items-center justify-center text-xl shadow-inner transition-transform group-hover:rotate-6">
                <i class="fas <?php echo $m['icon']; ?>"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1"><?php echo $m['label']; ?></p>
                <p class="text-3xl font-black text-slate-900 tracking-tighter">
                    <?php echo $m['val']; ?><span class="text-sm ml-0.5 opacity-40 font-bold"><?php echo $m['unit']; ?></span>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>


    <!-- Interactive Visualizations Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Service Performance Chart -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/30 border border-slate-100 p-8 flex flex-col items-center">
            <div class="flex items-center justify-between w-full mb-8">
                <h2 class="text-xl font-black text-slate-900 font-heading">Service Dynamics</h2>
                <div class="px-3 py-1 bg-slate-50 rounded-full border border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">Live Performance</div>
            </div>
            <div class="h-[350px] w-full">
                <canvas id="servicePerformanceChart"></canvas>
            </div>
        </div>

        <!-- Peak Hours Chart -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/30 border border-slate-100 p-8 flex flex-col items-center">
            <div class="flex items-center justify-between w-full mb-8">
                <h2 class="text-xl font-black text-slate-900 font-heading">Temporal Volume</h2>
                <div class="px-3 py-1 bg-indigo-50 rounded-full border border-indigo-100 text-[10px] font-black text-indigo-400 uppercase tracking-widest">Peak Hour Trends</div>
            </div>
            <div class="h-[350px] w-full">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>


    </div>
</div>

<style>
.glass-card {
    border: 1px solid rgba(255, 255, 255, 0.7);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
}
</style>

<script>
const chartData = <?php echo json_encode($chartData); ?>;

// Global settings for a modern "Apple-esque" look
Chart.defaults.font.family = "'Outfit', 'Inter', sans-serif";
Chart.defaults.font.weight = '600';
Chart.defaults.color = '#94a3b8';
Chart.defaults.elements.bar.borderRadius = 8;
Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(15, 23, 42, 0.9)';
Chart.defaults.plugins.tooltip.padding = 12;
Chart.defaults.plugins.tooltip.usePointStyle = true;

// 1. Peak Hours Chart
const peakCtx = document.getElementById('peakHoursChart').getContext('2d');
const peakGradient = peakCtx.createLinearGradient(0, 0, 0, 400);
peakGradient.addColorStop(0, '#6366f1');
peakGradient.addColorStop(1, '#a855f7');

new Chart(peakCtx, {
    type: 'bar',
    data: {
        labels: chartData.peakHours.labels.map(h => h + ':00'),
        datasets: [{
            label: 'Tickets',
            data: chartData.peakHours.data,
            backgroundColor: peakGradient,
            hoverBackgroundColor: '#4f46e5',
            barThickness: 16
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { 
                beginAtZero: true, 
                grid: { color: '#f1f5f9', drawBorder: false },
                ticks: { stepSize: 5 }
            },
            x: { grid: { display: false }, ticks: { padding: 10 } }
        }
    }
});


// 3. Service Performance Chart
const svcCtx = document.getElementById('servicePerformanceChart').getContext('2d');
new Chart(svcCtx, {
    type: 'bar',
    data: {
        labels: chartData.servicePerformance.labels,
        datasets: [
            {
                label: 'Service Time',
                data: chartData.servicePerformance.avgTime,
                backgroundColor: '#38bdf8',
                barThickness: 10
            },
            {
                label: 'Volume',
                data: chartData.servicePerformance.volume,
                backgroundColor: '#e2e8f0',
                barThickness: 10
            }
        ]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 8, usePointStyle: true } },
            tooltip: {
                callbacks: {
                    afterTitle: (ctx) => chartData.servicePerformance.names[ctx[0].dataIndex]
                }
            }
        },
        scales: {
            x: { grid: { color: '#f8fafc' }, ticks: { font: { size: 10 } } },
            y: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
        }
    }
});
</script>

<?php include __DIR__ . '/../../includes/admin-layout-footer.php'; ?>
