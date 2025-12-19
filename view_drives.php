<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';
session_start();

// Handle delete request
if (isset($_GET['delete'])) {
    $log_id = intval($_GET['delete']);
    
    // First delete from junction table
    $delete_actions = "DELETE FROM Drive_Log_Actions WHERE Log_ID = ?";
    $stmt = $conn->prepare($delete_actions);
    $stmt->bind_param("i", $log_id);
    $stmt->execute();
    
    // Then delete from main table
    $delete_log = "DELETE FROM Drive_Log WHERE Log_ID = ?";
    $stmt = $conn->prepare($delete_log);
    $stmt->bind_param("i", $log_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Drive log deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting record.";
    }
    $stmt->close();
    header("Location: view_drives.php");
    exit;
}

// Fetch all drives with details
$query = "SELECT 
            dl.Log_ID,
            dl.Log_Date,
            dl.Start_Time,
            dl.End_Time,
            TIMEDIFF(dl.End_Time, dl.Start_Time) AS Duration,
            dl.Trip_Length,
            wc.Condition_Desc AS Weather,
            fs.Fuel_Level,
            rc.Road_Desc AS Road_Type,
            st.Surface_Desc AS Surface,
            GROUP_CONCAT(at.Action_Type SEPARATOR ', ') AS Actions
          FROM Drive_Log dl
          INNER JOIN Weather_Condition wc ON dl.Condition_ID = wc.Condition_ID
          INNER JOIN Fuel_Status fs ON dl.Fuel_ID = fs.Fuel_ID
          INNER JOIN Road_Category rc ON dl.RoadCat_ID = rc.RoadCat_ID
          INNER JOIN Surface_Type st ON dl.Surface_ID = st.Surface_ID
          LEFT JOIN Drive_Log_Actions dla ON dl.Log_ID = dla.Log_ID
          LEFT JOIN Actions_Taken at ON dla.Action_ID = at.Action_ID
          GROUP BY dl.Log_ID
          ORDER BY dl.Log_Date DESC, dl.Start_Time DESC";

$result = $conn->query($query);

// Calculate total distance
$total_query = "SELECT SUM(Trip_Length) as total_km FROM Drive_Log";
$total_result = $conn->query($total_query);
$total_km = $total_result->fetch_assoc()['total_km'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Driving Data - Travel with Shehana</title>
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
        .sidebar .logo {
            font-family: 'Brush Script MT', cursive;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .total-distance {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .total-distance h3 {
            font-size: 2.5rem;
            margin: 0;
        }
        .btn-primary {
            background-color: #6c5ce7;
            border: none;
        }
        .btn-primary:hover {
            background-color: #5a51c3;
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
            <h1 class="mb-4">📋 All Driving Experiences</h1>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="total-distance">
                <p class="mb-2">Total Distance Traveled</p>
                <h3><?php echo number_format($total_km, 1); ?> km</h3>
            </div>

            <?php if ($result && $result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Duration</th>
                                <th>Distance (km)</th>
                                <th>Weather</th>
                                <th>Fuel</th>
                                <th>Road Type</th>
                                <th>Surface</th>
                                <th>Actions Performed</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('M d, Y', strtotime($row['Log_Date'])); ?></td>
                                <td><?php echo date('g:i A', strtotime($row['Start_Time'])) . ' - ' . 
                                           date('g:i A', strtotime($row['End_Time'])); ?></td>
                                <td><?php echo $row['Duration']; ?></td>
                                <td><strong><?php echo number_format($row['Trip_Length'], 1); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['Weather']); ?></td>
                                <td><?php echo htmlspecialchars($row['Fuel_Level']); ?></td>
                                <td><?php echo htmlspecialchars($row['Road_Type']); ?></td>
                                <td><?php echo htmlspecialchars($row['Surface']); ?></td>
                                <td><small><?php echo htmlspecialchars($row['Actions'] ?? 'None'); ?></small></td>
                                <td>
                                    <a href="edit_drive.php?id=<?php echo $row['Log_ID']; ?>" 
                                       class="btn btn-warning btn-sm">Edit</a>
                                    <a href="view_drives.php?delete=<?php echo $row['Log_ID']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No driving experiences recorded yet. <a href="add_drive.php">Add your first drive!</a>
                </div>
            <?php endif; ?>

            <a href="index.php" class="btn btn-primary mt-3">← Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
