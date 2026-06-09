<?php
    include "web_check.php";
    include "star_connection.php";
    session_start();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'])
    {
        http_response_code(400);
        die("Bad Request.");
    }

    $imageDir = realpath(__DIR__ . '/../gift_pic');
    $uploadDir = __DIR__ . '/tmp';
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

    function errorAndExit($msg) {
        $_SESSION['errors'] = '<p style="color:red;">Error: ' . htmlspecialchars($msg) . '</p>';
        header("Location: add_gift_master.php");
        exit;
    }

    if ($imageDir === false)
    {
        errorAndExit('Image directory not found.');
    }

    if(!is_dir($uploadDir))
    {
        mkdir($uploadDir, 0755, true);
    }

    if (!isset($_FILES['zip_file']))
    {
        errorAndExit('No ZIP file uploaded.');
    }

    $file = $_FILES['zip_file'];

    if($file['error'] !== UPLOAD_ERR_OK)
    {
        errorAndExit('Please upload a ZIP file.');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if($ext !== 'zip')
    {
        errorAndExit('Only ZIP files are allowed.');
    }

    $uniqueName = 'gift_images_' . time() . '_' . mt_rand(1000,9999) . '.zip';
    $destination = $uploadDir . '/' . $uniqueName;
    if (!move_uploaded_file($file['tmp_name'], $destination))
    {
        errorAndExit('Failed to upload ZIP file.');
    }

    $zip = new ZipArchive();
    if ($zip->open($destination) !== true)
    {
        @unlink($destination);
        errorAndExit('Unable to open ZIP file.');
    }

    $totalFiles = 0;
    $uploadedFiles = 0;
    $skippedFiles = 0;
    $messages = array();

    for ($i = 0; $i < $zip->numFiles; $i++)
    {
        $entryName = $zip->getNameIndex($i);
        if ($entryName === false)
        {
            continue;
        }

        if (substr($entryName, -1) === '/')
        {
            continue;
        }

        $totalFiles++;
        $baseName = basename(str_replace('\\', '/', $entryName));
        if ($baseName === '')
        {
            $skippedFiles++;
            $messages[] = 'Skipped invalid file name inside ZIP.<br>';
            continue;
        }

        $fileExtension = strtolower(pathinfo($baseName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions))
        {
            $skippedFiles++;
            $messages[] = 'Skipped unsupported file: ' . htmlspecialchars($baseName) . '.<br>';
            continue;
        }

        $stream = $zip->getStream($entryName);
        if (!$stream)
        {
            $skippedFiles++;
            $messages[] = 'Failed to read file from ZIP: ' . htmlspecialchars($baseName) . '.<br>';
            continue;
        }

        $targetPath = $imageDir . DIRECTORY_SEPARATOR . $baseName;
        $targetHandle = fopen($targetPath, 'wb');
        if (!$targetHandle)
        {
            fclose($stream);
            $skippedFiles++;
            $messages[] = 'Failed to save image: ' . htmlspecialchars($baseName) . '.<br>';
            continue;
        }

        while (!feof($stream))
        {
            fwrite($targetHandle, fread($stream, 8192));
        }

        fclose($targetHandle);
        fclose($stream);
        $uploadedFiles++;
    }

    $zip->close();
    @unlink($destination);

    $_SESSION['uploaded_response'] = '<b>ZIP Upload Completed, ' . $totalFiles . ' files scanned, ' . $uploadedFiles . ' images uploaded and ' . $skippedFiles . ' files skipped.</b>';
    if($skippedFiles > 0)
    {
        $_SESSION['uploaded_response'] .= ' <p style="color:red;">Details : <br>' . implode('', $messages) . '</p>';
    }

    header("Location: add_gift_master.php");
    exit;
?>
