<?php
if(isset($_REQUEST["thefilename"])){
    // Get parameters
    $file = urldecode($_REQUEST["thefilename"]); // Decode URL-encoded string
    $filepath = "dbup/" . $file;
    
    // Process download
    if(file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        flush(); // Flush system output buffer
        readfile($filepath);
        exit;
    }
}
?>