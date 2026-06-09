<?php
include "star_connection.php";
$contact_person_category_master = "contact_person_category_master";
$contact_person_category_data = array();
$sql3 = "select `category_name` from $contact_person_category_master order by `cpcm_id` asc";
	$res3 = mysqli_query($conn,$sql3);
	$tot_res3 = mysqli_num_rows($res3);
	if($tot_res3>0){
	while($row3 = mysqli_fetch_assoc($res3)){
	$category_name = $row3["category_name"] ? trim($row3["category_name"]) : "";
		if($category_name!=""){
			$contact_person_category_data[] = $category_name;
		}
	}
	if(count($contact_person_category_data)>0){
	$res_data = array("process_status"=>"YES","process_message"=>"Success","contact_person_category_data"=>$contact_person_category_data);
	}else{
	$res_data = array("process_status"=>"NO","process_message"=>"No category found.");
	}
	
	}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"No category found.");
	}
echo json_encode($res_data);
mysqli_close($conn);
?>