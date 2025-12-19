<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';

// Get summary statistics
$total_km = 0;
$total_drives = 0;

$count_query = "SELECT COUNT(*) as total FROM Drive_Log";
$count_result = $conn->query($count_query);
if ($count_result) {
    $total_drives = $count_result->fetch_assoc()['total'];
}

$km_query = "SELECT SUM(Trip_Length) as total_km FROM Drive_Log";
$km_result = $conn->query($km_query);
if ($km_result) {
    $row = $km_result->fetch_assoc();
    $total_km = $row['total_km'] ? $row['total_km'] : 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel with Shehana - Driving Experience Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f0fc;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: #a29bfe;
            color: white;
            text-align: center;
            padding: 1rem;
        }
        header .logo {
            font-family: 'Brush Script MT', cursive;
            font-size: 2rem;
        }
        .sidebar {
            width: 200px;
            background-color: #a29bfe;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        .stats-card h3 {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .menu-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: #333;
            transition: transform 0.3s;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .menu-card .icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .menu-card h3 {
            color: #6c5ce7;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex flex-column flex-md-row">
        <div class="sidebar">
            <div class="logo">Travel with Shehana</div>
            <a href="index.php">🏠 Dashboard</a>
            <a href="add_drive.php">➕ Log Driving</a>
            <a href="view_drives.php">📋 View Data</a>
            <a href="statistics.php">📊 Statistics</a>
            <a href="manage_variables.php">⚙️ Manage Variables</a>
        </div>

        <div class="content">
            <h1 class="text-center logo mb-4">Travel with Shehana</h1>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="stats-card">
                        <p class="mb-2">Total Driving Sessions</p>
                        <h3><?php echo number_format($total_drives); ?></h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stats-card">
                        <p class="mb-2">Total Distance Traveled</p>
                        <h3><?php echo number_format($total_km, 1); ?> <small>km</small></h3>
                    </div>
                </div>
            </div>

            <div class="menu-grid">
                <a href="add_drive.php" class="menu-card">
                    <div class="icon">🚗</div>
                    <h3>Log Driving</h3>
                    <p>Add a new driving experience</p>
                </a>
                
                <a href="view_drives.php" class="menu-card">
                    <div class="icon">📋</div>
                    <h3>View Data</h3>
                    <p>Browse all driving records</p>
                </a>
                
                <a href="statistics.php" class="menu-card">
                    <div class="icon">📊</div>
                    <h3>Statistics & Charts</h3>
                    <p>View detailed analytics</p>
                </a>
                
                <a href="manage_variables.php" class="menu-card">
                    <div class="icon">⚙️</div>
                    <h3>Manage Variables</h3>
                    <p>Add weather, road types, etc.</p>
                </a>
            </div>

            <?php
            // Show recent drives if any exist
            if ($total_drives > 0) {
                $recent_query = "SELECT dl.Log_Date, dl.Trip_Length, wc.Condition_Desc, rc.Road_Desc
                                 FROM Drive_Log dl
                                 INNER JOIN Weather_Condition wc ON dl.Condition_ID = wc.Condition_ID
                                 INNER JOIN Road_Category rc ON dl.RoadCat_ID = rc.RoadCat_ID
                                 ORDER BY dl.Log_Date DESC, dl.Start_Time DESC
                                 LIMIT 5";
                $recent_result = $conn->query($recent_query);
                
                if ($recent_result && $recent_result->num_rows > 0) {
                    echo '<div class="mt-4">
                            <h3>Recent Drives</h3>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Distance (km)</th>
                                            <th>Weather</th>
                                            <th>Road Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                    
                    while($row = $recent_result->fetch_assoc()) {
                        echo '<tr>
                                <td>' . date('M d, Y', strtotime($row['Log_Date'])) . '</td>
                                <td>' . number_format($row['Trip_Length'], 1) . ' km</td>
                                <td>' . htmlspecialchars($row['Condition_Desc']) . '</td>
                                <td>' . htmlspecialchars($row['Road_Desc']) . '</td>
                              </tr>';
                    }
                    
                    echo '</tbody></table></div></div>';
                }
            } else {
                echo '<div class="alert alert-info mt-4" role="alert">
                        No driving sessions recorded yet. Click "Log Driving" to get started! 🚗
                      </div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
