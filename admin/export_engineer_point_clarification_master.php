<?php
    session_start();
    include "web_check.php";
    ini_set('memory_limit', '999M');
    set_time_limit(0);
    include "star_connection.php";

    $engineer_master = "engineer_master";
    $ledger_master = "ledger_master";

    $curr_date = date("jS_M_Y_h_m_s_A");
    $output = "";

    startCreatEngineerMasterCsvfile($conn);


    function startCreatEngineerMasterCsvfile($conn)
    {
        $the_file_name = "engineer_master_".$curr_date.".csv";
        $engineer_master = "engineer_master";
        $ledger_master = "ledger_master";

        $qry = "
            SELECT 
                em.e_name,
                em.e_mobile,
                em.e_email,
                em.e_points,
                em.status,
                IFNULL(SUM(lm.point_earned), 0) AS total_earned,
                IFNULL(SUM(lm.point_redeem), 0) AS total_redeem
            FROM engineer_master AS em
            LEFT JOIN ledger_master AS lm 
                ON em.eid = lm.user_id
            GROUP BY em.eid
            ORDER BY em.reg_date DESC
        ";

        // echo $qry;
        // exit;
        
        $sql = mysqli_query($conn,$qry);

        $output .= '"Name","Mobile","Email","Status","Point Earned","Point Redeem","Current Point"';

        $output .="\n";
        // Get Records from the table

        while ($row1 = mysqli_fetch_assoc($sql))
        {
            $the_e_name = $row1["e_name"] ? str_replace('"', '""', trim($row1["e_name"])) : "";
            $the_e_mobile = $row1["e_mobile"];
            $the_e_email = $row1["e_email"];

            $the_e_status = $row1["status"] ? trim($row1["status"]) : "";

            $the_e_points = $row1["e_points"] ? trim($row1["e_points"]) : "";
            $the_ledger_redeem_points = $row1["total_redeem"] ? trim($row1["total_redeem"]) : "";
            $the_ledger_earned_points = $row1["total_earned"] ? trim($row1["total_earned"]) : "";
            
            $output .= '"'.$the_e_name.'","'.$the_e_mobile.'","'.$the_e_email.'","'.$the_e_status.'","'.$the_ledger_earned_points.'","'.$the_ledger_redeem_points.'","'.$the_e_points.'"';
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