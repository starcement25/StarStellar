<?php
    include "web_check.php";
    include "star_connection.php";
    session_start();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])
    {
        http_response_code(400);
        die("Bad Request.");
    }

    $gift_types = "gift_types";
    $gift_master = "gift_master";
    $uploadDir = __DIR__ . '/tmp';
    date_default_timezone_set('Asia/Kolkata');
    $cur_time = date('Y-m-d H:i:s');
    $admin_id = $_SESSION['start_stellar_admin'];

    if(!is_dir($uploadDir))
    {
        mkdir($uploadDir, 0755, true);
    }

    function errorAndExit($msg) {
        $_SESSION['errors'] = '<p style="color:red;">Error: ' . htmlspecialchars($msg) . '</p>';
        header("Location: add_gift_master.php");
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

    $uniqueName = 'csv_' . time() . '_' . mt_rand(1000,9999) . '.csv';
    $destination = $uploadDir . '/' . $uniqueName;
    if (!move_uploaded_file($file['tmp_name'], $destination))
    {
        errorAndExit('Failed to upload CSV file.');
    }

    $rowCount = 0;
    $processed = 0;
    $unprocessed = 0;
    $unprocessedMessages = array();

    if(($handle = fopen($destination, "r")) !== false)
    {
        $rowCount++;
        if($rowCount === 1)
        {
            fgetcsv($handle);
        }

        while(($data = fgetcsv($handle)) !== false)
        {
            $rowCount++;

            $gift_title = isset($data[0]) ? trim($data[0]) : null;
            $description = isset($data[1]) ? trim($data[1]) : null;
            $gift_type_id = isset($data[2]) ? trim($data[2]) : null;
            $gift_point = isset($data[3]) ? trim($data[3]) : null;
            $image_name = isset($data[4]) ? trim($data[4]) : null;

            if($gift_title === '')
            {
                $unprocessedMessages[] = 'Gift Name is empty in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            if($description === '')
            {
                $unprocessedMessages[] = 'Gift Description is empty in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            if($gift_type_id === '')
            {
                $unprocessedMessages[] = 'Gift Type is empty in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            if(!is_numeric($gift_type_id))
            {
                $unprocessedMessages[] = 'Gift Type is not in number, in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            $giftTypeQueryByID = "select * from $gift_types where id = $gift_type_id";
            $giftTypeByID = mysqli_query($conn,$giftTypeQueryByID);
            $totalGiftTypeByID = mysqli_num_rows($giftTypeByID);
            if($totalGiftTypeByID == 0)
            {
                $unprocessedMessages[] = 'Invalid Gift Type in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            if($gift_point === '')
            {
                $unprocessedMessages[] = 'Gift Point is empty in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            if (!is_numeric($gift_point))
            {
                $unprocessedMessages[] = 'Gift Point is not in number, in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            if($gift_point < 1)
            {
                $unprocessedMessages[] = 'Gift Point is less than 1 in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            if($image_name === '')
            {
                $unprocessedMessages[] = 'Gift Image Name is empty in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            if($image_name !== basename($image_name))
            {
                $unprocessedMessages[] = 'Invalid Gift Image Name in row '.$rowCount.'.<br>';
                $unprocessed++;
                continue;
            }

            $duplicateGiftCheckingQuery = "SELECT EXISTS(
                SELECT 1
                FROM $gift_master
                WHERE gift_title = ? AND gift_type_id = ? AND status = 'ACTIVE'
            ) AS row_exists";

            $stmt = $conn->prepare($duplicateGiftCheckingQuery);
            $stmt->bind_param("si", $gift_title, $gift_type_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $duplicateGiftChecking = $result->fetch_assoc();
            $stmt->close();

            if ((int)$duplicateGiftChecking['row_exists'] === 1)
            {
                $unprocessedMessages[] = 'Gift already exist in row ' . $rowCount . '.<br>';
                $unprocessed++;
                continue;
            }

            $insertQueryForCreatingNewGift = "INSERT INTO $gift_master
                (gift_title, gift_type_id, description, gift_image, point_require, last_updated_at, last_updated_by)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($insertQueryForCreatingNewGift);
            $stmt->bind_param("sisssss", $gift_title, $gift_type_id, $description, $image_name, $gift_point, $cur_time, $admin_id);

            if($stmt->execute())
            {
                $processed++;
            }
            else
            {
                $unprocessedMessages[] = 'Failed to save gift in row '.$rowCount.'.<br>';
                $unprocessed++;
            }

            $stmt->close();
        }

        fclose($handle);
        @unlink($destination);

        $uploaded_response = '<b>Upload Completed, '.$rowCount.' rows scanned, '.$processed.' rows processed and '.$unprocessed.' rows unprocessed.</b>';
        if($unprocessed > 0)
        {
            $uploaded_response .= ' <p style="color:red;">Error Details : <br> ' . implode('', $unprocessedMessages) . '</p>';
        }

        $_SESSION['uploaded_response'] = $uploaded_response;
        header("Location: add_gift_master.php");
        exit;
    }

    @unlink($destination);
    errorAndExit('CSV File Reading Failed.');
?>
