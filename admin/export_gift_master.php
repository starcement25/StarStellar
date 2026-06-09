<?php
    ini_set('memory_limit', '999M');
    set_time_limit(0);
    include "star_connection.php";
    startCreatGiftMasterCsvfile($conn);

    function startCreatGiftMasterCsvfile($conn)
    {
        $server_url = "https://" . $_SERVER['SERVER_NAME']."/";
        $img_dir = "../gift_pic/";
        $image_url_prefix = $server_url."gift_pic/";
        $gift_master = "gift_master";
        $gift_types = "gift_types";
        $curr_date = date("jS_M_Y_h_m_s_A");
        $the_file_name = "gift_master_".$curr_date.".csv";
        $output = "";
        $qry = "select $gift_master.*, $gift_types.name AS gift_type_name from $gift_master JOIN $gift_types ON $gift_types.id = $gift_master.gift_type_id WHERE $gift_master.is_deleted='no' order by ABS(`point_require`) asc";
        $sql = mysqli_query($conn,$qry);

        $output .= '"Gift Image","Title","Description","Gift Type","Point","Status","Featured"';

        $output .="\n";
        // Get Records from the table

        while ($row1 = mysqli_fetch_assoc($sql)) 
        {
            $the_gift_title = $row1["gift_title"] ? str_replace('"', '""', trim($row1["gift_title"])) : "";
            $the_gift_description = $row1["description"] ? str_replace('"', '""', trim($row1["description"])) : "";
            $the_gift_type_name = $row1["gift_type_name"] ? str_replace('"', '""', trim($row1["gift_type_name"])) : "";
            $the_gift_point_require = $row1["point_require"] ? trim($row1["point_require"]) : "";
            $the_gift_image = $row1["gift_image"] ? trim($row1["gift_image"]) : "";
            $the_gift_status = $row1["status"]? trim($row1["status"]) : "";
            $the_featured_status = $row1["featured"]? trim($row1["featured"]) : "";
            if($the_gift_image!="")
            {
                if(file_exists($img_dir.$the_gift_image))
                {
                    $the_gift_image_url = $image_url_prefix.$the_gift_image;
                }
                else
                {
                    $the_gift_image_url = "";
                }
            }
            else
            {
                $the_gift_image_url = "";
            }

            $output .= '"'.$the_gift_image_url.'","'.$the_gift_title.'","'.$the_gift_description.'","'.$the_gift_type_name.'","'.$the_gift_point_require.'","'.$the_gift_status.'","'.$the_featured_status.'"';
            $output .="\n";
        }

        // Download the file

        $filename = $the_file_name;
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename='.$filename);
        header('Pragma: no-cache');    
        header('Expires: 0');
        echo $output;
        exit;
    }

    mysqli_close($conn);
?>