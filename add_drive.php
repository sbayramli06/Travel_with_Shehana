<?php
require_once 'config.php';
session_start();

// Fetch dropdown options from database
$weather_query = "SELECT Condition_ID, Condition_Desc FROM Weather_Condition ORDER BY Condition_Desc";
$weather_result = $conn->query($weather_query);

$fuel_query = "SELECT Fuel_ID, Fuel_Level FROM Fuel_Status ORDER BY Fuel_ID";
$fuel_result = $conn->query($fuel_query);

$road_query = "SELECT RoadCat_ID, Road_Desc FROM Road_Category ORDER BY Road_Desc";
$road_result = $conn->query($road_query);

$surface_query = "SELECT Surface_ID, Surface_Desc FROM Surface_Type ORDER BY Surface_Desc";
$surface_result = $conn->query($surface_query);

$actions_query = "SELECT Action_ID, Action_Type FROM Actions_Taken ORDER BY Action_Type";
$actions_result = $conn->query($actions_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Driving Experience</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <h1>Add Driving Experience</h1>
        
        <form action="process_drive.php" method="POST" id="driveForm">
            <fieldset>
                <legend>Trip Details</legend>
                
                <label for="log_date">Date:</label>
                <input type="date" id="log_date" name="log_date" 
                       value="<?php echo date('Y-m-d'); ?>" required>
                
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required>
                
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required>
                
                <label for="trip_length">Distance (km):</label>
                <input type="number" id="trip_length" name="trip_length" 
                       step="0.1" min="0" required>
            </fieldset>
            
            <fieldset>
                <legend>Conditions</legend>
                
                <label for="weather">Weather Condition:</label>
                <select id="weather" name="condition_id" required>
                    <option value="">Select weather...</option>
                    <?php while($row = $weather_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['Condition_ID']; ?>">
                            <?php echo htmlspecialchars($row['Condition_Desc']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="fuel">Fuel Level:</label>
                <select id="fuel" name="fuel_id" required>
                    <option value="">Select fuel level...</option>
                    <?php while($row = $fuel_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['Fuel_ID']; ?>">
                            <?php echo htmlspecialchars($row['Fuel_Level']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="road">Road Category:</label>
                <select id="road" name="roadcat_id" required>
                    <option value="">Select road type...</option>
                    <?php while($row = $road_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['RoadCat_ID']; ?>">
                            <?php echo htmlspecialchars($row['Road_Desc']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="surface">Surface Type:</label>
                <select id="surface" name="surface_id" required>
                    <option value="">Select surface...</option>
                    <?php while($row = $surface_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['Surface_ID']; ?>">
                            <?php echo htmlspecialchars($row['Surface_Desc']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </fieldset>
            
            <fieldset>
                <legend>Actions Performed</legend>
                <?php while($row = $actions_result->fetch_assoc()): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="actions[]" 
                               value="<?php echo $row['Action_ID']; ?>">
                        <?php echo htmlspecialchars($row['Action_Type']); ?>
                    </label>
                <?php endwhile; ?>
            </fieldset>
            
            <button type="submit">Save Driving Experience</button>
            <a href="index.php" class="btn-secondary">Cancel</a>
        </form>
    </main>
</body>
</html>
<?php $conn->close(); ?>
