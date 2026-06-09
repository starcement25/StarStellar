<?php
include "star_connection.php";

$te_master = "te_master";
$branch_master = "branch_master";
$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";

$branch_list_data = array();
$zone_list_data = array();

if ($te_code != "") {
    $sql = "SELECT `branch_code`, `zone` FROM $te_master WHERE `te_code`='$te_code'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            if (strpos($row['branch_code'], ',') !== false) {
                $BranchList = explode(',', $row['branch_code']);
                foreach ($BranchList as $Brvalue) {
                    $sqlr = "SELECT `branch_name` FROM $branch_master WHERE `branch_code`='$Brvalue'";
                    $resultr = mysqli_query($conn, $sqlr);
                    $rowr = mysqli_fetch_assoc($resultr);

                    $branch_list_data[] = array("br_cod" => $Brvalue, "br_name" => $rowr['branch_name']);
                }
            } else {
                // Use $row['branch_code'] instead of $Brvalue
                $sqlr = "SELECT `branch_name` FROM $branch_master WHERE `branch_code`='$row[branch_code]'";
                $resultr = mysqli_query($conn, $sqlr);
                $rowr = mysqli_fetch_assoc($resultr);

                $branch_list_data[] = array("br_cod" => $row['branch_code'], "br_name" => $rowr['branch_name']);
            }

            if (strpos($row['zone'], ',') !== false) {
                $ZonList = explode(',', $row['zone']);
                foreach ($ZonList as $Znvalue) {
                    $zone_list_data[] = array("zn_cod" => $Znvalue);
                }
            } else {
                $zone_list_data[] = array("zn_cod" => $row['zone']);
            }

            $res_data = array("process_status" => "YES", "process_message" => "The TE CODE exists.", "branch_code" => $branch_list_data, "zone" => $zone_list_data);
        } else {
            $res_data = array("process_status" => "NO", "process_message" => "The TE CODE doesn't exist.", "branch_code" => "", "zone" => "");
        }
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "Error in database query.", "branch_code" => "", "zone" => "");
    }
} else {
    $res_data = array("process_status" => "NO", "process_message" => "Something went wrong.", "branch_code" => "", "zone" => "");
}

echo json_encode($res_data);
mysqli_close($conn);
?>
