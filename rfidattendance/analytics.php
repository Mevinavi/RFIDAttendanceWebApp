<?php
include('connectDB.php');

// Fetch summary stats
$today = date('Y-m-d');

// Total logs
$totalQuery = "SELECT COUNT(*) as total FROM users_logs";
$totalResult = mysqli_query($conn, $totalQuery);
$totalLogs = mysqli_fetch_assoc($totalResult)['total'];

// Distinct users
$usersQuery = "SELECT COUNT(DISTINCT username) as users FROM users_logs";
$usersResult = mysqli_query($conn, $usersQuery);
$distinctUsers = mysqli_fetch_assoc($usersResult)['users'];

// Today's logs
$todayQuery = "SELECT COUNT(*) as today FROM users_logs WHERE checkindate = '$today'";
$todayResult = mysqli_query($conn, $todayQuery);
$todaysLogs = mysqli_fetch_assoc($todayResult)['today'];

// Checked out
$checkoutQuery = "SELECT COUNT(*) as out_count FROM users_logs WHERE card_out = 1";
$checkoutResult = mysqli_query($conn, $checkoutQuery);
$checkedOut = mysqli_fetch_assoc($checkoutResult)['out_count'];

// Last 7 days attendance data
$chartQuery = "SELECT checkindate, COUNT(*) as count FROM users_logs GROUP BY checkindate ORDER BY checkindate DESC LIMIT 7";
$chartResult = mysqli_query($conn, $chartQuery);
$chartData = [];
while($row = mysqli_fetch_assoc($chartResult)) {
    $chartData[] = $row;
}
$chartData = array_reverse($chartData);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Analytics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include('header.php'); ?>

<div class="container mt-4">
    <h2 class="mb-4">Attendance Analytics Dashboard</h2>
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total Logs</h5>
                    <h2><?php echo $totalLogs; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Distinct Users</h5>
                    <h2><?php echo $distinctUsers; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Today</h5>
                    <h2><?php echo $todaysLogs; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Checked Out</h5>
                    <h2><?php echo $checkedOut; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm p-3">
        <h5>Attendance - Last 7 Days</h5>
        <canvas id="attendanceChart" height="100"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($chartData, 'checkindate')); ?>,
            datasets: [{
                label: 'Logs',
                data: <?php echo json_encode(array_column($chartData, 'count')); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision:0
                    }
                }
            }
        }
    });
</script>

</body>
</html>
