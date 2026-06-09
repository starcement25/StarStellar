<?php
    include "web_check.php";
    include "star_connection.php";
    session_start();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])
    {
        http_response_code(400);
        die("Bad Request.");
    }

    $gift_master = "gift_master";
    $uploadDir = __DIR__ . '/tmp';
    date_default_timezone_set('Asia/Kolkata');
    $cur_time = date('Y-m-d H:i:s');
    $admin_id = $_SESSION['start_stellar_admin'];
    
    // Creating directory if not exist
    if(!is_dir($uploadDir))
    {
        mkdir($uploadDir, 0755, true);
    }

    function errorAndExit($msg) {
        $_SESSION['errors'] = '<p style="color:red;">Error: ' . htmlspecialchars($msg) . '</p>';
        header("Location: update_gift_master.php");
        exit;
    }

    if (!isset($_FILES['csv_file']))
    {
        errorAndExit('No file uploaded.');
    }
    $file = $_FILES['csv_file'];

    if($file['error'] !== UPLOAD_ERR_OK)
    {
        errorAndExit('Please Upload a CSV File.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if($ext !== 'csv')
    {
        errorAndExit('Only CSV files are allowed.');
    }

    // Saving uploaded file
    $uniqueName = 'csv_edit_' . time() . '_' . mt_rand(1000,9999) . '.csv';
    $destination = $uploadDir . '/' . $uniqueName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        errorAndExit('Failed to upload CSV file.');
    }

    // --- Parsing CSV File---
    $rowCount = 0; 
    $processed = 0; 
    $unprocessed = 0; 
    $unprocessedMessages = array();

    if(($handle = fopen($destination, "r")) !== false) 
    {
        // Skip header row
        $header = fgetcsv($handle);
        $rowCount++;

        while(($data = fgetcsv($handle)) !== false) 
        {
            $rowCount++;

            $gift_id = isset($data[0]) ? trim($data[0]) : null;
            $gift_title = isset($data[1]) ? trim($data[1]) : null;
            $gift_point = isset($data[2]) ? trim($data[2]) : null;
            $status = isset($data[3]) ? trim($data[3]) : null;

            // Validation: Gift ID
            if($gift_id === '' || $gift_id === null)
            {
                array_push($unprocessedMessages, 'Gift ID is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if(!is_numeric($gift_id)) {
                array_push($unprocessedMessages, 'Gift ID is not a number in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            // Check if gift exists
            $checkGiftQuery = "SELECT id FROM $gift_master WHERE id = ?";
            $stmt = $conn->prepare($checkGiftQuery);
            $stmt->bind_param("i", $gift_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows === 0)
            {
                array_push($unprocessedMessages, 'Gift ID '.$gift_id.' does not exist in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            // Validation: Gift Title
            if($gift_title === '' || $gift_title === null)
            {
                array_push($unprocessedMessages, 'Gift Title is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            // Validation: Gift Point
            if($gift_point === '' || $gift_point === null)
            {
                array_push($unprocessedMessages, 'Gift Point is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if (!is_numeric($gift_point)) {
                array_push($unprocessedMessages, 'Gift Point is not a number in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if($gift_point < 1)
            {
                array_push($unprocessedMessages, 'Gift Point must be at least 1 in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            // Validation: Status
            if($status === '' || $status === null)
            {
                array_push($unprocessedMessages, 'Status is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            $status = strtoupper($status);
            $validStatuses = ['ACTIVE', 'INACTIVE'];
            if(!in_array($status, $validStatuses))
            {
                array_push($unprocessedMessages, 'Invalid Status in row '.$rowCount.'. Must be ACTIVE or INACTIVE.<br>');
                $unprocessed++;
                continue;
            }

            // Check for duplicate gift title (excluding current gift)
            // Only check if status is ACTIVE
            if($status === 'ACTIVE') {
                $duplicateCheckQuery = "SELECT id FROM $gift_master 
                                       WHERE gift_title = ? AND id != ? AND status = 'ACTIVE'";
                $stmt = $conn->prepare($duplicateCheckQuery);
                $stmt->bind_param("si", $gift_title, $gift_id);
                $stmt->execute();
                $duplicateResult = $stmt->get_result();

                if($duplicateResult->num_rows > 0)
                {
                    array_push($unprocessedMessages, 'Gift title "'.$gift_title.'" already exists for another active gift in row '.$rowCount.'.<br>');
                    $unprocessed++;
                    continue;
                }
            }

            // Update the gift
            $updateQuery = "UPDATE $gift_master 
                           SET point_require = ?, 
                               status = ?,
                               last_updated_at = ?
                             
                           WHERE id = ?";
            
            $stmt = $conn->prepare($updateQuery);
            if(!$stmt) {
                array_push($unprocessedMessages, 'Database prepare error for gift ID '.$gift_id.' in row '.$rowCount.': '.mysqli_error($conn).'<br>');
                $unprocessed++;
                continue;
            }
            
            $stmt->bind_param("issi", $gift_point, $status, $cur_time,  $gift_id);
            
            if($stmt->execute())
            {
                if($stmt->affected_rows > 0) {
                    $processed++;
                } else {
                    array_push($unprocessedMessages, 'No changes made for gift ID '.$gift_id.' in row '.$rowCount.' (data might be identical).<br>');
                    $unprocessed++;
                }
            }
            else
            {
                array_push($unprocessedMessages, 'Failed to update gift ID '.$gift_id.' in row '.$rowCount.': '.$stmt->error.'<br>');
                $unprocessed++;
            }
        }
        
        fclose($handle);
        
        // Delete the uploaded CSV file after processing
        if(file_exists($destination)) {
            unlink($destination);
        }

        $uploaded_response = '<b>Update Completed: '.$rowCount.' rows scanned, '.$processed.' rows updated, '.$unprocessed.' rows failed.</b>';
        if($unprocessed > 0)
        {
            $uploaded_response .= ' <p style="color:red;">Error Details:<br>' . implode('', $unprocessedMessages) . '</p>';
        }
        $_SESSION['uploaded_response'] = $uploaded_response;
        header("Location: update_gift_master.php");
        exit;
    }
    
    errorAndExit('CSV File Reading Failed.');
?>