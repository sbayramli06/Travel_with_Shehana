<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';
session_start();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];
    $value = trim($_POST['value']);
    
    $success = false;
    
    switch($table) {
        case 'weather':
            $sql = "INSERT INTO Weather_Condition (Condition_Desc) VALUES (?)";
            break;
        case 'fuel':
            $sql = "INSERT INTO Fuel_Status (Fuel_Level) VALUES (?)";
            break;
        case 'road':
            $sql = "INSERT INTO Road_Category (Road_Desc) VALUES (?)";
            break;
        case 'surface':
            $sql = "INSERT INTO Surface_Type (Surface_Desc) VALUES (?)";
            break;
        case 'action':
            $sql = "INSERT INTO Actions_Taken (Action_Type, WasSuccessful) VALUES (?, 1)";
            break;
        default:
            $_SESSION['error'] = "Invalid table selection.";
            header("Location: manage_variables.php");
            exit;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $value);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "New variable added successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
    $stmt->close();
    header("Location: manage_variables.php");
    exit;
}

// Handle delete requests
if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = intval($_GET['delete']);
    $type = $_GET['type'];
    
    switch($type) {
        case 'weather':
            $sql = "DELETE FROM Weather_Condition WHERE Condition_ID = ?";
            break;
        case 'fuel':
            $sql = "DELETE FROM Fuel_Status WHERE Fuel_ID = ?";
            break;
        case 'road':
            $sql = "DELETE FROM Road_Category WHERE RoadCat_ID = ?";
            break;
        case 'surface':
            $sql = "DELETE FROM Surface_Type WHERE Surface_ID = ?";
            break;
        case 'action':
            $sql = "DELETE FROM Actions_Taken WHERE Action_ID = ?";
            break;
        default:
            $_SESSION['error'] = "Invalid type.";
            header("Location: manage_variables.php");
            exit;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Variable deleted successfully!";
    } else {
        $_SESSION['error'] = "Cannot delete: Variable is in use.";
    }
    $stmt->close();
    header("Location: manage_variables.php");
    exit;
}

// Fetch all current variables
$weather = $conn->query("SELECT * FROM Weather_Condition ORDER BY Condition_Desc");
$fuel = $conn->query("SELECT * FROM Fuel_Status ORDER BY Fuel_ID");
$roads = $conn->query("SELECT * FROM Road_Category ORDER BY Road_Desc");
$surfaces = $conn->query("SELECT * FROM Surface_Type ORDER BY Surface_Desc");
$actions = $conn->query("SELECT * FROM Actions_Taken ORDER BY Action_Type");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Variables - Travel with Shehana</title>
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
        .form-box {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .variable-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .variable-section h3 {
            color: #6c5ce7;
            margin-bottom: 15px;
        }
        .badge {
            margin: 5px;
            font-size: 0.9rem;
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
            <h1 class="mb-4">⚙️ Manage Variables</h1>

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

            <!-- Add New Variable Form -->
            <div class="form-box">
                <h2 class="text-center mb-4">Add New Variable</h2>
                <form method="POST" action="manage_variables.php">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="table" class="form-label">Variable Type</label>
                            <select name="table" id="table" class="form-select" required>
                                <option value="">Select type...</option>
                                <option value="weather">Weather Condition</option>
                                <option value="fuel">Fuel Level</option>
                                <option value="road">Road Category</option>
                                <option value="surface">Surface Type</option>
                                <option value="action">Action Type</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="value" class="form-label">New Value</label>
                            <input type="text" name="value" id="value" class="form-control" 
                                   placeholder="e.g., Foggy, Gravel, etc." required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Display Current Variables -->
            <div class="row">
                <div class="col-md-6">
                    <div class="variable-section">
                        <h3>☀️ Weather Conditions</h3>
                        <?php while($row = $weather->fetch_assoc()): ?>
                            <span class="badge bg-info">
                                <?php echo htmlspecialchars($row['Condition_Desc']); ?>
                                <a href="?delete=<?php echo $row['Condition_ID']; ?>&type=weather" 
                                   class="text-white text-decoration-none"
                                   onclick="return confirm('Delete this item?')">×</a>
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="variable-section">
                        <h3>⛽ Fuel Levels</h3>
                        <?php while($row = $fuel->fetch_assoc()): ?>
                            <span class="badge bg-warning">
                                <?php echo htmlspecialchars($row['Fuel_Level']); ?>
                                <a href="?delete=<?php echo $row['Fuel_ID']; ?>&type=fuel" 
                                   class="text-white text-decoration-none"
                                   onclick="return confirm('Delete this item?')">×</a>
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="variable-section">
                        <h3>🛣️ Road Categories</h3>
                        <?php while($row = $roads->fetch_assoc()): ?>
                            <span class="badge bg-success">
                                <?php echo htmlspecialchars($row['Road_Desc']); ?>
                                <a href="?delete=<?php echo $row['RoadCat_ID']; ?>&type=road" 
                                   class="text-white text-decoration-none"
                                   onclick="return confirm('Delete this item?')">×</a>
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="variable-section">
                        <h3>🌍 Surface Types</h3>
                        <?php while($row = $surfaces->fetch_assoc()): ?>
                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($row['Surface_Desc']); ?>
                                <a href="?delete=<?php echo $row['Surface_ID']; ?>&type=surface" 
                                   class="text-white text-decoration-none"
                                   onclick="return confirm('Delete this item?')">×</a>
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="variable-section">
                        <h3>🚦 Action Types</h3>
                        <?php while($row = $actions->fetch_assoc()): ?>
                            <span class="badge bg-danger">
                                <?php echo htmlspecialchars($row['Action_Type']); ?>
                                <a href="?delete=<?php echo $row['Action_ID']; ?>&type=action" 
                                   class="text-white text-decoration-none"
                                   onclick="return confirm('Delete this item?')">×</a>
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <a href="index.php" class="btn btn-primary mt-3">← Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
