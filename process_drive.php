<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $log_date = $conn->real_escape_string($_POST['log_date']);
    $start_time = $conn->real_escape_string($_POST['start_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $trip_length = floatval($_POST['trip_length']);
    $condition_id = intval($_POST['condition_id']);
    $fuel_id = intval($_POST['fuel_id']);
    $roadcat_id = intval($_POST['roadcat_id']);
    $surface_id = intval($_POST['surface_id']);
    
    // Insert into Drive_Log table
    $sql = "INSERT INTO Drive_Log (Log_Date, Start_Time, End_Time, Trip_Length, 
            Condition_ID, Fuel_ID, RoadCat_ID, Surface_ID) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdiii", $log_date, $start_time, $end_time, $trip_length,
                      $condition_id, $fuel_id, $roadcat_id, $surface_id);
    
    if ($stmt->execute()) {
        $log_id = $conn->insert_id;
        
        // Insert selected actions
        if (!empty($_POST['actions'])) {
            $action_sql = "INSERT INTO Drive_Log_Actions (Log_ID, Action_ID) VALUES (?, ?)";
            $action_stmt = $conn->prepare($action_sql);
            
            foreach ($_POST['actions'] as $action_id) {
                $action_id = intval($action_id);
                $action_stmt->bind_param("ii", $log_id, $action_id);
                $action_stmt->execute();
            }
            $action_stmt->close();
        }
        
        $_SESSION['success'] = "Driving experience saved successfully!";
        header("Location: view_drives.php");
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
        header("Location: add_drive.php");
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: add_drive.php");
}
?>
