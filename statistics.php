<?php
require_once 'config.php';

// Query 1: Total KM per weather condition
$weather_sql = "SELECT wc.Condition_Desc, ROUND(SUM(dl.Trip_Length), 2) AS Total_KM
                FROM Drive_Log dl
                INNER JOIN Weather_Condition wc ON dl.Condition_ID = wc.Condition_ID
                GROUP BY wc.Condition_Desc
                ORDER BY Total_KM DESC";
$weather_result = $conn->query($weather_sql);

// Query 3: Success rate of actions
$success_sql = "SELECT at.Action_Type, COUNT(*) AS Total_Attempts,
                SUM(at.WasSuccessful) AS Successful_Attempts,
                ROUND((SUM(at.WasSuccessful) / COUNT(*)) * 100, 2) AS Success_Rate
                FROM Actions_Taken at
                INNER JOIN Drive_Log_Actions dla ON at.Action_ID = dla.Action_ID
                GROUP BY at.Action_Type
                ORDER BY Success_Rate";
$success_result = $conn->query($success_sql);

// Total kilometers
$total_sql = "SELECT SUM(Trip_Length) AS Total_KM FROM Drive_Log";
$total_result = $conn->query($total_sql);
$total_km = $total_result->fetch_assoc()['Total_KM'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driving Statistics</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Driving Statistics</h1>
        
        <section class="stat-card">
            <h2>Total Distance</h2>
            <p class="big-number"><?php echo number_format($total_km, 2); ?> km</p>
        </section>
        
        <section>
            <h2>Distance by Weather Condition</h2>
            <table>
                <thead>
                    <tr>
                        <th>Weather</th>
                        <th>Total KM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $weather_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Condition_Desc']); ?></td>
                        <td><?php echo number_format($row['Total_KM'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        
        <section>
            <h2>Action Success Rates</h2>
            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Attempts</th>
                        <th>Successful</th>
                        <th>Success Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $success_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Action_Type']); ?></td>
                        <td><?php echo $row['Total_Attempts']; ?></td>
                        <td><?php echo $row['Successful_Attempts']; ?></td>
                        <td><?php echo $row['Success_Rate']; ?>%</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        
        <a href="index.php" class="btn">Back to Dashboard</a>
    </main>
</body>
</html>
<?php $conn->close(); ?>
