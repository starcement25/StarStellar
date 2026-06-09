<?php
echo "Service is block now";
exit;
error_reporting(E_ALL);           // Report all types of errors
ini_set('display_errors', 1);     // Show errors on screen
ini_set('display_startup_errors', 1);
    include "web_check.php";
	include "star_connection.php";
    session_start();

    $ledger_master = "ledger_master";
    $engineer_master = "engineer_master";
    $uploadDir = __DIR__ . '/tmp';

    // Creating directory if not exist
    if(!is_dir($uploadDir))
    {
        mkdir($uploadDir, 0755, true);
    }

    
    function errorAndExit($msg) {
        echo '<p style="color:red;">Error: ' . htmlspecialchars($msg) . '</p>';
        exit;
    }

    // --- Parsing CSV File---
    $rowCount = 0; $processed = 0; $unprocessed = 0; $unprocessedMessages = array();
    $destination = $uploadDir . '/Updating_Engineer_Master_19th_Mar_2024_06_03_17_PM_ POINTS.csv';
    if(($handle = fopen($destination, "r")) !== false) 
    {
        $rowCount++;
        if($rowCount === 1) {
            fgetcsv($handle); // skip header
        }

        while(($data = fgetcsv($handle)) !== false) 
        {
            $rowCount++;

            $emgineer_mobile_number  = isset($data[1]) ? trim($data[1]) : null;
            $point_to_added = isset($data[7]) ? trim($data[7]) : null;

            if($emgineer_mobile_number === '')
            {
                array_push($unprocessedMessages, 'Engineer Mobile Number is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            //Validating Gift Type
            $engineerQuery = "select * from $engineer_master where e_mobile = '$emgineer_mobile_number'";
            
            $engineer = mysqli_query($conn,$engineerQuery);
            $row = mysqli_fetch_assoc($engineer);
            $totalEngineer = mysqli_num_rows($engineer);
            if($totalEngineer == 0)
            {
                array_push($unprocessedMessages, 'Engineer not found where phone number is '.$emgineer_mobile_number.', in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if($point_to_added === '')
            {
                array_push($unprocessedMessages, 'Point is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if (!is_numeric($point_to_added)) {
                array_push($unprocessedMessages, 'Gift Point is not in number, in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }
            $ldgr_type = "SOP_POINT";
            $description = "Correction in point calculation as per new SOP";
            $ldgr_datetime = "2024-03-19 10:00:00";
            $insertQueryForCreatingNewPoint = "INSERT INTO $ledger_master 
                        (user_id, ldgr_type, description, point_earned, ldgr_datetime) 
                        VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($insertQueryForCreatingNewPoint);
            $stmt->bind_param("issss", $row['eid'], $ldgr_type, $description, $point_to_added, $ldgr_datetime); 
            // s = string, i = int

            $stmt->execute();
            // echo "<br>".($row['eid'])."<br>";
            $processed++;
        }
        $uploaded_response = '<b>Upload Completed, '.$rowCount.' rows scanned, '.$processed.' rows processed and '.$unprocessed.' rows unprocessed.</b>';
        if($unprocessed > 0)
        {
            $uploaded_response .= ' <p style="color:red;">Error Details : <br> ' . implode('',$unprocessedMessages) . '</p>';
        }
        echo $uploaded_response;
    }
    errorAndExit('CSV File Reading Failed.');
?>