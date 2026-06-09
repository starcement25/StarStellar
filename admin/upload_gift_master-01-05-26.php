<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

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
    $imageDir = "../gift_pic/";
    $uploadDir = __DIR__ . '/tmp';
     date_default_timezone_set('Asia/Kolkata');
	$cur_time=date('Y-m-d H:i:s');
	$admin_id = $_SESSION['start_stellar_admin'];
    // Creating directory if not exist
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
        // errorAndExit('Upload error code: ' . $file['error']);
        errorAndExit('Please Upload a CSV File.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if($ext !== 'csv')
    {
        errorAndExit('Only CSV files are allowed.');
    }

    // Saving uploaded file
    $uniqueName = 'csv_' . time() . '_' . mt_rand(1000,9999) . '.csv';
    $destination = $uploadDir . '/' . $uniqueName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        errorAndExit('Failed to upload CSV file.');
    }

    // --- Parsing CSV File---
    $rowCount = 0; $processed = 0; $unprocessed = 0; $unprocessedMessages = array();

    if(($handle = fopen($destination, "r")) !== false) 
    {
        $rowCount++;
        if($rowCount === 1) {
            fgetcsv($handle); // skip header
        }

        while(($data = fgetcsv($handle)) !== false) 
        {
            $rowCount++;

            $gift_title  = isset($data[0]) ? trim($data[0]) : null;
            $description = isset($data[1]) ? trim($data[1]) : null;
            $gift_type_id   = isset($data[2]) ? trim($data[2]) : null;
            $gift_point = isset($data[3]) ? trim($data[3]) : null;
            $image_url = isset($data[4]) ? trim($data[4]) : null;
            $category_id = isset($data[5]) ? trim($data[5]) : null;

            if($gift_title === '')
            {
                array_push($unprocessedMessages, 'Gift Name is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if($description === '')
            {
                array_push($unprocessedMessages, 'Gift Description is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if($category_id === '')
            {
                array_push($unprocessedMessages, 'Category ID is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

             if(!is_numeric($category_id)) {
                array_push($unprocessedMessages, 'Category ID is not in number, in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

             //Validating Category ID
             $categoryQueryByID = "select * from categories where id = $category_id and status = 1";
             $categoryByID = mysqli_query($conn,$categoryQueryByID);
             $totalCategoryByID = mysqli_num_rows($categoryByID);
            if($totalCategoryByID == 0)
            {
                array_push($unprocessedMessages, 'Invalid Category ID in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if($gift_type_id === '')
            {
                array_push($unprocessedMessages, 'Gift Type is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if(!is_numeric($gift_type_id)) {
                array_push($unprocessedMessages, 'Gift Type is not in number, in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            //Validating Gift Type
            $giftTypeQueryByID = "select * from $gift_types where id = $gift_type_id";
            $giftTypeByID = mysqli_query($conn,$giftTypeQueryByID);
            $totalGiftTypeByID = mysqli_num_rows($giftTypeByID);
            if($totalGiftTypeByID == 0)
            {
                array_push($unprocessedMessages, 'Invalid Gift Type in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if($gift_point === '')
            {
                array_push($unprocessedMessages, 'Gift Point is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if (!is_numeric($gift_point)) {
                array_push($unprocessedMessages, 'Gift Point is not in number, in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if($gift_point < 1)
            {
                array_push($unprocessedMessages, 'Gift Point is less than 1 in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if($image_url === '')
            {
                array_push($unprocessedMessages, 'Gift Image URL is empty in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            if(!filter_var($image_url, FILTER_VALIDATE_URL))
            {
                array_push($unprocessedMessages, 'Invalid Gift Image URL in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }

            $duplicateGiftCheckingQuery = "SELECT EXISTS(
                SELECT 1 
                FROM $gift_master 
                WHERE gift_title = ? AND gift_type_id = ? AND category_id = ? AND status = 'ACTIVE'
            ) AS row_exists";

            $stmt = $conn->prepare($duplicateGiftCheckingQuery);
            if ($stmt === false) {
                array_push($unprocessedMessages, 'DB prepare failed (duplicate check) in row ' . $rowCount . ': ' . htmlspecialchars($conn->error) . '.<br>');
                $unprocessed++;
                continue;
            }

            $giftTypeId = (int)$gift_type_id;
            $categoryId = (int)$category_id;

            if (!$stmt->bind_param("sii", $gift_title, $giftTypeId, $categoryId)) { // s = string, i = int
                array_push($unprocessedMessages, 'DB bind failed (duplicate check) in row ' . $rowCount . ': ' . htmlspecialchars($stmt->error) . '.<br>');
                $unprocessed++;
                $stmt->close();
                continue;
            }

            if (!$stmt->execute()) {
                array_push($unprocessedMessages, 'DB execute failed (duplicate check) in row ' . $rowCount . ': ' . htmlspecialchars($stmt->error) . '.<br>');
                $unprocessed++;
                $stmt->close();
                continue;
            }

            $stmt->bind_result($row_exists);
            $stmt->fetch();
            $stmt->close();

            // $result = $stmt->get_result();
            // $duplicateGiftChecking = $result->fetch_assoc();

            if ((int)$row_exists === 1) {
                array_push($unprocessedMessages, 'Gift already exist in row ' . $rowCount . '.<br>');
                $unprocessed++;
                continue;
            }

            $ch = curl_init($image_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_HEADER, false);

            $imgData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

          
           

            if($imgData !== false && $httpCode == 200) {

                // Map MIME → extension
                $mimeToExt = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif',
                    'image/webp' => 'webp'
                ];

                $imgExt = isset($mimeToExt[$contentType]) ? $mimeToExt[$contentType] : 'jpg';
                $imgFile = 'gift_img_' . time() . '_' . mt_rand(1000,9999) . '.' . $imgExt;
                $imgPath = $imageDir . '/' . $imgFile;

                if(file_put_contents($imgPath, $imgData)) {
                    $localImagePath = $imgFile; // relative path
                    $insertQueryForCreatingNewGift = "INSERT INTO $gift_master 
                        (`gift_title`, `gift_type_id`, `category_id`, `description`, `gift_image`, `point_require`, `last_updated_at`, `last_updated_by`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                        $stmt = $conn->prepare($insertQueryForCreatingNewGift);
                      

                        $giftPoint = (int)$gift_point;
                        $adminId = (int)$admin_id;

                        if (!$stmt->bind_param("siissisi", $gift_title, $giftTypeId, $categoryId, $description, $localImagePath, $giftPoint, $cur_time, $adminId)) {
                            array_push($unprocessedMessages, 'DB bind failed (insert) in row ' . $rowCount . ': ' . htmlspecialchars($stmt->error) . '.<br>');
                            $unprocessed++;
                            $stmt->close();
                            continue;
                        }

                        if (!$stmt->execute()) {
                            array_push($unprocessedMessages, 'DB execute failed (insert) in row ' . $rowCount . ': ' . htmlspecialchars($stmt->error) . '.<br>');
                            $unprocessed++;
                            $stmt->close();
                            continue;
                        }

                        $stmt->close();
                    // echo $insertQueryForCreatingNewGift; die;
                    $processed++;
                    continue;
                }
                else
                {
                    array_push($unprocessedMessages, 'Failed to upload Image, in row '.$rowCount.'.<br>');
                    $unprocessed++;
                    continue;
                }
            }
            else
            {
                array_push($unprocessedMessages, 'Failed to dwnload Image from the URL, in row '.$rowCount.'.<br>');
                $unprocessed++;
                continue;
            }
        }
        $uploaded_response = '<b>Upload Completed, '.$rowCount.' rows scanned, '.$processed.' rows processed and '.$unprocessed.' rows unprocessed.</b>';
        if($unprocessed > 0)
        {
            $uploaded_response .= ' <p style="color:red;">Error Details : <br> ' . implode('',$unprocessedMessages) . '</p>';
        }
        $_SESSION['uploaded_response'] = $uploaded_response;
        header("Location: add_gift_master.php");
        exit;
    }
    errorAndExit('CSV File Reading Failed.');
?>
