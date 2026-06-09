<?php
include "star_connection.php";
$te_master = "te_master";
$gift_master = "gift_master";
$gift_data = array();
$e_points = 0;
$upload_dir = "gift_pic/";
$image_url = $server_url."gift_pic/";
$curr_datetime = date("Y-m-d H:i");
$page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
$categoryId = isset($_POST["category_id"]) ? addslashes(trim($_POST["category_id"])) : "";
$limit = 20;
$start_from = (($page_no-1)*$limit);

$sql2 = "SELECT gift_master.*, categories.name AS category_name FROM $gift_master 
                    JOIN categories ON categories.id = $gift_master.category_id
                    WHERE gift_master.`status`='ACTIVE'";

            if($categoryId != ""){
                $sql2 .= " AND gift_master.category_id='$categoryId'";
            }

$sql2 .= " ORDER BY ABS(`point_require`) ASC LIMIT $start_from,$limit";

$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0)
{
    while($row2 = mysqli_fetch_assoc($res2))
    {
        $gift_id = $row2["id"];
        $gift_title = $row2["gift_title"];
        $categoryId = $row2["category_id"];
        $category_name = $row2["category_name"];
        $gift_description = $row2["description"];
        $gift_image = $row2["gift_image"] ? trim($row2["gift_image"]) : "";
        if($gift_image!='')
        {
            if(file_exists($upload_dir.$gift_image)){
                $gift_image_url = $image_url.$gift_image;
            }
            else
            {
                $gift_image_url ="";
            }
        }
        else
        {
            $gift_image_url ="";
        }
        $point_require = $row2["point_require"] ? trim($row2["point_require"]) : 0;
        $point_require_text = "Redeem ".$point_require." pts";

        $gift_data[] = array("gift_id"=>$gift_id,"gift_title"=>$gift_title, "category_name"=>$category_name, "category_id"=>$categoryId, "gift_description"=>$gift_description,"gift_image_url"=>$gift_image_url,"point_require"=>$point_require,"point_require_text"=>$point_require_text);
    }
    $res_data = array("process_status"=>"YES","process_message"=>"Success.","gift_data"=>$gift_data);
}
else{
    $res_data = array("process_status"=>"NO","process_message"=>"No record found.","gift_data"=>$gift_data);
}
echo json_encode($res_data);
mysqli_close($conn);
?>