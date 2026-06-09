<?php
include "web_check.php";
include "config_setup_acedns.php";
include "star_connection.php";
include "pclzip.lib.php";
$db_name1 = "starsaat_acednsproduct";
$db_name2 = "starsaat_START";
$table_name = "employee_master";
$changepassword = "changepassword";
function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
function modifyempdatadownloadlog($conn,$emp_code,$nick_name)
	{
		if($emp_code=='')
		{
			$sqlallemp="SELECT emp_code FROM employee_master WHERE acedns <> 'N'";
			$rsallemp=mysqli_query($conn,$sqlallemp);
			while($rowallemp=mysqli_fetch_array($rsallemp))
			{
				$emp_code_all=$rowallemp['emp_code'];
				$sqlemplogchk="SELECT emp_code,is_download FROM emp_data_download_log WHERE emp_code='".$emp_code_all."'";
				$rsemplogchk=mysqli_query($conn,$sqlemplogchk);
				$countemplogchk=mysqli_num_rows($rsemplogchk);
				if($countemplogchk<1)
				{
					$sqlinsertemplog  = "INSERT INTO emp_data_download_log SET ";
					$sqlinsertemplog .= "  	emp_code='".mysqli_real_escape_string($emp_code_all)."'";
					$sqlinsertemplog .= " , is_download='yes'";
					$sqlinsertemplog .= " , is_download_time=CURRENT_TIMESTAMP()";
					mysqli_query($conn,$sqlinsertemplog);
				}
				else
				{
					$rowemplogchk=mysqli_fetch_array($rsemplogchk);
					$is_download=$rowemplogchk['is_download'];
					if($is_download=='no')
					{
						$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='yes',
										is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code_all."'";
						mysqli_query($conn,$sqlupdateemplog);	
					}
				}
			}
		}
		else
		{
			$sqlemplogchk="SELECT emp_code,is_download FROM emp_data_download_log WHERE emp_code='".$emp_code."'";
			$rsemplogchk=mysqli_query($conn,$sqlemplogchk);
			$countemplogchk=mysqli_num_rows($rsemplogchk);
			if($countemplogchk<1)
			{
				$sqlinsertemplog  = "INSERT INTO emp_data_download_log SET ";
				$sqlinsertemplog .= "  	emp_code='".mysqli_real_escape_string($emp_code)."'";
				$sqlinsertemplog .= " , is_download='yes'";
				$sqlinsertemplog .= " , is_download_time=CURRENT_TIMESTAMP()";
				mysqli_query($conn,$sqlinsertemplog);
			}
			else
			{
				$rowemplogchk=mysqli_fetch_array($rsemplogchk);
				$is_download=$rowemplogchk['is_download'];
				if($is_download=='no')
				{
					$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='yes',
									is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
					mysqli_query($conn,$sqlupdateemplog);	
				}
			}
		}
	}
$submsg = "";
if(@$_POST["upload"]=="Upload"){
$zip_file_name = $_FILES["zip_file"]["name"];
$zip_file_type = $_FILES["zip_file"]["type"];
$zip_file_size = $_FILES["zip_file"]["size"];
$zip_file_tmp = $_FILES["zip_file"]["tmp_name"];
$nick_name = "START";

if($zip_file_name!=""){
//For Unzip a zip file
	$nick_name = strtoupper($nick_name);
	$folderName = strtoupper($nick_name);
	$error_array=array();
	if (!file_exists("../csv")){
		mkdir("../csv");
		chmod("../csv", 0777);
	}
	if ( !file_exists("../csv/$folderName")){
		mkdir("../csv/$folderName");
		chmod("../csv/$folderName", 0777);
	}
	if ( !file_exists("../csv/$folderName/filebkup/")){
		mkdir("../csv/$folderName/filebkup/");
		chmod("../csv/$folderName/filebkup/", 0777);
	}
	// Get array of all source files
		$files = scandir("../csv/$folderName");
		// Identify directories
		$source = "../csv/$folderName/";
		$destination = "../csv/$folderName/filebkup/";
		// Cycle through all source files
		foreach ($files as $file) {
		  if (in_array($file, array(".",".."))) continue;
		  // If we copied this successfully, mark it for deletion
		  if (@copy($source.$file, $destination.$file)) {
			$delete[] = $source.$file;
		  }
		}
		// Delete all successfully-copied files
		foreach ($delete as $file) {
		  unlink($file);
		}
		
	$upload_dir="../csv/$folderName/";
	$upload_file = $upload_dir.$zip_file_name;
	
	$thezipupload = move_uploaded_file($zip_file_tmp,$upload_file);
	if($thezipupload){
	$zipfile = new PclZip($upload_file);
	$zipresfun = $zipfile->extract($upload_dir);
	if($zipresfun!= 0){
		/*---------------------------------CODE START--------------------------------------------*/


	//For Branch Master CSV

	if(similar_file_exists("../csv/$folderName/Branch master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Branch master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate branch_master";

		$rsdelete=mysqli_query($conn,$sqldelete);*/

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_branch_code=trim($data[0]);

				$branch_name=trim($data[1]);

				$branch_location=trim($data[2]);

				$comp_code=trim($data[3]);

				$branch_state=trim($data[4]);

				$branch_email_id=trim($data[5]);

				$branch_accounts_email_id=trim($data[6]);

				$alternative_email_id=trim($data[7]);

				if(providing_code=='yes'){

					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";

				}

				else

				{

					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_name."'";

				}

				/*$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."' 

									AND branch_location='".$branch_location."'";*/

				$rsbranchnamechk=mysqli_query($conn,$sqlbranchnamechk);

				$countbranchnamechk=mysqli_num_rows($rsbranchnamechk);

				

				$csv_row_count=$rec_count+1;

				if($countbranchnamechk<1)

				{

					$sqlmaxbranchcode="SELECT MAX(branch_code) AS max_branch_code FROM  branch_master WHERE 1";

					$rsmaxbranchcode=mysqli_query($conn,$sqlmaxbranchcode);

					$rowmaxbranchcode=mysqli_fetch_array($rsmaxbranchcode);

					$max_branch_code=$rowmaxbranchcode['max_branch_code'];

					

					if($max_branch_code=='')

					{

						$max_branch_code='B0001';

					}

					else

					{

						$max_branch_code++;

					}

				

					$sqlbranch  = "insert into branch_master SET ";

					$sqlbranch .= "  	branch_code='".mysqli_real_escape_string($max_branch_code)."'";

					$sqlbranch .= " , dns_branch_code='".mysqli_real_escape_string($dns_branch_code)."'";

					$sqlbranch .= " , branch_name='".mysqli_real_escape_string($branch_name)."'";

					$sqlbranch .= " , branch_state='".mysqli_real_escape_string($branch_state)."'";

					$sqlbranch .= " , branch_location='".mysqli_real_escape_string($branch_location)."'";

					$sqlbranch .= " , comp_code='".mysqli_real_escape_string($comp_code)."'";

					$sqlbranch .= " , branch_email_id='".mysqli_real_escape_string($branch_email_id)."'";

					$sqlbranch .= " , alternative_email_id='".mysqli_real_escape_string($alternative_email_id)."'";

					$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";

				}

				else

				{

					$rowbranchnamechk=mysqli_fetch_array($rsbranchnamechk);

					$branch_code=$rowbranchnamechk['branch_code'];



					$sqlbranch  = "UPDATE branch_master SET ";

					$sqlbranch .= "  	dns_branch_code='".mysqli_real_escape_string($dns_branch_code)."'";

					$sqlbranch .= " , branch_location='".mysqli_real_escape_string($branch_location)."'";

					$sqlbranch .= " , branch_name='".mysqli_real_escape_string($branch_name)."'";

					$sqlbranch .= " , branch_state='".mysqli_real_escape_string($branch_state)."'";

					$sqlbranch .= " , comp_code='".mysqli_real_escape_string($comp_code)."'";

					$sqlbranch .= " , branch_email_id='".mysqli_real_escape_string($branch_email_id)."'";

					$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";

					$sqlbranch .= " , alternative_email_id='".mysqli_real_escape_string($alternative_email_id)."' 

									WHERE branch_code='".$branch_code."'";

				}

				mysqli_query($conn,$sqlbranch) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Branch master.csv.Please check.");

			}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Branch master.csv is wrong.";

		exit();

	}*/



	//For Category Master CSV

	if(similar_file_exists("../csv/$folderName/Brand Master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Brand Master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

			$lines = file($filename);

			/*$sqldelete="truncate product_group_master";

			$rsdelete=mysqli_query($conn,$sqldelete);*/

	

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

						

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

					

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   $data[]=$value;

				  //print_r($data);

				  	$dns_product_group_code=trim($data[0]);

					$product_group_code=trim($data[1]);

					$product_group_name=trim($data[1]);

					$vertical_value=trim($data[2]);

					

					$csv_row_count=$rec_count+1;

					$sqlprodgroupnamechk="SELECT product_group_name FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";

					$rsprodgroupnamechk=mysqli_query($conn,$sqlprodgroupnamechk);

					$countprodgroupnamechk=mysqli_num_rows($rsprodgroupnamechk);

					if($countprodgroupnamechk<1){

						$sqlbrand  = "insert into product_group_master SET ";

						$sqlbrand .= "  product_group_code='".mysqli_real_escape_string($product_group_code)."'";

						$sqlbrand .= " ,dns_product_group_code='".mysqli_real_escape_string($dns_product_group_code)."'";

						$sqlbrand .= " , product_group_name='".mysqli_real_escape_string($product_group_name)."'";

						$sqlbrand .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";

						$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlbrand) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand name column in Brand Master.csv.Please check.");

					}

				}

				 $rec_count++;

			}		

			$successval=1;

		}

		

	//For Sub Category Master CSV

	if(similar_file_exists("../csv/$folderName/Brand Form Master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Brand Form Master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

			$lines = file($filename);

			/*$sqldelete="truncate product_sub_group_master";

			$rsdelete=mysqli_query($conn,$sqldelete);*/

	

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

						

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

					

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   $data[]=$value;

				  //print_r($data);

				  	$dns_product_sub_group_code=trim($data[0]);

					$product_sub_group_code=trim($data[1]);

					$product_sub_group_name=trim($data[1]);

					$product_group_code=trim($data[2]);

					$vertical_value=trim($data[3]);

					

					$csv_row_count=$rec_count+1;

					$sqlprodsubgroupnamechk="SELECT product_sub_group_name,product_group_code FROM product_sub_group_master 

											WHERE product_sub_group_name='".addslashes($product_sub_group_name)."' AND product_group_code='".$product_group_code."'";

					$rsprodsubgroupnamechk=mysqli_query($conn,$sqlprodsubgroupnamechk);

					$countprodsubgroupnamechk=mysqli_num_rows($rsprodsubgroupnamechk);

					

					if($countprodsubgroupnamechk<1){

						$sqlbrandform  = "insert into product_sub_group_master SET ";

						$sqlbrandform .= "  product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";

						$sqlbrandform .= " ,dns_product_sub_group_code='".mysqli_real_escape_string($dns_product_sub_group_code)."'";

						$sqlbrandform .= " , product_sub_group_name='".mysqli_real_escape_string($product_sub_group_name)."'";

						$sqlbrandform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

						$sqlbrandform .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";

						$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlbrandform) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand code and Brand form code columns in Brand Form Master.csv.Please check.");

					}

					else

					{

						$rowprodsubgroupnamechk=mysqli_fetch_array($rsprodsubgroupnamechk);

						$product_group_name_existing=$rowprodsubgroupnamechk['product_group_code'];

						$vertical_value_existing=$rowprodsubgroupnamechk['vertical_value'];

						if($product_group_name_existing!=$product_group_name || $vertical_value_existing!=$vertical_value)

						{

							$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";

							$sqlupdatebrandform .= " product_group_code='".mysqli_real_escape_string($product_group_code)."'";

							$sqlupdatebrandform .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";

							$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP()";

							$sqlupdatebrandform .= "  WHERE product_sub_group_name='".addslashes($product_sub_group_name)."'";

							mysqli_query($conn,$sqlupdatebrandform) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Brand Form Master.csv.Please check.");

						}

					}

				}

				 $rec_count++;

			}

			//Product group code checking start

				$sqlgroupcodesub_group="SELECT product_group_code FROM product_sub_group_master WHERE product_group_code NOT IN(SELECT product_group_code FROM product_group_master)";

				$rsgroupcodesub_group=mysqli_query($conn,$sqlgroupcodesub_group) or die(mysqli_error());

				$cntgroupcodesub_group=mysqli_num_rows($rsgroupcodesub_group);

				if($cntgroupcodesub_group>0)

				{

					$groupcodesub_group='';

					while($rowgroupcodesub_group=mysqli_fetch_array($rsgroupcodesub_group))

					{

						$groupcodesub_group=$groupcodesub_group.$rowgroupcodesub_group['product_group_code'].',';

					}

					$groupcodesub_group=substr($groupcodesub_group,0,-1);

					$errorgroupcodesub_group=$groupcodesub_group.' exists in Brand Form Master but not exists in Brand Master.';

					array_push($error_array,$errorgroupcodesub_group);

				}

			//Product group code checking end		

			$successval=1;

		}

		

	

	//For Sub Category Master CSV

	if(similar_file_exists("../csv/$folderName/Brand Sub Form Master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Brand Sub Form Master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

			$lines = file($filename);

			/*$sqldelete="truncate product_sub_group_master";

			$rsdelete=mysqli_query($conn,$sqldelete);*/

	

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

						

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

					

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   $data[]=$value;

				  //print_r($data);

				  	$dns_brand_code=trim($data[0]);

					$product_brand_code=trim($data[1]);

					$product_sub_group_code=trim($data[2]);

					$vertical_value=trim($data[3]);

					

					$csv_row_count=$rec_count+1;

					/*$sqlprodsubgroupnamechk="SELECT product_sub_group_name,product_group_code FROM product_sub_group_master 

											WHERE product_sub_group_name='".addslashes($product_sub_group_name)."'";

					$rsprodsubgroupnamechk=mysqli_query($conn,$sqlprodsubgroupnamechk);

					$countprodsubgroupnamechk=mysqli_num_rows($rsprodsubgroupnamechk);

					

					if($countprodsubgroupnamechk<1){*/

						$sqlbrandsubform  = "insert into product_brand_master SET ";

						$sqlbrandsubform .= "  product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";

						$sqlbrandsubform .= " ,	dns_product_brand_code='".mysqli_real_escape_string($dns_brand_code)."'";

						$sqlbrandsubform .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";

						$sqlbrandsubform .= " , product_brand_name='".mysqli_real_escape_string($product_brand_code)."'";

						$sqlbrandsubform .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";

						$sqlbrandsubform .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlbrandsubform) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Brand code and Brand form code columns in Brand Sub Form Master.csv.Please check.");

						//exit();

					/*}

					else

					{

						$rowprodsubgroupnamechk=mysqli_fetch_array($rsprodsubgroupnamechk);

						$product_group_name_existing=$rowprodsubgroupnamechk['product_group_code'];

						$vertical_value_existing=$rowprodsubgroupnamechk['vertical_value'];

						if($product_group_name_existing!=$product_group_name || $vertical_value_existing!=$vertical_value)

						{

							$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";

							$sqlupdatebrandform .= " product_group_code='".mysqli_real_escape_string($product_group_code)."'";

							$sqlupdatebrandform .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";

							$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP()";

							$sqlupdatebrandform .= "  WHERE product_sub_group_name='".addslashes($product_sub_group_name)."'";

							mysqli_query($conn,$sqlupdatebrandform) or die(mysqli_error().".Internel error occurrs @row $csv_row_count on Brand Form Master.csv.Please check.");

						}

					}*/

				}

				 $rec_count++;

			}

			$successval=1;

		}

		if(similar_file_exists("../csv/$folderName/Sku master.csv")!=false)

		{

			if($folderName=='RUPA')

			{

				$filesize=(filesize("$_SERVER[DOCUMENT_ROOT]/csv/$folderName/sku master.csv") * .0009765625) * .0009765625;// MB

				if($filesize >1)

				{

					header("location:uploadskuRUPA.php");

					exit();

				}

			}

			

			$filename=similar_file_exists("../csv/$folderName/Sku master.csv");

			$rec_count = 0;

			$ins_count = 0;

			$err = "";

			

			$lines = file($filename);

			$duplicate_product=array();

			$branch_code_array=array();

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

						

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

					

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

					 $data[]=$value;

					//print_r($data);

				  

					$csv_row_count=$rec_count+1;

					$branch_code_name=trim($data[0]);

					

					if(branch_wise_product == 'yes')

					{

						if($branch_code_name == '')

						{

							echo "Please provide valid branch code at row ".($csv_row_count+1);

							die;

						}

					}

					

					$dns_prod_code=trim($data[1]);

					$prod_desc=trim($data[2]);

					//$prod_desc=str_replace('~','"',$prod_desc);

					$product_group_code_name=trim($data[3]);

					$product_sub_group_code_name=trim($data[4]);

					$product_brand_code_name=trim($data[5]);

					//For SELVEL

					/*if($product_brand_code_name!=''){

					$prod_desc=$prod_desc.'-'.$product_brand_code_name;

					}

					$product_brand_code_name='';*/

					//End For SELVEL

					$cl_stk=trim($data[6]);

					if(strpos($cl_stk,',')!=false){

						$stkpos=strpos($cl_stk,',');

					$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);

					}

					$acedns=trim($data[7]);

					if($acedns =='')

					{

						echo "Please provide proper value for Acedns column at row ".($csv_row_count+1);

						die;

					}

					

					$black_list=trim($data[8]);

					if($black_list=='')

					{

						echo "Please provide proper value for Blacklist column at row ".($csv_row_count+1);

						die;

					}

					

					$vertical_value=trim($data[9]);

					$UOM1=trim($data[10]);

					$UOM2=trim($data[11]);

					$conversion=trim($data[12]);

					$pack_size=trim($data[13]);

					$UOM3=trim($data[14]);

					$conversion_factor_two=trim($data[15]);

					$conversion_factor_two=str_replace(',','',$conversion_factor_two);

					$TD=trim($data[16]);

					$focus=trim($data[17]);

					$vat=trim($data[18]);

					$pack_unit=trim($data[19]);

					

					//echo no_of_filter;

					if(providing_code=='yes'){

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";

					}

					else

					{

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";

					}

					$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

					$rowbranchcode=mysqli_fetch_array($rsbranchcode);

					$branch_code=$rowbranchcode['branch_code'];



						if(no_of_filter > 1){

						//Product group code checking start

						$sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_code_name)."'";

						$rsprodgroupnamechk=mysqli_query($conn,$sqlprodgroupnamechk);

						$countprodgroupnamechk=mysqli_num_rows($rsprodgroupnamechk);

						if($countprodgroupnamechk<1){

							$sqlmaxproductgroupcode="SELECT MAX( CAST( SUBSTRING( product_group_code, -(length( product_group_code ) -2), length( product_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_group_code from product_group_master";

							$rsmaxproductgroupcode=mysqli_query($conn,$sqlmaxproductgroupcode);

							$rowmaxproductgroupcode=mysqli_fetch_array($rsmaxproductgroupcode);

							$max_product_group_code=$rowmaxproductgroupcode['max_product_group_code'];

							

							if($max_product_group_code=='')

							{

								$max_product_group_code='1';

							}

							else

							{

								$max_product_group_code++;

							}

							$max_product_group_code='BR'.$max_product_group_code;

							$sqlbrand  = "INSERT INTO product_group_master SET ";

							$sqlbrand .= "  product_group_code='".$max_product_group_code."'";

							$sqlbrand .= " , product_group_name='".addslashes($product_group_code_name)."'";

							$sqlbrand .= " , vertical_value='".addslashes($vertical_value)."'";

							$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";

							mysqli_query($conn,$sqlbrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");

							$product_group_code=$max_product_group_code;

						}

						else

						{

							$rowprodgroupnamechk=mysqli_fetch_array($rsprodgroupnamechk);

							$product_group_code=$rowprodgroupnamechk['product_group_code'];

							$vertical_value_db=$rowprodgroupnamechk['vertical_value'];

							if($vertical_value_db!=$vertical_value)

							{

								$sqlupdatebrand  = "UPDATE product_group_master SET ";

								$sqlupdatebrand .= " vertical_value='".addslashes($vertical_value)."'";

								$sqlupdatebrand .= " , download_time=CURRENT_TIMESTAMP() WHERE product_group_name='".addslashes($product_group_code_name)."'";

								mysqli_query($conn,$sqlupdatebrand) or array_push($error_array,"mysqli_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");

							}

						}

						//Product group code checking end

					 }

					if(no_of_filter > 2){

						//Product sub group code checking start

						$sqlprodsubgroupnamechk="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".addslashes($product_sub_group_code_name)."' 

												AND product_group_code='".$product_group_code."'";

						$rsprodsubgroupnamechk=mysqli_query($conn,$sqlprodsubgroupnamechk);

						$countprodsubgroupnamechk=mysqli_num_rows($rsprodsubgroupnamechk);

						if($countprodsubgroupnamechk<1){

							$sqlmaxproductsubgroupcode="SELECT MAX( CAST( SUBSTRING( product_sub_group_code, -(length( product_sub_group_code ) -2), length( product_sub_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_sub_group_code from product_sub_group_master";

							$rsmaxproductsubgroupcode=mysqli_query($conn,$sqlmaxproductsubgroupcode);

							$rowmaxproductsubgroupcode=mysqli_fetch_array($rsmaxproductsubgroupcode);

							$max_product_sub_group_code=$rowmaxproductsubgroupcode['max_product_sub_group_code'];

							

							if($max_product_sub_group_code=='')

							{

								$max_product_sub_group_code='1';

							}

							else

							{

								$max_product_sub_group_code++;

							}

							$max_product_sub_group_code='BF'.$max_product_sub_group_code;

							$sqlbrandform  = "INSERT INTO product_sub_group_master SET ";

							$sqlbrandform .= "  product_sub_group_code='".mysqli_real_escape_string($max_product_sub_group_code)."'";

							$sqlbrandform .= " , product_sub_group_name='".addslashes($product_sub_group_code_name)."'";

							$sqlbrandform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

							$sqlbrandform .= " , vertical_value='".addslashes($vertical_value)."'";

							$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";

							mysqli_query($conn,$sqlbrandform);

							$product_sub_group_code=$max_product_sub_group_code;

						}

						else

						{

							$rowprodsubgroupnamechk=mysqli_fetch_array($rsprodsubgroupnamechk);

							$product_sub_group_code=$rowprodsubgroupnamechk['product_sub_group_code'];

							$vertical_value_sub_group=$rowprodsubgroupnamechk['vertical_value'];

							if($vertical_value_sub_group!=$vertical_value)

							{

								$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";

								$sqlupdatebrandform .= " vertical_value='".addslashes($vertical_value)."'";

								$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP() WHERE 

														product_sub_group_name='".addslashes($product_sub_group_code_name)." AND product_group_code='".$product_group_code."'";

								mysqli_query($conn,$sqlupdatebrandform);

							}

						}

						//Product sub group code checking end

					}

					if(no_of_filter > 3){

						//Product brand code checking start

						$sqlprodbrandnamechk="SELECT product_brand_code FROM product_brand_master WHERE product_brand_name='".addslashes($product_brand_code_name)."'

												AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";

						$rsprodbrandnamechk=mysqli_query($conn,$sqlprodbrandnamechk);

						$countprodbrandnamechk=mysqli_num_rows($rsprodbrandnamechk);

						if($countprodbrandnamechk<1){

							$sqlmaxproductbrandcode="SELECT MAX( CAST( SUBSTRING( product_brand_code, -(length( product_brand_code ) -2), length( product_brand_code ) -2 ) AS UNSIGNED ) ) AS max_product_brand_code from product_brand_master";

							$rsmaxproductbrandcode=mysqli_query($conn,$sqlmaxproductbrandcode);

							$rowmaxproductbrandcode=mysqli_fetch_array($rsmaxproductbrandcode);

							$max_product_brand_code=$rowmaxproductbrandcode['max_product_brand_code'];

							

							if($max_product_brand_code=='')

							{

								$max_product_brand_code='1';

							}

							else

							{

								$max_product_brand_code++;

							}

							$max_product_brand_code='BS'.$max_product_brand_code;

							$sqlbrandsubform  = "INSERT INTO product_brand_master SET ";

							$sqlbrandsubform .= "  product_brand_code='".mysqli_real_escape_string($max_product_brand_code)."'";

							$sqlbrandsubform .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";

							$sqlbrandsubform .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

							$sqlbrandsubform .= " , product_brand_name='".addslashes($product_brand_code_name)."'";

							$sqlbrandsubform .= " , vertical_value='".addslashes($vertical_value)."'";

							$sqlbrandsubform .= " , download_time=CURRENT_TIMESTAMP()";

							mysqli_query($conn,$sqlbrandsubform);

							$product_brand_code=$max_product_brand_code;

						}

						else

						{

							$rowprodbrandnamechk=mysqli_fetch_array($rsprodbrandnamechk);

							$product_brand_code=$rowprodbrandnamechk['product_brand_code'];

							$vertical_value_brand=$rowprodbrandnamechk['vertical_value'];

							if($vertical_value_brand!=$vertical_value)

							{

								$sqlupdatebrandsubform  = "UPDATE product_brand_master SET ";

								$sqlupdatebrandsubform .= " vertical_value='".addslashes($vertical_value)."'";

								$sqlupdatebrandsubform .= " , download_time=CURRENT_TIMESTAMP() 

															WHERE product_brand_name='".addslashes($product_brand_code_name)." 

															AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";

								mysqli_query($conn,$sqlupdatebrandsubform);

							}

						}

						//Product brand code checking end

					}

						if(branch_wise_product=='yes' || $folderName=='DNV' || $folderName=='SKIPPER')

						{

							$branch_code_condition= " AND branch_code='".$branch_code."'";

						}

						else

						{

							$branch_code_condition= "";

						}

						if(providing_code=='yes' || $folderName=='HALDIRAM'){

							$sqlskunamechk="SELECT * FROM product_master WHERE  dns_prod_code='".$dns_prod_code."'".$branch_code_condition."";

						}

						else

						{

							$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."'".$branch_code_condition." 

											AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 

											AND product_brand_code='".$product_brand_code."'";

						}

						$rsskunamechk=mysqli_query($conn,$sqlskunamechk);

						$countskunamechk=@mysqli_num_rows($rsskunamechk);

						$rowskunamechk=@mysqli_fetch_array($rsskunamechk);

						$updateflag=0;

						$insertflag=0;

						if($countskunamechk<1)

						{

							$sqlmaxskucode="SELECT MAX(prod_code) AS max_prod_code FROM  product_master WHERE 1";

							$rsmaxskucode=mysqli_query($conn,$sqlmaxskucode);

							$rowmaxskucode=mysqli_fetch_array($rsmaxskucode);

							$max_prod_code=$rowmaxskucode['max_prod_code'];

							

							if($max_prod_code=='')

							{

								$max_prod_code='12001';

							}

							else

							{

								$max_prod_code++;

							}

							$sql  = "insert into product_master ";

							$sql .= " SET prod_code='".$max_prod_code."'";

							$sql .= " , dns_prod_code='".$dns_prod_code."'";

							$sql .= " , branch_code='".$branch_code."'";

							$sql .= " , prod_desc='".addslashes($prod_desc)."'";

							$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

							$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";

							$sql .= " , product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";

							$sql .= " , cl_stk='".mysqli_real_escape_string($cl_stk)."'";

							$sql .= " , acedns='".strtoupper($acedns)."'";

							$sql .= " , black_list='".strtoupper($black_list)."'";

							$sql .= " , vertical_value='".addslashes($vertical_value)."'";

							$sql .= " , UOM1='".$UOM1."'";

							$sql .= " , UOM2='".$UOM2."'";

							$sql .= " , pack_size='".$pack_size."'";

							$sql .= " , UOM3='".$UOM3."'";

							$sql .= " , conversion_factor_two='".$conversion_factor_two."'";

							$sql .= " , TD='".$TD."'";

							$sql .= " , conversion_factor='".$conversion."'";

							$sql .= " , focus='".$focus."'";

							$sql .= " , weightage='".$weightage."'";

							$sql .= " , vat='".$vat."'";

							$sql .= " , addl_vat='".$addl_vat."'";

							$sql .= " , freight_cost='".$freight_cost."'";

							$sql .= " , pack_unit='".$pack_unit."'";

							$sql .= " , download_time=CURRENT_TIMESTAMP()";

							$sql .= " ,	download_time_cl_stk=CURRENT_TIMESTAMP()";

							//exit();

						mysqli_query($conn,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");

							$insertflag=1;

							if(branch_wise_cl_stk=='yes' || branch_wise_mrp=='yes')

							{

								$sqlbranch="SELECT branch_code FROM branch_master ORDER BY branch_code ASC";

								$rsbranch=mysqli_query($conn,$sqlbranch);

								while($rowbranch=mysqli_fetch_array($rsbranch))

								{

									$branch_code_cl_stk=$rowbranch['branch_code'];

									if(branch_wise_cl_stk=='yes')

									{

										$sqlinsertstk  = "insert into branch_product_wise_stock SET ";

										$sqlinsertstk .= "  	branch_code='".mysqli_real_escape_string($branch_code_cl_stk)."'";

										$sqlinsertstk .= " , product_code='".mysqli_real_escape_string($max_prod_code)."'";

										$sqlinsertstk .= " , closing_stk='0'";

										$sqlinsertstk .= " , download_time=CURRENT_TIMESTAMP()";

										mysqli_query($conn,$sqlinsertstk) or array_push($error_array,"mysqli_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN.");

									}

									/*if(branch_wise_mrp=='yes')

									{

										$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";

										$rsmaxmrpcode=mysqli_query($conn,$sqlmaxmrpcode);

										$rowmaxmrpcode=mysqli_fetch_array($rsmaxmrpcode);

										$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];

										

										if($max_mrp_code=='')

										{

											$max_mrp_code='001';

										}

										else

										{

											$max_mrp_code++;

										}

										$max_mrp_code='z'.$max_mrp_code;

				

										$sqlinsertmrp  = "insert into mrp ";

										$sqlinsertmrp .= " SET product_code='".mysqli_real_escape_string($max_prod_code)."'";

										$sqlinsertmrp .= " , branch_code='".mysqli_real_escape_string($branch_code_cl_stk)."'";

										$sqlinsertmrp .= " , mrp_code='".$max_mrp_code."'";

										$sqlinsertmrp .= " , dns_mrp_code=''";

										$sqlinsertmrp .= " , mrp='0'";

										$sqlinsertmrp .= " , sale_rate='0'";

										$sqlinsertmrp .= " , vertical_value='".addslashes($vertical_value)."'";

										$sqlinsertmrp .= " , UOM=''";

										$sqlinsertmrp .= " , download_time=CURRENT_TIMESTAMP()";

										mysqli_query($conn,$sqlinsertmrp)  or  array_push($error_array,"mysqli_error().Internal error occurs in addition of mrp.Please check.");

									}*/

								}

							}

							/*if((mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')) && branch_wise_mrp=='no')

							{

								$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";

								$rsmaxmrpcode=mysqli_query($conn,$sqlmaxmrpcode);

								$rowmaxmrpcode=mysqli_fetch_array($rsmaxmrpcode);

								$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];

								

								if($max_mrp_code=='')

								{

									$max_mrp_code='001';

								}

								else

								{

									$max_mrp_code++;

								}

								$max_mrp_code='z'.$max_mrp_code;

								$sqlinsertmrp  = "insert into mrp ";

								$sqlinsertmrp .= " SET product_code='".mysqli_real_escape_string($max_prod_code)."'";

								$sqlinsertmrp .= " , branch_code=''";

								$sqlinsertmrp .= " , mrp_code='".$max_mrp_code."'";

								$sqlinsertmrp .= " , dns_mrp_code=''";

								$sqlinsertmrp .= " , mrp='0'";

								$sqlinsertmrp .= " , sale_rate='0'";

								$sqlinsertmrp .= " , vertical_value='".addslashes($vertical_value)."'";

								$sqlinsertmrp .= " , UOM=''";

								$sqlinsertmrp .= " , download_time=CURRENT_TIMESTAMP()";

								mysqli_query($conn,$sqlinsertmrp)  or  array_push($error_array,"mysqli_error().Internal error occurs in addition of mrp.Please check.");

							}*/

						}

						else

						{

							$cl_stk_db=$rowskunamechk['cl_stk'];

							$branch_code_db=$rowskunamechk['branch_code'];

							$acedns_db=$rowskunamechk['acedns'];

							$black_list_db=$rowskunamechk['black_list'];

							$prod_code_db=$rowskunamechk['prod_code'];

							$product_group_code_db=$rowskunamechk['product_group_code'];

							$product_sub_group_code_db=$rowskunamechk['product_sub_group_code'];

							$product_brand_code_db=$rowskunamechk['product_brand_code'];

							$UOM1_db=$rowskunamechk['UOM1'];

							$UOM2_db=$rowskunamechk['UOM2'];

							$conversion_db=$rowskunamechk['conversion_factor'];

							$focus_db=$rowskunamechk['focus'];

							$weightage_db=$rowskunamechk['weightage'];

							$vat_db=$rowskunamechk['vat'];

							$addl_vat_db=$rowskunamechk['addl_vat'];

							$freight_cost_db=$rowskunamechk['freight_cost'];

							$vertical_value_db=$rowskunamechk['vertical_value'];

							$pack_unit_db=$rowskunamechk['pack_unit'];

							

							if(($cl_stk_db==$cl_stk) && ($acedns_db!=$acedns || $black_list_db!=$black_list 

								|| $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code 

								|| $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code || $conversion_db!=$conversion 

								|| $vertical_value_db!=$vertical_value || $conversion_factor_two_db!=$conversion_factor_two || $UOM3_db!=$UOM3 || $pack_size_db!=$pack_size || $TD_db!=$TD || $focus_db!=$focus || $weightage_db!=$weightage || $vat_db!=$vat || $addl_vat_db!=$addl_vat || $freight_cost_db!=$freight_cost || $pack_unit_db!=$pack_unit))

							{

								$sql  = "UPDATE product_master ";

								$sql .= " SET branch_code='".$branch_code."'";

								$sql .= " , prod_desc='".addslashes($prod_desc)."'";

								$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

								$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";

								$sql .= " , product_brand_code='".mysqli_real_escape_string($product_brand_code)."'";

								$sql .= " , acedns='".strtoupper($acedns)."'";

								$sql .= " , black_list='".strtoupper($black_list)."'";

								$sql .= " , UOM1	 ='".$UOM1."'";

								$sql .= " , UOM2  ='".$UOM2."'";

								$sql .= "  ,conversion_factor='".$conversion."'";

								$sql .= " , UOM3='".$UOM3."'";

								$sql .= " , conversion_factor_two='".$conversion_factor_two."'";

								$sql .= " , TD='".$TD."'";

								$sql .= " , focus='".$focus."'";

								$sql .= " , weightage='".$weightage."'";

								$sql .= " , vat='".$vat."'";

								$sql .= " , addl_vat='".$addl_vat."'";

								$sql .= " , freight_cost='".$freight_cost."'";

								$sql .= " , pack_size='".$pack_size."'";

								$sql .= " , pack_unit='".$pack_unit."'";

								$sql .= " , download_time=CURRENT_TIMESTAMP()";

								$sql .= " , vertical_value='".addslashes($vertical_value)."' WHERE prod_code='".$prod_code_db."'";

								mysqli_query($conn,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");

								$updateflag=1;

							}

							else if($cl_stk_db!=$cl_stk)

							{

								$sql  = "UPDATE product_master ";

								$sql .= " SET cl_stk='".$cl_stk."',download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'";

								mysqli_query($conn,$sql) or array_push($error_array,"mysqli_error().Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");

								$updateflag=1;

							}

						}

						if(branch_wise_product=='yes' && ($updateflag==1 || $insertflag==1))//Start For emp data download log

						{

							if(!in_array($branch_code,$branch_code_array))

							{

								array_push($branch_code_array,$branch_code);

								$sqlbranchwiseemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$branch_code."', branch_code)";

								$rsbranchwiseemp=mysqli_query($conn,$sqlbranchwiseemp);

								while($rowbranchwiseemp=mysqli_fetch_array($rsbranchwiseemp))

								{

									$emp_code_branchwise=$rowbranchwiseemp['emp_code'];

									modifyempdatadownloadlog($conn,$emp_code_branchwise,strtoupper($folderName));

								}

							}

						}//End For emp data download log

					//For TT

					//array_push($duplicate_product,$dns_prod_code." \t".$prod_desc." \t".$product_group_code." \t".$product_sub_group_code);



				}

		$rec_count++;

		}

		//exit();

		if(branch_wise_product=='no')//Start For emp data download log with no branch tagging

		{

			$emp_code='';

			modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

		}//End For emp data download log with no branch tagging



		//Product group code checking start

			if(no_of_filter==2 || no_of_filter==3){

				$sqlgroupcodeproduct="SELECT product_group_code FROM product_master WHERE product_group_code NOT IN

									(SELECT product_group_code FROM product_group_master) GROUP BY product_group_code";

				$rsgroupcodeproduct=mysqli_query($conn,$sqlgroupcodeproduct);

				$cntgroupcodeproduct=mysqli_num_rows($rsgroupcodeproduct);

				if($cntgroupcodeproduct>0)

				{

					$groupcodeproduct='';

					while($rowgroupcodeproduct=mysqli_fetch_array($rsgroupcodeproduct))

					{

						$groupcodeproduct=$groupcodeproduct.$rowgroupcodeproduct['product_group_code'].',';

					}

					$groupcodeproduct=substr($groupcodeproduct,0,-1);

					if($folderName=='RUPA')

					{

						$errorgroupcodeproduct='There are many brand exists in Sku master but not exists in Brand Master.';

					}

					else

					{

						$errorgroupcodeproduct=$groupcodeproduct.' exists in Sku master but not exists in Brand Master.';

					}

					array_push($error_array,$errorgroupcodeproduct);

				}

			}

		//Product group code checking end

		//Product sub group code checking start

			if(no_of_filter==3){

			$sqlsubgroupcodeproduct="SELECT product_sub_group_code FROM product_master WHERE product_sub_group_code NOT IN

			(SELECT product_sub_group_code FROM product_sub_group_master) GROUP BY product_sub_group_code";

			$rssubgroupcodeproduct=mysqli_query($conn,$sqlsubgroupcodeproduct);

			$cntsubgroupcodeproduct=mysqli_num_rows($rssubgroupcodeproduct);

			if($cntsubgroupcodeproduct>0)

			{

				$subgroupcodeproduct='';

				while($rowsubgroupcodeproduct=mysqli_fetch_array($rssubgroupcodeproduct))

				{

					$subgroupcodeproduct=$subgroupcodeproduct.$rowsubgroupcodeproduct['product_sub_group_code'].',';

				}

				$subgroupcodeproduct=substr($subgroupcodeproduct,0,-1);

				$errorsubgroupcodeproduct=$subgroupcodeproduct.' exists in Sku master but not exists in Brand Form Master.';

				array_push($error_array,$errorsubgroupcodeproduct);

			}

		}

		//Product sub group code checking end

		/*foreach($duplicate_product as $duplicate_product_val)

		{

			$dupliacateproductval=$dupliacateproductval.$duplicate_product_val."\n";

		}

		//print_r($customeroutstandingmissmatchArr);

				$data = str_replace("\r","",$dupliacateproductval);

				

				header("Content-type: application/x-msdownload"); 

				header("Content-Disposition: attachment; filename=duplicateproduct.xls"); 

				header("Pragma: no-cache"); 

				header("Expires: 0"); 

				print "$data";*/

			$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for SKU Master.csv is wrong.";

		exit();

	}*/

	//exit();	

	//For sub product

		if(similar_file_exists("../csv/$folderName/Sub sku master.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Sub sku master.csv");

			$rec_count = 0;

			$ins_count = 0;

			$err = "";

			

			$lines = file($filename);

			$duplicate_product=array();

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

					

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				  $data[]=$value;

				 //print_r($data);

					$sub_product_name=trim($data[0]);

					$dns_prod_code='';

					$branch_code='';

					//$prod_desc=str_replace('~','"',$prod_desc);

					$product_brand_code_name='';

					$cl_stk=0;

					$acedns='Y';

					$black_list='N';

					$vertical_value='';

					$UOM1='KG';

					$UOM2='';

					$conversion='';



					$sqlproductsubgroupcode="SELECT product_sub_group_code,product_group_code FROM product_sub_group_master ";

					$rsproductsubgroupcode=mysqli_query($conn,$sqlproductsubgroupcode);

					while($rowproductsubgroupcode=mysqli_fetch_array($rsproductsubgroupcode))

					{

					$product_group_code=$rowproductsubgroupcode['product_group_code'];

					$product_sub_group_code=$rowproductsubgroupcode['product_sub_group_code'];

						

						if($max_prod_code=='')

						{

							$max_prod_code='12001';

						}

						else

						{

							$max_prod_code++;

						}

						$sql  = "insert into product_master ";

						$sql .= " SET prod_code='".$max_prod_code."'";

						$sql .= " , dns_prod_code='".$dns_prod_code."'";

						$sql .= " , branch_code='".$branch_code."'";

						$sql .= " , prod_desc='".addslashes($sub_product_name)."'";

						$sql .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

						$sql .= " , product_sub_group_code='".mysqli_real_escape_string($product_sub_group_code)."'";

						$sql .= " , product_brand_code='".mysqli_real_escape_string($product_brand_code_name)."'";

						$sql .= " , cl_stk='".mysqli_real_escape_string($cl_stk)."'";

						$sql .= " , acedns='".$acedns."'";

						$sql .= " , black_list='".$black_list."'";

						$sql .= " , vertical_value='".$vertical_value."'";

						$sql .= " , UOM1='".$UOM1."'";

						$sql .= " , UOM2='".$UOM2."'";

						$sql .= " , conversion_factor='".$conversion."'";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");

				}

			}

		$rec_count++;

	}

		//print_r($customeroutstandingmissmatchArr);

			/*$data = str_replace("\r","",$dupliacateproductval);

			

			header("Content-type: application/x-msdownload"); 

			header("Content-Disposition: attachment; filename=duplicateproduct.xls"); 

			header("Pragma: no-cache"); 

			header("Expires: 0"); 

			print "$data";*/

		$successval=1;

	}

	//For Branch wise closing stock CSV

	if(similar_file_exists("../csv/$folderName/Branch closing stock.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Branch closing stock.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_branch_code_name=trim($data[0]);

				$prod_code_name=trim($data[1]);

				$brand_code_name=trim($data[2]);

				$brand_form_code_name=trim($data[3]);

				$brand_sub_form_code_name=trim($data[4]);

				$cl_stk=trim($data[5]);

				if($cl_stk=='') $cl_stk=0;

				if(strpos($cl_stk,',')!=false){

				   $cl_stk =str_replace(',','',$cl_stk);

				}

				

				$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".$brand_code_name."'";

				$rsproductgroupcode=mysqli_query($conn,$sqlproductgroupcode);

				$rowproductgroupcode=mysqli_fetch_array($rsproductgroupcode);

				$product_group_code=$rowproductgroupcode['product_group_code'];



				$sqlproductsubgroupcode="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".$brand_form_code_name."' 

										AND product_group_code='".$product_group_code."'";

				$rsproductsubgroupcode=mysqli_query($conn,$sqlproductsubgroupcode);

				$rowproductsubgroupcode=mysqli_fetch_array($rsproductsubgroupcode);

				$product_sub_group_code=$rowproductsubgroupcode['product_sub_group_code'];



				$sqlproductbrandcode="SELECT product_brand_code FROM product_brand_master WHERE product_brand_name='".$brand_sub_form_code_name."' 

										AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."'";

				$rsproductbrandcode=mysqli_query($conn,$sqlproductbrandcode);

				$rowproductbrandcode=mysqli_fetch_array($rsproductbrandcode);

				$product_brand_code=$rowproductbrandcode['product_brand_code'];



				if(providing_code=='yes'){

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code_name."'";

					$sqlproductcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND 

									product_group_code='".addslashes($product_group_code)."' AND product_sub_group_code='".addslashes($product_sub_group_code)."' 

									AND product_brand_code='".addslashes($product_brand_code)."'";

				}

				else

				{

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($dns_branch_code_name)."'";

					$sqlproductcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' AND 

									product_group_code='".addslashes($product_group_code)."' AND product_sub_group_code='".addslashes($product_sub_group_code)."' 

									AND product_brand_code='".addslashes($product_brand_code)."'";

				} 

				$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

				$rowbranchcode=mysqli_fetch_array($rsbranchcode);

				$branch_code=$rowbranchcode['branch_code'];

				$rsproductcode=mysqli_query($conn,$sqlproductcode);

				$rowproductcode=mysqli_fetch_array($rsproductcode);

				$product_code=$rowproductcode['prod_code'];



				$sqlclstkchk="SELECT branch_code,closing_stk FROM branch_product_wise_stock WHERE branch_code='".$branch_code."' 

							AND product_code='".$product_code."'";

				$rsclstkchk=mysqli_query($conn,$sqlclstkchk);

				$countstkchk=mysqli_num_rows($rsclstkchk);

				$rowclstkchk=mysqli_fetch_array($rsclstkchk);

				

				$csv_row_count=$rec_count+1;

				if($countstkchk<1)

				{

					$sqlinsertstk  = "insert into branch_product_wise_stock SET ";

					$sqlinsertstk .= "  	branch_code='".mysqli_real_escape_string($branch_code)."'";

					$sqlinsertstk .= " , product_code='".mysqli_real_escape_string($product_code)."'";

					$sqlinsertstk .= " , closing_stk='".mysqli_real_escape_string($cl_stk)."'";

					$sqlinsertstk .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($conn,$sqlinsertstk) or array_push($error_array,"mysqli_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN.");

				}

				else

				{

					$cl_stk_db=$rowclstkchk['closing_stk'];

					if($cl_stk!=$cl_stk_db)

					{

						$sqlupdatestk  = "UPDATE branch_product_wise_stock SET ";

						$sqlupdatestk .= "  	closing_stk='".mysqli_real_escape_string($cl_stk)."'";

						$sqlupdatestk .= ",  download_time=CURRENT_TIMESTAMP()";

						$sqlupdatestk .= "  WHERE branch_code='".mysqli_real_escape_string($branch_code)."' AND product_code='".mysqli_real_escape_string($product_code)."'";

						mysqli_query($conn,$sqlupdatestk) or array_push($error_array,"mysqli_error().Internal error occurs on branch product wise closing stk table.Please contact ADMIN");

					}

				}

			}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Branch master.csv is wrong.";

		exit();

	}*/



	//For Employee CSV

	if((similar_file_exists("../csv/$folderName/Employee master.csv")!=false) && strtoupper($folderName)!='START')

	{

		$filename=similar_file_exists("../csv/$folderName/Employee master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

			$lines = file($filename);

			//print_r($lines);

			$reporting_to_array=array();

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				

				if($rec_count>=1)

				{ 

					$reporting_to_val='';

					$branch_code='';



					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

						if(!$double_coute_found && $char=="\"")

						{  

						

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

					

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				  $data[]=$value;

				  

					$dns_employee_code=trim($data[0]);

					$employee_name=trim($data[1]);

					$branch_code_name=trim($data[2]);

					$vertical_value=trim($data[3]);

					$reporting_to=trim($data[4]);

					if(strpos($reporting_to,';')!=false)

					 {

						$reporting_to=str_replace(';',',',$reporting_to);

					 }

					if($reporting_to!='')

					{

						$reporting_to=str_replace(', ',',',$reporting_to);

						$reporting_val_array=explode(',',$reporting_to);

						foreach($reporting_val_array as $reporting_val)

						{

							if(providing_code == 'yes')

							{

								$sql_check_emp_code = "SELECT emp_code FROM employee_master WHERE dns_emp_code = '".ltrim($reporting_val)."'";

									/*if($total_rows_dns_emp_code == 0)

									{

										echo "Please provide dns employee code in reporting to at row ".($csv_row_count+1);

										die;

									}*/

							}

							else

							{

							   $sql_check_emp_code = "SELECT emp_code FROM employee_master WHERE emp_name = '".ltrim($reporting_val)."'";

							}

							$res_check_emp_code = mysqli_query($conn,$sql_check_emp_code);

							$total_rows_emp_code = mysqli_num_rows($res_check_emp_code);

							$reporting_not_exists='';

							if($total_rows_emp_code == 0)

							{

								if(providing_code == 'yes')

								{

									$reporting_not_exists=$dns_employee_code.'#'.$reporting_val;

									array_push($reporting_to_array,$reporting_not_exists);

								}

								else

								{

									$reporting_not_exists=$employee_name.'#'.$reporting_val;

									array_push($reporting_to_array,$reporting_not_exists);

								}

							}

						}

				    }

					

					

					$email=trim($data[5]);

					$phone_no=trim($data[6]);

					$sale_access=trim($data[7]);

					$designation=trim($data[8]);

					$HQ=trim($data[9]);

					$state=trim($data[10]);

					$zone=trim($data[11]);

					$acedns=trim($data[12]);

					$district=trim($data[13]);

					$functionality=trim($data[14]);

					$functionality_rel_val=trim($data[15]);

					$DOJ=trim($data[16]);

					if(strpos($DOJ,'/')!=false){

					 $DOJArr=explode('/',$DOJ);

					}

					if(strpos($DOJ,'-')!=false){

					 $DOJArr=explode('-',$DOJ);

					}

					if(strlen($DOJArr[2])==2)

					{

						$year='20'.$DOJArr[2];

					}

					else

					{

						$year=$DOJArr[2];

					}

					$DOJ=$year.'-'.$DOJArr[1].'-'.$DOJArr[0];

					if($acedns =='N')

					{

						$app_access='N';

					}

					else

					{

						$app_access='Y';

					}

					if($folderName=='KHMER')

					{

						if(strtoupper($functionality)=='DOS')

						{

							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($employee_name)."' AND cust_type='D'";

							$rscustomercode=mysqli_query($conn,$sqlcustomercode);

							$rowcustomercode=mysqli_fetch_array($rscustomercode);

							$functionality_rel_val=$rowcustomercode['customer_code'];

						}

						if(strtoupper($functionality)=='ROS')

						{

							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($employee_name)."' AND cust_type='R'";

							$rscustomercode=mysqli_query($conn,$sqlcustomercode);

							$rowcustomercode=mysqli_fetch_array($rscustomercode);

							$functionality_rel_val=$rowcustomercode['customer_code'];

						}

						$sqlinsertcond=",functionality='".$functionality."',functionality_rel_val='".$functionality_rel_val."'";

					}

					else if(strtoupper($folderName)=='VCONNECT'){

						if(strtoupper($functionality)=='ROS')

						{

							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($functionality_rel_val)."' 

											AND cust_type='R'";

							$rscustomercode=mysqli_query($conn,$sqlcustomercode);

							$rowcustomercode=mysqli_fetch_array($rscustomercode);

							$functionality_rel_val=$rowcustomercode['customer_code'];

						}

						$sqlinsertcond=",functionality='".$functionality."',functionality_rel_val='".$functionality_rel_val."'";

					}

					else $sqlinsertcond='';



					if(providing_code=='yes'){

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";

						$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(dns_emp_code,'".$reporting_to."')";

					}

					else

					{

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(branch_name,'".$branch_code_name."')";

						$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name,'".$reporting_to."')";

					}

					$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

					while($rowbranchcode=mysqli_fetch_array($rsbranchcode))

					{

						$branch_code=$branch_code.$rowbranchcode['branch_code'].',';

					}

					$branch_code=substr($branch_code,0,-1);



					$rsreportingto=mysqli_query($conn,$sqlreportingto);

					

					while($rowreportingto=mysqli_fetch_array($rsreportingto))

					{

						$reporting_to_val=$reporting_to_val.$rowreportingto['emp_code'].',';

					}

					$reporting_to_val=substr($reporting_to_val,0,-1);

					if(providing_code=='yes'){

						$sqlempnamechk="SELECT emp_code,acedns FROM employee_master WHERE dns_emp_code='".$dns_employee_code."'";

					}

					else{

						$sqlempnamechk="SELECT emp_code,acedns FROM employee_master WHERE emp_name='".addslashes($employee_name)."'";

					}

					$rsempnamechk=mysqli_query($conn,$sqlempnamechk);

					$countempnamechk=mysqli_num_rows($rsempnamechk);



					$csv_row_count=$rec_count+1;

					if($countempnamechk<1)

					{

						$sqlmaxempcode="SELECT MAX(emp_code) AS max_emp_code FROM  employee_master ";

						$rsmaxempcode=mysqli_query($conn,$sqlmaxempcode);

						$rowmaxempcode=mysqli_fetch_array($rsmaxempcode);

						$max_emp_code=$rowmaxempcode['max_emp_code'];

						if($max_emp_code=='')

						{

							$max_emp_code='E0001';

						}

						else

						{

							$max_emp_code++;

						}

						if($folderName=='PARLE')

						{

							if(strtoupper($designation)=='ASM') $level='2';

							if(strtoupper($designation)=='DSM') $level='3';

							if(strtoupper($designation)=='ZSM') $level='4';

							$sqllevel = " , level='".$level."'";

						}

						else	$sqllevel='';

						

						

						$sql  = "insert into employee_master ";

						$sql .= " SET emp_code='".$max_emp_code."'";

						$sql .= " , dns_emp_code='".$dns_employee_code."'";

						$sql .= " , emp_name='".ltrim(addslashes($employee_name))."'";

						$sql .= " , branch_code='".$branch_code."'";

						$sql .= " , vertical_value='".$vertical_value."'";

						$sql .= " , reporting_to='".$reporting_to_val."'";

						$sql .= " , email='".addslashes($email)."'";

						$sql .= " , phone_no='".$phone_no."'";

						$sql .= " , sale_access='".$sale_access."'";

						$sql .= " , HQ='".$HQ."'";

						$sql .= " , designation='".$designation."'";

						$sql .= " , acedns='".$acedns."'";

						$sql .= " , app_access='".$app_access."'";

						$sql .= " , state='".$state."'";

						$sql .= " , zone='".$zone."'";

						$sql .= " , DOJ='".$DOJ."'";

						$sql .= " , District='".$district."'".$sqllevel.$sqlinsertcond;

						$sql .= " , acedns_changed_date=CURRENT_TIMESTAMP()";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sql) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Employee code column in Employee Master.csv.Please check.");

						

						$sqlchkchangepassword="SELECT emp_code FROM changepassword WHERE emp_code='".$max_emp_code."'";

						$rschkchangepassword=mysqli_query($conn,$sqlchkchangepassword);

						$countchkchangepassword=mysqli_num_rows($rschkchangepassword);

						if($countchkchangepassword==0)

						{

							$sqlcp  = "insert into changepassword ";

							$sqlcp .= " SET emp_code='".$max_emp_code."'";

							$sqlcp .= " , newpassword='1234'";

							$sqlcp .= " , oldpassword='1234'"; 

							$sqlcp .= " , status='true'";

							$sqlcp .= " , is_licensed='1'"; 

							mysqli_query($conn,$sqlcp) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");

						}

						//modifyempdatadownloadlog($conn,$max_emp_code,strtoupper($folderName));

						if($folderName=='STAR')

						{

							$sqlmis  = "insert into mis_data_details ";

							$sqlmis .= " SET emp_code='".$max_emp_code."'";

							$sqlmis .= " , sale_access='".$sale_access."'";

							$sqlmis .= " , reporting_to='".$reporting_to_val."'"; 

							mysqli_query($conn,$sqlmis) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on mis data details table.

							PLease contact aceDNS admin.");

						}

					}

					else

					{

						$rowempnamechk=mysqli_fetch_array($rsempnamechk);

						$emp_code_db=$rowempnamechk['emp_code'];

						$acedns_db=$rowempnamechk['acedns'];

						$sqlupdate  = "UPDATE employee_master ";

						$sqlupdate .= " SET branch_code='".$branch_code."'";

						$sqlupdate .= " , vertical_value='".$vertical_value."'";

						$sqlupdate .= " , reporting_to='".$reporting_to_val."'";

						$sqlupdate .= " , email='".$email."'";

						$sqlupdate .= " , sale_access='".$sale_access."'";

						$sqlupdate .= " , HQ='".$HQ."'";

						$sqlupdate .= " , designation='".$designation."'";

						$sqlupdate .= " , acedns='".$acedns."'";

						$sqlupdate .= " , app_access='".$app_access."'";

						$sqlupdate .= " , state='".$state."'";

						$sqlupdate .= " , zone='".$zone."'";

						$sqlupdate .= " , DOJ='".$DOJ."'";

						$sqlupdate .= " , District='".$district."'".$sqlinsertcond;

						$sqlupdate .= " , download_time=CURRENT_TIMESTAMP()";

						$sqlupdate .= " , phone_no='".$phone_no."' WHERE emp_code='".addslashes($emp_code_db)."'";

						mysqli_query($conn,$sqlupdate) or  array_push($error_array,"mysqli_error().Internel error  @row $csv_row_count on in Employee Master.csv.Please check.");

/*------CHECK AND ADD DEALER DATA INTO CHANGEPASSWORD TABLE CODE START------------------*/
$sqlchkchangepassword="SELECT emp_code FROM changepassword WHERE emp_code='".$emp_code_db."'";
$rschkchangepassword=mysqli_query($conn,$sqlchkchangepassword);
$countchkchangepassword=mysqli_num_rows($rschkchangepassword);
if($countchkchangepassword==0){
$sqlcp  = "insert into changepassword ";
$sqlcp .= " SET emp_code='".$emp_code_db."'";
$sqlcp .= " , newpassword='1234'";
$sqlcp .= " , oldpassword='1234'"; 
$sqlcp .= " , status='true'";
$sqlcp .= " , is_licensed='1'"; 
mysqli_query($conn,$sqlcp) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");
}
/*------CHECK AND ADD DEALER DATA INTO CHANGEPASSWORD TABLE CODE END------------------*/

						if($acedns_db !=$acedns)

						{

							$sql_update_emp_acedns = "UPDATE employee_master SET acedns_changed_date=CURRENT_TIMESTAMP() 

										WHERE emp_code = '".$emp_code_db."'";

							mysqli_query($conn,$sql_update_emp_acedns);

						}

						//modifyempdatadownloadlog($conn,$emp_code_db,strtoupper($folderName));

						

						$sqlupdatebranchtime="UPDATE branch_master SET download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$branch_code."'";

						mysqli_query($conn,$sqlupdatebranchtime);

					}

					//Customer Employee mapping checking

					if(customer_employee_mapping=='no' && $countempnamechk<1 && $nick_name!='LIPL')

					{

						$sqlcustomeremployee="SELECT emp_code FROM customer_master WHERE emp_code <>'' AND customer_code NOT LIKE 'N%' ORDER BY emp_code ASC LIMIT 0,1";

						$rscustomeremployee=mysqli_query($conn,$sqlcustomeremployee);

						$rowcustomeremployee=mysqli_fetch_array($rscustomeremployee);

						$customeremployeecode=$rowcustomeremployee['emp_code'];

						

						$sqlupdateemployee="UPDATE employee_master set reporting_to=CONCAT(reporting_to,',','".$max_emp_code."') 

											WHERE emp_code='".$customeremployeecode."'";

						$rsupdateemployee=mysqli_query($conn,$sqlupdateemployee);					

					}

					if($nick_name == 'LIPL' && $countempnamechk<1){

						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";

						$rsmaxroutecode=mysqli_query($conn,$sqlmaxroutecode);

						$rowmaxroutecode=mysqli_fetch_array($rsmaxroutecode);

						$new_route_code=$rowmaxroutecode['new_route_code'];

						

						$max_route_code='RT/'.($new_route_code+1);

												

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code=''";

						$sqlroute .= " ,route_name='KOLKATA'";

						$sqlroute .= " , emp_code='".$max_emp_code."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlroute);

						$route_code=$max_route_code;

						

						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";

						$rsmaxcustomercode=mysqli_query($conn,$sqlmaxcustomercode);

						$rowmaxcustomercode=mysqli_fetch_array($rsmaxcustomercode);

						$max_customer_code=$rowmaxcustomercode['max_customer_code'];

						

						$max_customer_code++;

						

						$sql  = "insert into customer_master ";

						$sql .= " SET customer_code='".$max_customer_code."'";

						$sql .= " , dns_customer_code=''";

						$sql .= " , customer_name='STOREHANDLE'";

						$sql .= " , branch_code=''";

						$sql .= " , phone_no=''";

						$sql .= " , route_code='".$route_code."'";

						$sql .= " , emp_code='".$max_emp_code."'";

						$sql .= " , current_balance	=''";

						$sql .= " , credit_limit=''";

						$sql .= " , credit_days=''";

						$sql .= " , acedns='Y'";

						$sql .= " , black_list='N'";

						$sql .= " , TD=''";

						$sql .= " , rds_tag=''";

						$sql .= " , cust_type=''";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						//exit();

						mysqli_query($conn,$sql);

					}

				}

				 $rec_count++;

			}

			foreach($reporting_to_array as $reporting_to_concat_values)

			{

				$reporting_to_splitval=explode('#',$reporting_to_concat_values);

				if(providing_code=='yes'){

					$sqlempowncode="SELECT emp_code,reporting_to FROM employee_master WHERE dns_emp_code='".$reporting_to_splitval[0]."'";

					$sqlempbosscode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$reporting_to_splitval[1]."'";

				}

				else{

					$sqlempowncode="SELECT emp_code,reporting_to FROM employee_master WHERE emp_name='".$reporting_to_splitval[0]."'";

					$sqlempbosscode="SELECT emp_code FROM employee_master WHERE emp_name='".$reporting_to_splitval[1]."'";

				}

				$rsempowncode=mysqli_query($conn,$sqlempowncode);

				$rsempbosscode=mysqli_query($conn,$sqlempbosscode);

				$countempowncode=mysqli_num_rows($rsempowncode);

				$countempbosscode=mysqli_num_rows($rsempbosscode);

				

				if($countempowncode == 0 || $countempbosscode==0)

				{

					echo "Please provide the details for the reporting to '".$reporting_to_splitval[1]."'";

					die;

				}

				if($countempbosscode >0)

				{

					$rowempowncode=mysqli_fetch_array($rsempowncode);

					$emp_own_reportingto=$rowempowncode['reporting_to'];

					$rowempbosscode=mysqli_fetch_array($rsempbosscode);

					if($emp_own_reportingto!='')

					{

					  $sqlupdatereportingto="UPDATE employee_master SET reporting_to=CONCAT(reporting_to,',".$rowempbosscode['emp_code']."') 

						                   WHERE emp_code='".$rowempowncode['emp_code']."'";

					}

					else

					{

						$sqlupdatereportingto="UPDATE employee_master SET reporting_to='".$rowempbosscode['emp_code']."' 

										WHERE emp_code='".$rowempowncode['emp_code']."'";

					}

					mysqli_query($conn,$sqlupdatereportingto);					

				}

			}

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Employee Master.csv is wrong.";

			exit();

		}*/

		

	//For Route CSV

	if(similar_file_exists("../csv/$folderName/ROUTE MASTER.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/ROUTE MASTER.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		/*$sqlroutedelete="truncate route_master";

		$rsroutedelete=mysqli_query($conn,$sqlroutedelete);*/

		

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

				//$routcode=trim($data[0]);

				$route_name	  =trim($data[0]); 

				$emp_code_name   =trim($data[1]); 



				

				/*if(strlen($emp_code)>4){

					$emp_code=substr($emp_code, -4);

					if(substr($emp_code, 0,1)=='0')

					{

						$emp_code=substr($emp_code,-3);

					}

				}*/

				$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

				$rsempcode=mysqli_query($conn,$sqlempcode);

				$rowempcode=mysqli_fetch_array($rsempcode);

				$emp_code=$rowempcode['emp_code'];

				

				$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$emp_code."'";

				$rsroutechk=mysqli_query($conn,$sqlroutechk);

				$countroutechk=mysqli_num_rows($rsroutechk);

				if($countroutechk<1)

				{

					$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master";

					$rsmaxroutecode=mysqli_query($conn,$sqlmaxroutecode);

					$rowmaxroutecode=mysqli_fetch_array($rsmaxroutecode);

					$new_route_code=$rowmaxroutecode['new_route_code'];

					

					if($new_route_code=='')

					{

						$max_route_code='RT/1';

					}

					else

					{

						$max_route_code='RT/'.($new_route_code+1);

						//$max_route_code++;

					}



					$sqlroute  = "insert into route_master ";

					$sqlroute .= " SET route_code='".$max_route_code."'";

					$sqlroute .= " ,route_name='".$route_name."'";

					$sqlroute .= " , emp_code='".$emp_code."'";

					

					mysqli_query($conn,$sqlroute);

				}

			}

			 $rec_count++;

		}		

		$successval=1;

	}

		//For RDS CSV

	if(similar_file_exists("../csv/$folderName/RDS MASTER.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/RDS MASTER.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqlroutedelete="truncate route_master";

		$rsroutedelete=mysqli_query($conn,$sqlroutedelete);*/

		

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

				$rdscode=trim($data[0]);

				$rdsname	  =trim($data[1]); 

				$emp_code_name =trim($data[2]);

				$rds_type =trim($data[3]);

				

				if(providing_code=='yes'){

					$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";

					$rsempcode=mysqli_query($conn,$sqlempcode);

					$rowempcode=mysqli_fetch_array($rsempcode);

					$emp_code=$rowempcode['emp_code'];

				}

				else

				{

					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

					$rsempcode=mysqli_query($conn,$sqlempcode);

					$rowempcode=mysqli_fetch_array($rsempcode);

					$emp_code=$rowempcode['emp_code'];

				}

				$sqlrdsnamechk="SELECT * FROM rds_master WHERE rds_name='".addslashes($rdsname)."' AND emp_code='".$emp_code."'";

				$rsrdsnamechk=mysqli_query($conn,$sqlrdsnamechk);

				$countrdsnamechk=mysqli_num_rows($rsrdsnamechk);

					

				$csv_row_count=$rec_count+1;

				if($countrdsnamechk<1)

				{

					$sqlmaxrdscode="SELECT MAX(rds_code) AS max_rds_code FROM  rds_master WHERE 1";

					$rsmaxrdscode=mysqli_query($conn,$sqlmaxrdscode);

					$rowmaxrdscode=mysqli_fetch_array($rsmaxrdscode);

					$max_rds_code=$rowmaxrdscode['max_rds_code'];

					

					if($max_rds_code=='')

					{

						$max_rds_code='C/00001';

					}

					else

					{

						$max_rds_code++;

					}



				

					$sqlrds  = "insert into rds_master ";

					$sqlrds .= " SET rds_code='".$max_rds_code."'";

					$sqlrds .= " ,dns_rds_code='".$rdscode."'";

					$sqlrds .= " ,rds_name='".$rdsname."'";

					$sqlrds .= " , emp_code='".$emp_code."'";

					$sqlrds .= " , rds_type='".$rds_type."'";

					$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";

				

					mysqli_query($conn,$sqlrds) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee name columns in rds master.csv.Please check.");

				}

				else

				{

					$sqlupdated  = "update rds_master ";

					$sqlupdated .= " SET rds_type='".$rds_type."'";

					$sqlupdated .= " WHERE rds_name='".addslashes($rdsname)."' AND emp_code='".$emp_code."'";

					mysqli_query($conn,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on rds master.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	//For Customer CSV

	if(similar_file_exists("../csv/$folderName/Customer Master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Customer Master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

			$lines = file($filename);

			$countroute=0;

			if(modified_customer_emp_route=='no' && $folderName!='ABDOS' && $folderName!='DNV' && $folderName!='HALDIRAM' && $folderName!='SHAKERS' && $folderName!='SKIPPER' && $folderName!='SMPDEMO' && $folderName!='SAVERA' && $folderName!='GWAAL' && $folderName!='KARMA' && $folderName!='STAR')

			{

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .=$char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   $data[]=$value;

				  //print_r($data);

					

					$csv_row_count=$rec_count+1;

					$dns_customer_code = trim($data[0]);

					$customer_name	=trim($data[1]);

					$phone_no		=trim($data[2]);

					$dns_route_code	  =trim($data[3]);

					$route_name	  =trim($data[4]);  

					$emp_code_name		=trim($data[5]);

					if($folderName=='MAITHAN')

					{

						$sqlemparray="SELECT emp_name FROM employee_master WHERE HQ='".$route_name."'";

						$rsemparray=mysqli_query($conn,$sqlemparray);

						$emp_code_name_array=array();

						while($rowemparray=mysqli_fetch_array($rsemparray))

						{

							array_push($emp_code_name_array,$rowemparray['emp_name']);

						}

					}

					else

					{

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					 $emp_code_name_array=explode(',',$emp_code_name);

					}

					

					//print_r($emp_code_name_array);



					if(providing_code == 'yes')

					{

						foreach($emp_code_name_array as $emp_code_name_values)

						{

							$sql_check_dns_code = "SELECT emp_code FROM employee_master WHERE dns_emp_code = '".$emp_code_name_values."'";

							$res_check_dns_code = mysqli_query($conn,$sql_check_dns_code);

							$total_rows = mysqli_num_rows($res_check_dns_code);

							if($total_rows == 0)

							{

								echo "Please provide proper DNS Employee Code at row ".($csv_row_count+1);

								die;

							}

						}

						if($route_name != '' && $dns_route_code == '')

						{

							echo "Please provide dns route code at row ".($csv_row_count+1);

						}

					}

					else

					{

						foreach($emp_code_name_array as $emp_code_name_values)

						{

							$sql_check_emp = "SELECT emp_code FROM employee_master WHERE emp_name = '".$emp_code_name_values."'";

							$res_check_emp = mysqli_query($conn,$sql_check_emp);

							$total_rows = mysqli_num_rows($res_check_emp);

							if($total_rows == 0)

							{

								echo "Please provide proper employee at row ".($csv_row_count+1);

								die;

							}

						}

					}

					//For VIPL employee only

					/*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_code)."'";

					$rsempnamechk=mysqli_query($conn,$sqlempnamechk);

					$rowempnamechk=mysqli_fetch_array($rsempnamechk);

					$emp_code=$rowempnamechk['emp_code'];*/

					$acedns		  =trim($data[6]);

					if($acedns =='')

					{

						echo "Please provide value for Acedns at row ".($csv_row_count+1);

						die;

					}

					

					$credit_limit	=trim($data[7]);

					$credit_days	 =trim($data[8]);

					$current_balance =trim($data[9]);

					$black_list	  =trim($data[10]); 

					if($black_list=='')

					{

						echo "Please provide value for Blacklist at row ".($csv_row_count+1);

						die;

					}

					

					$TD	  		  =trim($data[11]);

					$branch_code_name =trim($data[12]);

					$customer_type   =trim($data[13]);

					$rds_tag   =trim($data[14]);

					if(retailer_app=='yes'){

					 if(strpos($rds_tag,';')!=false)

					 {

						$rds_tag=str_replace(';',',',$rds_tag);

					 }

					 $emp_code_name_array=explode(',',$rds_tag);

					}

					$sauda_validity_period  =trim($data[15]);

					$address  =trim($data[16]);

					$landline_no  =trim($data[17]);

					$owner_name  =trim($data[18]);

					$owner_phone  =trim($data[19]);

					$cust_class  =trim($data[20]);

					$weekly_closing_day  =trim($data[21]);

					$coverage_type  =trim($data[22]);

					$TIN  =trim($data[23]);

					$PAN  =trim($data[24]);

					$district  =trim($data[25]);

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim($data[27]);

					$bank_account_number  =trim($data[28]);

					$email  =trim($data[29]);

					$visit_day  =trim($data[30]);

					$state  =trim($data[31]);

					$monthly_potential  =trim($data[32]);

					

					foreach($emp_code_name_array as $emp_code_name_value_next)

					{

						if(providing_code=='yes'){

							$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";

							$rsempcode=mysqli_query($conn,$sqlempcode);

							$rowempcode=mysqli_fetch_array($rsempcode);

							$emp_code=$rowempcode['emp_code'];

							

							$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

							$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

							$rowbranchcode=mysqli_fetch_array($rsbranchcode);

							$branch_code=$rowbranchcode['branch_code'];

						}

						else

						{

							$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";

							$rsempcode=mysqli_query($conn,$sqlempcode);

							$rowempcode=mysqli_fetch_array($rsempcode);

							$emp_code=$rowempcode['emp_code'];

							

							//exit();

							$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";

							$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

							$rowbranchcode=mysqli_fetch_array($rsbranchcode);

							$branch_code=$rowbranchcode['branch_code'];

						}

					 //$emp_code=$emp_code_name;

					 	if(providing_code=='yes'){

							$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";

						}

						else

						{

							$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' 

										and emp_code='".$emp_code."'";

						}

						$rsrdscode=mysqli_query($conn,$sqlrdscode);

						$rowrdscode=mysqli_fetch_array($rsrdscode);

						$rds_code=$rowrdscode['customer_code'];



						if(providing_code=='yes'){

							$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."' AND emp_code='".$emp_code."'";

						}

						else

						{

							$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$emp_code."'";

						}

						$rsroutechk=mysqli_query($conn,$sqlroutechk);

						$countroutechk=mysqli_num_rows($rsroutechk);

						if($countroutechk<1 && $route_name!='')

						{

							$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";

							$rsmaxroutecode=mysqli_query($conn,$sqlmaxroutecode);

							$rowmaxroutecode=mysqli_fetch_array($rsmaxroutecode);

							$new_route_code=$rowmaxroutecode['new_route_code'];

							

							if($new_route_code=='')

							{

								$max_route_code='RT/1';

							}

							else

							{

								$max_route_code='RT/'.($new_route_code+1);

								//$max_route_code++;

							}

	

							$sqlroute  = "insert into route_master ";

							$sqlroute .= " SET route_code='".$max_route_code."'";

							$sqlroute .= " ,dns_route_code='".$dns_route_code."'";

							$sqlroute .= " ,route_name='".$route_name."'";

							$sqlroute .= " , emp_code='".$emp_code."'";

							$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

							mysqli_query($conn,$sqlroute) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on route table.PLease contact aceDNS admin.");

						

							modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

							$route_code=$max_route_code;

						}

						else

						{

							$rowroutechk=mysqli_fetch_array($rsroutechk);

							$route_code=$rowroutechk['route_code'];

						}



						if(providing_code=='yes'){

						   $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."' AND 

											emp_code='".$emp_code."' AND route_code='".$route_code."'";

						}

						else

						{

						$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' 

										AND emp_code='".$emp_code."' AND route_code='".$route_code."'";

						}

					/*$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";*/

					$rscustomernamechk=mysqli_query($conn,$sqlcustomernamechk);

					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);

					

					if($countcustomernamechk<1)

					{

						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";

						$rsmaxcustomercode=mysqli_query($conn,$sqlmaxcustomercode);

						$rowmaxcustomercode=mysqli_fetch_array($rsmaxcustomercode);

						$max_customer_code=$rowmaxcustomercode['max_customer_code'];

						

						if($max_customer_code=='')

						{

							$max_customer_code='C/0000001';

						}

						else

						{

							$max_customer_code++;

						}

						$sql  = "insert into customer_master ";

						$sql .= " SET customer_code='".$max_customer_code."'";

						$sql .= " , dns_customer_code='".$dns_customer_code."'";

						$sql .= " , customer_name='".addslashes($customer_name)."'";

						$sql .= " , branch_code='".addslashes($branch_code)."'";

						$sql .= " , phone_no='".addslashes($phone_no)."'";

						$sql .= " , route_code='".addslashes($route_code)."'";

						$sql .= " , emp_code='".addslashes($emp_code)."'";

						$sql .= " , current_balance	='".addslashes($current_balance)."'";

						$sql .= " , credit_limit='".addslashes($credit_limit)."'";

						$sql .= " , credit_days='".addslashes($credit_days)."'";

						$sql .= " , acedns='".addslashes($acedns)."'";

						$sql .= " , black_list='".addslashes($black_list)."'";

						$sql .= " , TD='".addslashes($TD)."'";

						$sql .= " , rds_tag='".addslashes($rds_code)."'";

						$sql .= " , cust_type='".addslashes($customer_type)."'";

						$sql .= " , sauda_validity_period='".addslashes($sauda_validity_period)."'";

						$sql .= " , address='".addslashes($address)."'";

						$sql .= " , owner_name='".addslashes($owner_name)."'";

						$sql .= " , owner_phone='".addslashes($owner_phone)."'";

						$sql .= " , cust_class='".addslashes($cust_class)."'";

						$sql .= " , weekly_closing_day='".addslashes($weekly_closing_day)."'";

						$sql .= " , TIN='".addslashes($TIN)."'";

						$sql .= " , PAN='".addslashes($PAN)."'";

						$sql .= " , district='".addslashes($district)."'";

						$sql .= " , landline_no='".addslashes($landline_no)."'";

						$sql .= " , minimum_stock='".addslashes($minimum_stock)."'";

						$sql .= " , bank_name='".addslashes($bank_name)."'";

						$sql .= " , bank_account_number='".addslashes($bank_account_number)."'";

						$sql .= " , email='".addslashes($email)."'";

						$sql .= " , visit_day='".addslashes($visit_day)."'";

						$sql .= " , state_code='".addslashes($state)."'";

						$sql .= " , monthly_potential='".addslashes($monthly_potential)."'";

						$sql .= " , coverage_type='".addslashes($coverage_type)."'";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						//exit();

						/*mysqli_query($conn,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");*/

$cm = mysqli_query($conn,$sql);

if(!$cm){

echo mysqli_error();

}

						modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						$customer_code=$max_customer_code;

					}

					else

					{

						$rowcustomernamechk=mysqli_fetch_array($rscustomernamechk);

						$customer_code_db=$rowcustomernamechk['customer_code'];

						$route_code_db=$rowcustomernamechk['route_code'];

						$emp_code_db=$rowcustomernamechk['emp_code'];

						$current_balance_db=$rowcustomernamechk['current_balance'];

						$credit_limit_db=$rowcustomernamechk['credit_limit'];

						$credit_days_db=$rowcustomernamechk['credit_days'];

						$acedns_db=$rowcustomernamechk['acedns'];

						$black_list_db=$rowcustomernamechk['black_list'];

						$TD_db=$rowcustomernamechk['TD'];

						$customer_type_db=$rowcustomernamechk['cust_type'];

						$rds_tag_db=$rowcustomernamechk['rds_tag'];

						$branch_code_db=$rowcustomernamechk['branch_code'];

						$sauda_validity_period_db=$rowcustomernamechk['sauda_validity_period'];						

						$customer_name_db=$rowcustomernamechk['customer_name'];

						$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];

						$address_db=$rowcustomernamechk['address'];

						$owner_name_db=$rowcustomernamechk['owner_name'];

						$owner_phone_db=$rowcustomernamechk['owner_phone'];

						$cust_class_db=$rowcustomernamechk['cust_class'];

						$weekly_closing_day_db=$rowcustomernamechk['weekly_closing_day'];

						$TIN_db=$rowcustomernamechk['TIN'];

						$PAN_db=$rowcustomernamechk['PAN'];

						$district_db=$rowcustomernamechk['district'];

						$landline_no_db=$rowcustomernamechk['landline_no'];

						$minimum_stock_db=$rowcustomernamechk['minimum_stock'];

						$bank_name_db=$rowcustomernamechk['bank_name'];

						$bank_account_number_db=$rowcustomernamechk['bank_account_number'];

						$email_db=$rowcustomernamechk['email'];

						$visit_day_db=$rowcustomernamechk['visit_day'];

						$coverage_type_db  =$rowcustomernamechk['coverage_type'];

						$state_code_db=$rowcustomernamechk['state_code'];

						$monthly_potential_db  =$rowcustomernamechk['monthly_potential'];

						if(providing_code=='yes'){

							$update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";

						}

						else

						{

							$update_condition=" customer_name='".addslashes($customer_name)."'";

						}

						if($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list || $TD_db!=$TD || $customer_type_db!=$customer_type || $rds_tag_db!=$rds_code 

						|| $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days 

						|| $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no || $address_db!=$address || $owner_name_db!=$owner_name || $owner_phone_db!=$owner_phone || $cust_class_db!=$cust_class || $weekly_closing_day_db!=$weekly_closing_day || $TIN_db!=$TIN || $PAN_db!=$PAN || $district_db!=$district || $landline_no_db!=$landline_no || $minimum_stock_db!=$minimum_stock || $bank_name_db!=$bank_name || $bank_account_number_db!=$bank_account_number || $email_db!=$email || $visit_day_db!=$visit_day 

						|| $coverage_type_db!=$coverage_type || $monthly_potential_db!=$monthly_potential)

						{

							$sqlupdated  = "update customer_master ";

							$sqlupdated .= " SET route_code='".$route_code."'";

							$sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";

							$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";

							$sqlupdated .= " , current_balance	='".addslashes($current_balance)."'";

							$sqlupdated .= " , acedns='".addslashes($acedns)."'";

							$sqlupdated .= " , branch_code='".addslashes($branch_code)."'";

							$sqlupdated .= " , TD='".addslashes($TD)."'";

							$sqlupdated .= " , cust_type='".addslashes($customer_type)."'";

						    $sqlupdated .= " , credit_days='".addslashes($credit_days)."'";

							$sqlupdated .= " , sauda_validity_period='".addslashes($sauda_validity_period)."'";

							$sqlupdated .= " , address='".addslashes($address)."'";

							$sqlupdated .= " , owner_name='".addslashes($owner_name)."'";

							$sqlupdated .= " , owner_phone='".addslashes($owner_phone)."'";

							$sqlupdated .= " , cust_class='".addslashes($cust_class)."'";

							$sqlupdated .= " , weekly_closing_day='".addslashes($weekly_closing_day)."'";

							$sqlupdated .= " , TIN='".addslashes($TIN)."'";

							$sqlupdated .= " , PAN='".addslashes($PAN)."'";

							$sqlupdated .= " , district='".addslashes($district)."'";

							$sqlupdated .= " , landline_no='".addslashes($landline_no)."'";

							$sqlupdated .= " , minimum_stock='".addslashes($minimum_stock)."'";

							$sqlupdated .= " , bank_name='".addslashes($bank_name)."'";

							$sqlupdated .= " , bank_account_number='".addslashes($bank_account_number)."'";

							$sqlupdated .= " , email='".addslashes($email)."'";

							$sqlupdated .= " , rds_tag='".addslashes($rds_code)."',visit_day='".addslashes($visit_day)."',

												coverage_type='".addslashes($coverage_type)."',

												state_code='".addslashes($state)."',

												monthly_potential='".addslashes($monthly_potential)."',

												download_time=CURRENT_TIMESTAMP() 

											 WHERE  ".$update_condition." AND emp_code='".$emp_code."' AND route_code='".$route_code."' ";

							/*mysqli_query($conn,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");*/

$cm = mysqli_query($conn,$sqlupdated);

if(!$cm){

echo mysqli_error();

}

							modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						}

						if(($credit_limit_db!=$credit_limit))

						{

							$sqlupdatedcredit  = "update customer_master ";

							$sqlupdatedcredit .= " SET credit_limit='".$credit_limit."'";

							$sqlupdatedcredit .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 

											 WHERE ".$update_condition." AND emp_code='".$emp_code."' AND route_code='".$route_code."'";

							/*mysqli_query($conn,$sqlupdatedcredit) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");*/

$cm = mysqli_query($conn,$sqlupdatedcredit);

if(!$cm){

echo mysqli_error();

}

							



							modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						}

						$customer_code=$customer_code_db;

					}//End of else

					//For Distributor route creation

					  if(distributor_route_planning=='yes')

					   {

						   $sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE distributor_code='".$customer_code."',

						   							route_code='".$route_code."',emp_code='".$emp_code."'";

						   $rschkdistributorroute=mysqli_query($conn,$sqlchkdistributorroute);

						   $countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);

						   if($countchkdistributorroute==0)

						   {						

							   $sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$customer_code."',

														route_code='".$route_code."',emp_code='".$emp_code."',download_time=CURRENT_TIMESTAMP()";

							   $rsinsertdistributorroute=mysqli_query($conn,$sqlinsertdistributorroute);

						   }

					   }

					//End of Distributor route creation

				}//End of emp code name foreach

				//exit();

			 }//End of IF

				 $rec_count++;

		}//End of main foreach

			//exit();

			//Emp code checking start

				$sqlempcoderetail="SELECT emp_code FROM customer_master WHERE emp_code NOT IN(SELECT emp_code FROM employee_master)";

				$rsempcoderetail=mysqli_query($conn,$sqlempcoderetail);

				$cntempcoderetail=mysqli_num_rows($rsempcoderetail);

				if($cntempcoderetail>0)

				{

					$empcoderetail='';

					while($rowempcoderetail=mysqli_fetch_array($rsempcoderetail))

					{

						$empcoderetail=$empcoderetail.$rowempcoderetail['emp_code'].',';

					}

					$empcoderetail=substr($empcoderetail,0,-1);

					if($folderName=='RUPA')

					{

						$errorempcoderetail='There are many employee exists in customer_master but not exists in employee_master.';

					}

					else

					{

						$errorempcoderetail=$empcoderetail.' exists in customer_master but not exists in employee_master.';

					}

					array_push($error_array,$errorempcoderetail);

				}

			//Emp code checking end

			//Route code checking start

				$sqlroutecoderetail="SELECT route_code FROM customer_master WHERE route_code NOT IN(SELECT route_code FROM route_master)";

				$rsroutecoderetail=mysqli_query($conn,$sqlroutecoderetail);

				$cntroutecoderetail=mysqli_num_rows($rsroutecoderetail);

				if($cntroutecoderetail>0)

				{

					$routecoderetail='';

					while($rowroutecoderetail=mysqli_fetch_array($rsroutecoderetail))

					{

						$routecoderetail=$routecoderetail.$rowroutecoderetail['route_code'].',';

					}

					$routecoderetail=substr($routecoderetail,0,-1);

					if($folderName=='RUPA')

					{

						$errorempcoderetail='There are many route exists in customer_master but not exists in route_master.';

					}

					else

					{

						$errorroutecoderetail=$routecoderetail.' exists in customer_master but not exists in route_master.';

					}

					array_push($error_array,$errorroutecoderetail);

				}

			//Route code checking end

			}

			else

			{

			  foreach($lines as $line)

			  {

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   $data[]=$value;

					$dns_customer_code =trim($data[0]);

					$customer_name	=trim($data[1]);

					$phone_no		=trim($data[2]);

					$dns_route_code	  =trim($data[3]);

					$route_name	  =trim($data[4]);  

					$emp_code_name		=trim($data[5]);

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					 $emp_code_name_array=explode(',',$emp_code_name);

					$acedns		  =trim($data[6]);

					$credit_limit	=trim($data[7]);

					$credit_days	 =trim($data[8]);

					$current_balance =trim($data[9]);

					$black_list	  =trim($data[10]); 

					$TD	  		  =trim($data[11]);

					$branch_code_name =trim($data[12]);

					$customer_type   =trim($data[13]);

					$rds_tag   =trim($data[14]);

					//echo retailer_app;

					if(strtoupper($folderName)=='START' && strtoupper($customer_type)=='SUB DEALER'){

					 if(strpos($rds_tag,';')!=false)

					 {

						$rds_tag=str_replace(';',',',$rds_tag);

					 }

					 $emp_code_name_array=explode(',',$rds_tag);

					}

					if(strtoupper($folderName)=='START' ){

					 $emp_code_name_array=explode(',',$dns_customer_code);

					}

					$sauda_validity_period  =trim($data[15]);

					$address  =trim($data[16]);

					$landline_no  =trim($data[17]);

					$owner_name  =trim($data[18]);

					$owner_phone  =trim($data[19]);

					$cust_class  =trim($data[20]);

					$weekly_closing_day  =trim($data[21]);

					$coverage_type  =trim($data[22]);

					$TIN  =trim($data[23]);

					$PAN  =trim($data[24]);

					$district  =trim($data[25]);

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim($data[27]);

					$bank_account_number  =trim($data[28]);

					$email  =trim($data[29]);

					$visit_day  =trim($data[30]);

					$state  =trim($data[31]);

					$monthly_potential  =trim($data[32]);



					$mapped_emp_code_string='';

					if(strtoupper($folderName)=='START' && strtoupper($customer_type)=='DEALER')
					{

					  $branch_code='';

					  if(providing_code=='yes'){

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";

						}

						else

						{

							$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(branch_name,'".$branch_code_name."')";

						}

						$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

						while($rowbranchcode=mysqli_fetch_array($rsbranchcode))

						{

							$branch_code=$branch_code.$rowbranchcode['branch_code'].',';

						}

						$branch_code=substr($branch_code,0,-1);

					  if(providing_code=='yes'){

						 $sqlempnamechk="SELECT emp_code,acedns FROM employee_master WHERE dns_emp_code='".$dns_customer_code."'";

						}

						else{

							$sqlempnamechk="SELECT emp_code,acedns FROM employee_master WHERE emp_name='".addslashes($customer_name)."'";

						}

						$rsempnamechk=mysqli_query($conn,$sqlempnamechk);

						$countempnamechk=mysqli_num_rows($rsempnamechk);

						$csv_row_count=$rec_count+1;

						if($countempnamechk<1 && $acedns=='Y')

						{

						$sqlmaxempcode="SELECT MAX(emp_code) AS max_emp_code FROM  employee_master ";

						$rsmaxempcode=mysqli_query($conn,$sqlmaxempcode);

						$rowmaxempcode=mysqli_fetch_array($rsmaxempcode);

						$max_emp_code=$rowmaxempcode['max_emp_code'];

						if($max_emp_code=='')
						{
							$max_emp_code='E0001';

						}
						else

						{

							$max_emp_code++;
						}
						$sql  = "insert into employee_master ";

						$sql .= " SET emp_code='".$max_emp_code."'";

						$sql .= " , dns_emp_code='".$dns_customer_code."'";

						$sql .= " , emp_name='".ltrim(addslashes($customer_name))."'";

						$sql .= " , branch_code='".$branch_code."'";

						$sql .= " , vertical_value=''";

						$sql .= " , reporting_to=''";

						$sql .= " , email=''";

						$sql .= " , phone_no='".$phone_no."'";

						$sql .= " , sale_access='PRIMARY'";

						$sql .= " , HQ=''";

						$sql .= " , designation='DEALER'";

						$sql .= " , acedns='Y'";

						$sql .= " , app_access='Y'";

						$sql .= " , state='".$state."'";

						$sql .= " , zone=''";

						$sql .= " , DOJ=CURDATE()";

						$sql .= " , District=''";

						$sql .= " , sms_otp=''";

						$sql .= " , acedns_changed_date=CURRENT_TIMESTAMP()";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sql) or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Employee code column in Employee Master.csv.Please check.");

						

						$sqlchkchangepassword="SELECT emp_code FROM changepassword WHERE emp_code='".$max_emp_code."'";

						$rschkchangepassword=mysqli_query($conn,$sqlchkchangepassword);

						$countchkchangepassword=mysqli_num_rows($rschkchangepassword);

						if($countchkchangepassword==0)

						{

							$sqlcp  = "insert into changepassword ";

							$sqlcp .= " SET emp_code='".$max_emp_code."'";

							$sqlcp .= " , newpassword='1234'";

							$sqlcp .= " , oldpassword='1234'"; 

							$sqlcp .= " , status='true'";

							$sqlcp .= " , is_licensed='1'"; 

							$sqlcp .= " , deviceid=''";

							mysqli_query($conn,$sqlcp) or  array_push($error_array,"mysqli_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");

						}

					}
					else
					{
					    $rowempnamechk=mysqli_fetch_array($rsempnamechk);
					    $emp_code_db=$rowempnamechk['emp_code'];
						$acedns_db=$rowempnamechk['acedns'];

						$sqlupdate  = "UPDATE employee_master ";

						$sqlupdate .= " SET branch_code='".$branch_code."'";

						$sqlupdate .= " , download_time=CURRENT_TIMESTAMP()";

						$sqlupdate .= " , phone_no='".$phone_no."',emp_name='".addslashes($customer_name)."',acedns='".$acedns."'  
										WHERE emp_code='".addslashes($emp_code_db)."'";

						mysqli_query($conn,$sqlupdate) or  array_push($error_array,"mysqli_error().Internel error  @row $csv_row_count on in Employee Master.csv.Please check.");

					}

				}

					//For employee code and branch code

					foreach($emp_code_name_array as $emp_code_name_value_next)

					{

					if(providing_code=='yes'){

						$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";

						$rsempcode=mysqli_query($conn,$sqlempcode);

						$rowempcode=mysqli_fetch_array($rsempcode);

						$emp_code=$rowempcode['emp_code'];

						

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

						$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

						$rowbranchcode=mysqli_fetch_array($rsbranchcode);

						$branch_code=$rowbranchcode['branch_code'];

					}

					else

					{

						$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";

						$rsempcode=mysqli_query($conn,$sqlempcode);

						$rowempcode=mysqli_fetch_array($rsempcode);

						$emp_code=$rowempcode['emp_code'];

						

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";

						$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

						$rowbranchcode=mysqli_fetch_array($rsbranchcode);

						$branch_code=$rowbranchcode['branch_code'];

					}

					//For distributor tagged

					if(providing_code=='yes'){

						$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";

					}

					else

					{

						$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y' 

									AND cust_type='D'";

					}

					$rsrdscode=mysqli_query($conn,$sqlrdscode);

					$rowrdscode=mysqli_fetch_array($rsrdscode);

					$rds_code=$rowrdscode['customer_code'];

					

					//For route

					if(providing_code=='yes'){

						$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."'";

					}

					else

					{

						$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";

					}

					$rsroutechk=mysqli_query($conn,$sqlroutechk);

					$countroutechk=mysqli_num_rows($rsroutechk);

					if($countroutechk<1 && $route_name!='')

					{

						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";

						$rsmaxroutecode=mysqli_query($conn,$sqlmaxroutecode);

						$rowmaxroutecode=mysqli_fetch_array($rsmaxroutecode);

						$new_route_code=$rowmaxroutecode['new_route_code'];

						

						if($new_route_code=='')

						{

							$max_route_code='RT/1';

						}

						else

						{

							$max_route_code='RT/'.($new_route_code+1);

						}

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";

						$sqlroute .= " ,route_name='".$route_name."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlroute) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				

						//modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						$route_code=$max_route_code;

					}

					else

					{

						$rowroutechk=mysqli_fetch_array($rsroutechk);

						$route_code=$rowroutechk['route_code'];

						$route_name_db=$rowroutechk['route_name'];

						if($route_name_db !=$route_name)

						{

							$sqlupdateroue="UPDATE route_master SET route_name='".$route_name."',download_time=CURRENT_TIMESTAMP() 

											WHERE route_code='".$route_code."'";

							mysqli_query($conn,$sqlupdateroue) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");

						}

					}

					//For customer

					if(providing_code=='yes'){

					  // $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."' AND route_code='".$route_code."'";

					    $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";

					}

					else

					{

					 $sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."' AND acedns='Y'";

					 //$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";

					}

					$rscustomernamechk=mysqli_query($conn,$sqlcustomernamechk);

					$countcustomernamechk=mysqli_num_rows($rscustomernamechk);

					

					$csv_row_count=$rec_count+1;

					if($countcustomernamechk<1)

					{

						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";

						$rsmaxcustomercode=mysqli_query($conn,$sqlmaxcustomercode);

						$rowmaxcustomercode=mysqli_fetch_array($rsmaxcustomercode);

						$max_customer_code=$rowmaxcustomercode['max_customer_code'];

						

						if($max_customer_code=='')

						{

							$max_customer_code='C/0000001';

						}

						else

						{

							$max_customer_code++;

						}

						$sql  = "insert into customer_master ";

						$sql .= " SET customer_code='".$max_customer_code."'";

						$sql .= " , dns_customer_code='".$dns_customer_code."'";

						$sql .= " , customer_name='".addslashes($customer_name)."'";

						$sql .= " , branch_code='".addslashes($branch_code)."'";

						$sql .= " , phone_no='".addslashes($phone_no)."'";

						$sql .= " , route_code='".addslashes($route_code)."'";

						$sql .= " , current_balance	='".addslashes($current_balance)."'";

						$sql .= " , credit_limit='".addslashes($credit_limit)."'";

						$sql .= " , credit_days='".addslashes($credit_days)."'";

						$sql .= " , acedns='Y'";

						$sql .= " , black_list='N'";

						$sql .= " , TD='".addslashes($TD)."'";

						$sql .= " , rds_tag='".addslashes($rds_code)."'";

						$sql .= " , cust_type='".addslashes($customer_type)."'";

						$sql .= " , sauda_validity_period='".addslashes($sauda_validity_period)."'";

						$sql .= " , address='".addslashes($address)."'";

						$sql .= " , owner_name='".addslashes($owner_name)."'";

						$sql .= " , owner_phone='".addslashes($owner_phone)."'";

						$sql .= " , cust_class='".addslashes($cust_class)."'";

						$sql .= " , weekly_closing_day='".addslashes($weekly_closing_day)."'";

						$sql .= " , TIN='".addslashes($TIN)."'";

						$sql .= " , PAN='".addslashes($PAN)."'";

						$sql .= " , district='".addslashes($district)."'";

						$sql .= " , landline_no='".addslashes($landline_no)."'";

						$sql .= " , minimum_stock='".addslashes($minimum_stock)."'";

						$sql .= " , bank_name='".addslashes($bank_name)."'";

						$sql .= " , bank_account_number='".addslashes($bank_account_number)."'";

						$sql .= " , email='".addslashes($email)."'";

						$sql .= " , visit_day='".addslashes($visit_day)."'";

						$sql .= " , state_code='".addslashes($state)."'";

						$sql .= " , monthly_potential='".addslashes($monthly_potential)."'";

						$sql .= " , coverage_type='".addslashes($coverage_type)."'";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						/*mysqli_query($conn,$sql) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");*/

$cm = mysqli_query($conn,$sql);

if(!$cm){

echo mysqli_error();

}

					   //modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						$customer_code=$max_customer_code;

					}

					else

					{

						/*if($folderName=='HALDIRAM')

						{

							$sqltest  = "insert into customer_master_test ";

							$sqltest .= " SET customer_name='".addslashes($customer_name)."'";

							$sqltest .= " , 	route_code='".addslashes($route_name)."'";

							$sqltest .= " , emp_code='".addslashes($emp_code_name)."'";

							

							mysqli_query($conn,$sqltest);

						}*/

						

						$rowcustomernamechk=mysqli_fetch_array($rscustomernamechk);

						$customer_code_db=$rowcustomernamechk['customer_code'];

						$route_code_db=$rowcustomernamechk['route_code'];

						$current_balance_db=$rowcustomernamechk['current_balance'];

						$phone_no_db=$rowcustomernamechk['phone_no'];

						$credit_limit_db=$rowcustomernamechk['credit_limit'];

						$credit_days_db=$rowcustomernamechk['credit_days'];

						$acedns_db=$rowcustomernamechk['acedns'];

						$black_list_db=$rowcustomernamechk['black_list'];

						$TD_db=$rowcustomernamechk['TD'];

						$customer_type_db=$rowcustomernamechk['cust_type'];

						$rds_tag_db=$rowcustomernamechk['rds_tag'];

						$branch_code_db=$rowcustomernamechk['branch_code'];

						$sauda_validity_period_db=$rowcustomernamechk['sauda_validity_period'];

						$customer_name_db=$rowcustomernamechk['customer_name'];

						$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];

						$address_db=$rowcustomernamechk['address'];

						$owner_name_db=$rowcustomernamechk['owner_name'];

						$owner_phone_db=$rowcustomernamechk['owner_phone'];

						$cust_class_db=$rowcustomernamechk['cust_class'];

						$weekly_closing_day_db=$rowcustomernamechk['weekly_closing_day'];

						$TIN_db=$rowcustomernamechk['TIN'];

						$PAN_db=$rowcustomernamechk['PAN'];

						$district_db=$rowcustomernamechk['district'];

						$zone_db=$rowcustomernamechk['zone'];

						$landline_no_db=$rowcustomernamechk['landline_no'];

						$minimum_stock_db=$rowcustomernamechk['minimum_stock'];

						$bank_name_db=$rowcustomernamechk['bank_name'];

						$bank_account_number_db=$rowcustomernamechk['bank_account_number'];

						$email_db=$rowcustomernamechk['email'];

						$visit_day_db=$rowcustomernamechk['visit_day'];

						$coverage_type_db=$rowcustomernamechk['coverage_type'];

						$state_code_db=$rowcustomernamechk['state_code'];

						$monthly_potential_db  =$rowcustomernamechk['monthly_potential'];



						if(providing_code=='yes'){

							$update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";

						}

						else

						{

							$update_condition=" customer_name='".addslashes($customer_name)."' AND route_code='".$route_code."'";

						}



						if($route_code_db!=$route_code || $current_balance_db!=$current_balance || $TD_db!=$TD || $customer_type_db!=$customer_type 

						|| $rds_tag_db!=$rds_code 

						|| $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days 

						|| $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no 

						|| $address_db!=$address || $owner_name_db!=$owner_name || $owner_phone_db!=$owner_phone || $cust_class_db!=$cust_class 

						|| $weekly_closing_day_db!=$weekly_closing_day || $TIN_db!=$TIN || $PAN_db!=$PAN || $district_db!=$district 

						|| $landline_no_db!=$landline_no || $minimum_stock_db!=$minimum_stock || $bank_name_db!=$bank_name 

						|| $bank_account_number_db!=$bank_account_number || $email_db!=$email || $visit_day_db!=$visit_day 

						|| $coverage_type_db!=$coverage_type || $monthly_potential_db!=$monthly_potential || $acedns_db!=$acedns)

						{

							$sqlupdated  = "update customer_master ";

							$sqlupdated .= " SET route_code='".$route_code."'";

							$sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";

							$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";

							$sqlupdated .= " , current_balance	='".addslashes($current_balance)."'";

							$sqlupdated .= " , branch_code='".addslashes($branch_code)."'";

							$sqlupdated .= " , TD='".addslashes($TD)."'";

							$sqlupdated .= " , cust_type='".addslashes($customer_type)."'";

							$sqlupdated .= " , phone_no='".addslashes($phone_no)."'";
							
							$sqlupdated .= " , acedns='".addslashes($acedns)."'";

						    $sqlupdated .= " , credit_days='".addslashes($credit_days)."'";

							$sqlupdated .= " , sauda_validity_period='".addslashes($sauda_validity_period)."'";

							$sqlupdated .= " , address='".addslashes($address)."'";

							$sqlupdated .= " , owner_name='".addslashes($owner_name)."'";

							$sqlupdated .= " , owner_phone='".addslashes($owner_phone)."'";

							$sqlupdated .= " , cust_class='".addslashes($cust_class)."'";

							$sqlupdated .= " , weekly_closing_day='".addslashes($weekly_closing_day)."'";

							$sqlupdated .= " , TIN='".addslashes($TIN)."'";

							$sqlupdated .= " , PAN='".addslashes($PAN)."'";

							$sqlupdated .= " , district='".addslashes($district)."'";

							$sqlupdated .= " , landline_no='".addslashes($landline_no)."'";

							$sqlupdated .= " , minimum_stock='".addslashes($minimum_stock)."'";

							$sqlupdated .= " , bank_name='".addslashes($bank_name)."'";

							$sqlupdated .= " , bank_account_number='".addslashes($bank_account_number)."'";

							$sqlupdated .= " , email='".addslashes($email)."'";

							$sqlupdated .= " , rds_tag='".addslashes($rds_code)."',visit_day='".addslashes($visit_day)."',

												coverage_type='".addslashes($coverage_type)."',

												state_code='".addslashes($state)."',

												monthly_potential='".addslashes($monthly_potential)."',

												download_time=CURRENT_TIMESTAMP() 

											 WHERE  ".$update_condition."";

/*mysqli_query($conn,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");*/

$cm = mysqli_query($conn,$sqlupdated);

if(!$cm){

	echo mysqli_error();

}

							//modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						}

						if(($credit_limit_db!=$credit_limit))

						{

							$sqlupdatedcredit  = "update customer_master ";

							$sqlupdatedcredit .= " SET credit_limit='".$credit_limit."'";

							$sqlupdatedcredit .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 

											 WHERE ".$update_condition."";

							/*mysqli_query($conn,$sqlupdatedcredit) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");*/

$cm = mysqli_query($conn,$sqlupdatedcredit);

if(!$cm){

	echo mysqli_error();

}

						 // modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						}

						$customer_code=$customer_code_db;

					}

					//For Distributor route creation

					  if($folderName=='HALDIRAM' || $folderName=='OSHEA')

					   {

						   if($customer_type=='D')

						   {

							   $sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE 

							   							distributor_code='".$customer_code."' AND emp_code='".$emp_code."'";

								$rschkdistributorroute=mysqli_query($conn,$sqlchkdistributorroute);

								$countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);

							   if($countchkdistributorroute==0)

							   {	

							   $sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$customer_code."',

														route_code='".$route_code."',emp_code='".$emp_code."',download_time=CURRENT_TIMESTAMP()";

							   $rsinsertdistributorroute=mysqli_query($conn,$sqlinsertdistributorroute);

							   }

							   else

							   {

								   $sqlupdatedistributorroute="UPDATE distributor_route_relation SET route_code='".$route_code."',

												acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 

												distributor_code='".$customer_code."' AND emp_code='".$emp_code."'";

									mysqli_query($conn,$sqlupdatedistributorroute);	

							   }

						   }

					   }

					//End of Distributor route creation

					//For customer route relation

					$sqlselcustomerroute="SELECT customer_code,route_code,emp_code FROM customer_route_emp_relation WHERE 

						           customer_code='".$customer_code."'  AND emp_code='".$emp_code."'";

					//exit();

					$rsselcustomerroute=mysqli_query($conn,$sqlselcustomerroute);

					$countcustomerroute=mysqli_num_rows($rsselcustomerroute);

					if($countcustomerroute==0)

					{

					    $sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code."',

												 route_code='".$route_code."',

												emp_code='".$emp_code."',

												acedns='".$acedns."',

												download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlinsertcustomerroute);

					}

					else

					{

						$sqlupdatecustomerroute="UPDATE customer_route_emp_relation SET route_code='".$route_code."',

												acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE 

												customer_code='".$customer_code."' AND emp_code='".$emp_code."'";

						mysqli_query($conn,$sqlupdatecustomerroute);						

					}

					if($folderName=='STAR')

					{

						$mapped_emp_code_string=$mapped_emp_code_string."'".$emp_code."'".',';

					}

					//exit();

					//End for customer route relation

					//For customer branch relation

					/*$branch_code_array=array();

					if(providing_code=='yes'){

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";

					$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

					while($rowbranchcode=mysqli_fetch_array($rsbranchcode))

					{

						$branch_code=$rowbranchcode['branch_code'];

						$sqlcustomerbranch="SELECT customer_code,acedns FROM customer_branch_relation WHERE customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

						$rscustomerbranch=mysqli_query($conn,$sqlcustomerbranch);

						$countcustomerbranch=mysqli_num_rows($rscustomerbranch);

						if($countcustomerbranch <1)

						{

							$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation ";

							$sqlinsertcustomerbranch .= " SET customer_code='".$customer_code."'";

							$sqlinsertcustomerbranch .= " , branch_code='".$branch_code."'";

							$sqlinsertcustomerbranch .= " ,acedns='Y'";

							$sqlinsertcustomerbranch .= " ,download_time=CURRENT_TIMESTAMP()";

							mysqli_query($conn,$sqlinsertcustomerbranch);

							modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						}

						else

						{

							//For acedns  Y

							$rowcustomerbranch=mysqli_fetch_array($rscustomerbranch);

							$acedns_db=$rowcustomerbranch['acedns'];

							if($acedns_db=='N')

							{

								$sqlupdateacednsstatus="UPDATE customer_branch_relation SET acedns='Y',download_time=CURRENT_TIMESTAMP() WHERE 

													customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

								$rsupdateacednsstatus=mysqli_query($conn,$sqlupdateacednsstatus);

								modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

							}

						}

						array_push($branch_code_array,$branch_code);

					}

				}

				else

				{

					$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(branch_name,'".$branch_code_name."')";

					$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

					while($rowbranchcode=mysqli_fetch_array($rsbranchcode))

					{

						$branch_code=$rowbranchcode['branch_code'];

						$sqlcustomerbranch="SELECT customer_code,acedns FROM customer_branch_relation WHERE customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

						$rscustomerbranch=mysqli_query($conn,$sqlcustomerbranch);

						$countcustomerbranch=mysqli_num_rows($rscustomerbranch);

						if($countcustomerbranch <1)

						{

							$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation ";

							$sqlinsertcustomerbranch .= " SET customer_code='".$customer_code."'";

							$sqlinsertcustomerbranch .= " ,branch_code='".$branch_code."'";

							$sqlinsertcustomerbranch .= " ,acedns='Y'";

							$sqlinsertcustomerbranch .= " ,download_time=CURRENT_TIMESTAMP()";

							mysqli_query($conn,$sqlinsertcustomerbranch);

						}

						else

						{

							//For acedns  Y

							$rowcustomerbranch=mysqli_fetch_array($rscustomerbranch);

							$acedns_db=$rowcustomerbranch['acedns'];

							if($acedns_db=='N')

							{

								$sqlupdateacednsstatus="UPDATE customer_branch_relation SET acedns='Y',download_time=CURRENT_TIMESTAMP() WHERE 

														customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

								$rsupdateacednsstatus=mysqli_query($conn,$sqlupdateacednsstatus);

							}

						}

						array_push($branch_code_array,$branch_code);

					}

				}

				//For acedns  N

				$sqlcutomerallbranches="SELECT branch_code FROM customer_branch_relation WHERE customer_code='".$customer_code."'";

				$rscustomerallbranches=mysqli_query($conn,$sqlcutomerallbranches);

				while($rowcustomerallbranches=mysqli_fetch_array($rscustomerallbranches))

				{

					$branch_code_active=$rowcustomerallbranches['branch_code'];

					if(!in_array($branch_code_active,$branch_code_array))

					{

						$sqlupdatecustomerbranch="UPDATE customer_branch_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() WHERE 

												customer_code='".$customer_code."' AND branch_code='".$branch_code_active."'";

						$rsupdatecustomerbranch=mysqli_query($conn,$sqlupdatecustomerbranch);

					}

				}*/

			//End of acedns  N

			//End For customer branch relation	

					}

					if($folderName=='STAR')

					{

						$mapped_emp_code_string_final=substr($mapped_emp_code_string,0,-1);

						$sql_update_customerrouteacedns="UPDATE customer_route_emp_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() 

															WHERE customer_code='".$customer_code."' AND emp_code NOT IN(".$mapped_emp_code_string_final.")";

						mysqli_query($conn,$sql_update_customerrouteacedns);								 

					}

					if($folderName=='ARCHITA')

					{

						//For customer branch relation

							$branch_code_array=array();

							$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$state."')";

							$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

							while($rowbranchcode=mysqli_fetch_array($rsbranchcode))

							{

								$branch_code=$rowbranchcode['branch_code'];

								$sqlcustomerbranch="SELECT customer_code,acedns FROM customer_branch_relation WHERE customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

								$rscustomerbranch=mysqli_query($conn,$sqlcustomerbranch);

								$countcustomerbranch=mysqli_num_rows($rscustomerbranch);

								if($countcustomerbranch <1)

								{

									$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation ";

									$sqlinsertcustomerbranch .= " SET customer_code='".$customer_code."'";

									$sqlinsertcustomerbranch .= " , branch_code='".$branch_code."'";

									$sqlinsertcustomerbranch .= " ,acedns='Y'";

									$sqlinsertcustomerbranch .= " ,download_time=CURRENT_TIMESTAMP()";

									mysqli_query($conn,$sqlinsertcustomerbranch);

								}

								else

								{

									//For acedns  Y

									$rowcustomerbranch=mysqli_fetch_array($rscustomerbranch);

									$acedns_db=$rowcustomerbranch['acedns'];

									if($acedns_db=='N')

									{

										$sqlupdateacednsstatus="UPDATE customer_branch_relation SET acedns='Y',download_time=CURRENT_TIMESTAMP() WHERE 

															customer_code='".$customer_code."' AND branch_code='".$branch_code."'";

										$rsupdateacednsstatus=mysqli_query($conn,$sqlupdateacednsstatus);

									}

								}

								array_push($branch_code_array,$branch_code);

						   }

							//For acedns  N

							$sqlcutomerallbranches="SELECT branch_code FROM customer_branch_relation WHERE customer_code='".$customer_code."'";

							$rscustomerallbranches=mysqli_query($conn,$sqlcutomerallbranches);

							while($rowcustomerallbranches=mysqli_fetch_array($rscustomerallbranches))

							{

								$branch_code_active=$rowcustomerallbranches['branch_code'];

								if(!in_array($branch_code_active,$branch_code_array))

								{

									$sqlupdatecustomerbranch="UPDATE customer_branch_relation SET acedns='N',download_time=CURRENT_TIMESTAMP() WHERE 

															customer_code='".$customer_code."' AND branch_code='".$branch_code_active."'";

									$rsupdatecustomerbranch=mysqli_query($conn,$sqlupdatecustomerbranch);

								}

							}

						//End of acedns  N

					}//End ARCHITA

				  }

					$rec_count++;

				}//End of for loop

			}//End of else

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Customer Master.csv is wrong.";

			exit();

		}*/

		

	

	//For Vendor Master CSV

	if(similar_file_exists("../csv/$folderName/Vendor master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Vendor master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_vendor_code=trim($data[0]);

				$vendor_name=trim($data[1]);

				$emp_code_name=trim($data[2]);

				$rds_code_name=trim($data[3]);

				$branch_code_name=trim($data[4]);

				

				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";

				$rsbranchnamechk=mysqli_query($conn,$sqlbranchnamechk);

				$rowbranchnamechk=mysqli_fetch_array($rsbranchnamechk);

				$branch_code=$rowbranchnamechk['branch_code'];

				

				$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

				$rsempnamechk=mysqli_query($conn,$sqlempnamechk);

				$rowempnamechk=mysqli_fetch_array($rsempnamechk);

				$emp_code=$rowempnamechk['emp_code'];



				$sqlrdscode="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_code_name)."' AND emp_code='".$emp_code."'";

				$rsrdscode=mysqli_query($conn,$sqlrdscode);

				$rowrdscode=mysqli_fetch_array($rsrdscode);

				$rds_code=$rowrdscode['rds_code'];

				

				$sqlvendornamechk="SELECT vendor_code FROM vendor_master WHERE vendor_name='".addslashes($vendor_name)."' 

									AND rds_code='".$rds_code."'";

				$rsvendornamechk=mysqli_query($conn,$sqlvendornamechk);

				$countvendornamechk=mysqli_num_rows($rsvendornamechk);

				

				$csv_row_count=$rec_count+1;

				if($countvendornamechk<1)

				{

					$sqlmaxvendorcode="SELECT MAX(vendor_code) AS max_vendor_code FROM  vendor_master WHERE 1";

					$rsmaxvendorcode=mysqli_query($conn,$sqlmaxvendorcode);

					$rowmaxvendorcode=mysqli_fetch_array($rsmaxvendorcode);

					$max_vendor_code=$rowmaxvendorcode['max_vendor_code'];

					

					if($max_vendor_code=='')

					{

						$max_vendor_code='V0001';

					}

					else

					{

						$max_vendor_code++;

					}



				$sqlvendor  = "insert into vendor_master SET ";

				$sqlvendor .= "  vendor_code='".mysqli_real_escape_string($max_vendor_code)."'";

				$sqlvendor .= "  ,dns_vendor_code='".mysqli_real_escape_string($dns_vendor_code)."'";

				$sqlvendor .= "  ,vendor_name='".mysqli_real_escape_string($vendor_name)."'";

				$sqlvendor .= " , branch_code='".mysqli_real_escape_string($branch_code)."'";

				$sqlvendor .= " , rds_code='".mysqli_real_escape_string($rds_code)."'";

				$sqlvendor .= " , emp_code='".mysqli_real_escape_string($emp_code)."'";

				mysqli_query($conn,$sqlvendor) or array_push($error_array,"mysqli_error().Internal error in Vendor master.csv.Please check.");

				}

			}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}	*/



	//For Outstanding CSV

	if(similar_file_exists("../csv/$folderName/Outstanding.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Outstanding.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

			$lines = file($filename);

			$sqldelete="truncate outstanding";

			$rsdelete=mysqli_query($conn,$sqldelete);

			$customeroutstandingmissmatchArr=array();

			$line='';

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

						if(!$double_coute_found && $char=="\"")

						{  

						

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				  $data[]=$value;

				  //print_r($data);

				  

					$csv_row_count=$rec_count+1;

					$customer_code_name=trim($data[0]);

					if(providing_code=='yes')

					{

						if($folderName=='STAR')

						{

							$emp_code_name=trim($data[1]);

							if(strpos($emp_code_name,';')!=false)

							 {

								$emp_code_name=str_replace(';',',',$emp_code_name);

							 }

							$invoice_id=trim($data[2]);

							$date=trim($data[3]);

							if(strpos($date,'/')!=false){

							 $dateArr=explode('/',$date);

							}

							if(strpos($date,'-')!=false){

							 $dateArr=explode('-',$date);

							}

							if(strlen($dateArr[2])==2)

							{

								$year='20'.$dateArr[2];

							}

							else

							{

								$year=$dateArr[2];

							}

							$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];

							$invoice_amount=trim($data[4]);

							if(strpos($invoice_amount,',')!=false){

								//$invoicepos=strpos($invoice_amount,',');

							//$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);

								$invoice_amount =str_replace(',','',$invoice_amount);

							}

							if(strpos($invoice_amount,' Cr')!=false){

								$invoice_amount='-'.$invoice_amount;

							}

							$due_amount=trim($data[5]);

							if(strpos($due_amount,',')!=false){

							//$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);

							$due_amount =str_replace(',','',$due_amount);

							}

							if(strpos($due_amount,' Cr')!=false){

								$due_amount ='-'.$due_amount;

							}

							//$vertical_value	  =trim($data[6]); 

							$achievement_qty=trim($data[6]);

						}

						else

						{

							//$emp_code_name=trim($data[1]);

							$invoice_id=trim($data[1]);

							$date=trim($data[2]);

							if(strpos($date,'/')!=false){

							 $dateArr=explode('/',$date);

							}

							if(strpos($date,'-')!=false){

							 $dateArr=explode('-',$date);

							}

							if(strlen($dateArr[2])==2)

							{

								$year='20'.$dateArr[2];

							}

							else

							{

								$year=$dateArr[2];

							}

							$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];

							$invoice_amount=trim($data[3]);

							if(strpos($invoice_amount,',')!=false){

								//$invoicepos=strpos($invoice_amount,',');

							//$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);

								$invoice_amount =str_replace(',','',$invoice_amount);

							}

							if(strpos($invoice_amount,' Cr')!=false){

								$invoice_amount='-'.$invoice_amount;

							}

							$due_amount=trim($data[4]);

							if(strpos($due_amount,',')!=false){

							//$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);

							$due_amount =str_replace(',','',$due_amount);

							}

							if(strpos($due_amount,' Cr')!=false){

								$due_amount ='-'.$due_amount;

							}

							$vertical_value	  =trim($data[5]); 

						}

					}

					else

					{

						if($folderName=='ROUNAK')

						{

							$emp_code_name=trim($data[1]);

							$invoice_id=trim($data[2]);

							$date=trim($data[3]);

							$dateArr=explode('/',$date);

							if(strlen($dateArr[2])==2)

							{

								$year='20'.$dateArr[2];

							}

							else

							{

								$year=$dateArr[2];

							}

							$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];

							$invoice_amount=trim($data[4]);

							if(strpos($invoice_amount,',')!=false){

								//$invoicepos=strpos($invoice_amount,',');

							//$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);

								$invoice_amount =str_replace(',','',$invoice_amount);

							}

							if(strpos($invoice_amount,' Cr')!=false){

								$invoice_amount='-'.$invoice_amount;

							}

							$due_amount=trim($data[5]);

							if(strpos($due_amount,',')!=false){

							//$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);

							$due_amount =str_replace(',','',$due_amount);

							}

							if(strpos($due_amount,' Cr')!=false){

								$due_amount ='-'.$due_amount;

							}

							$vertical_value	  =trim($data[6]); 

						}

						else

						{

							//$route_code_name=trim($data[1]);

							$invoice_id=trim($data[1]);

							$date=trim($data[2]);

							$dateArr=explode('/',$date);

							if(strlen($dateArr[2])==2)

							{

								$year='20'.$dateArr[2];

							}

							else

							{

								$year=$dateArr[2];

							}

							$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];

							$invoice_amount=trim($data[3]);

							if(strpos($invoice_amount,',')!=false){

								//$invoicepos=strpos($invoice_amount,',');

							//$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);

								$invoice_amount =str_replace(',','',$invoice_amount);

							}

							if(strpos($invoice_amount,' Cr')!=false){

								$invoice_amount='-'.$invoice_amount;

							}

							$due_amount=trim($data[4]);

							if(strpos($due_amount,',')!=false){

							//$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);

							$due_amount =str_replace(',','',$due_amount);

							}

							if(strpos($due_amount,' Cr')!=false){

								$due_amount ='-'.$due_amount;

							}

							$vertical_value	  =trim($data[5]); 

						}

					}

	

					if($folderName=='STAR')

					 {

						$sqlempcode="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(dns_emp_code, '".$emp_code_name."')";

						$rsempcode=mysqli_query($conn,$sqlempcode);

						while($rowempcode=mysqli_fetch_array($rsempcode))

						{

						   $emp_code=$rowempcode['emp_code'];

						   $sqlcustomercode="SELECT CM.customer_code FROM customer_master CM,customer_route_emp_relation CRR

						   					WHERE CM.customer_code=CRR.customer_code AND CM.dns_customer_code='".$customer_code_name."' 

											AND CRR.emp_code='".$emp_code."'";

						   $rscustomercode=mysqli_query($conn,$sqlcustomercode);

						   $rowcustomercode=mysqli_fetch_array($rscustomercode);

						   $customer_code=$rowcustomercode['customer_code'];



							$sql  = "insert into outstanding ";

							$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";

							$sql .= " ,route_code='".mysqli_real_escape_string($route_code)."'";

							$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";

							$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";

							$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";

							$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";

							mysqli_query($conn,$sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");

						}

					 }

					 else if($folderName=='ROUNAK')

					 {

						$sqlempcode="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name, '".$emp_code_name."')";

						$rsempcode=mysqli_query($conn,$sqlempcode);

						while($rowempcode=mysqli_fetch_array($rsempcode))

						{

						   $emp_code=$rowempcode['emp_code'];

						   $sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".$customer_code_name."' 

											AND emp_code='".$emp_code."'";

						   $rscustomercode=mysqli_query($conn,$sqlcustomercode);

						   $rowcustomercode=mysqli_fetch_array($rscustomercode);

						   $customer_code=$rowcustomercode['customer_code'];



							$sql  = "insert into outstanding ";

							$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";

							$sql .= " ,route_code='".mysqli_real_escape_string($route_code)."'";

							$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";

							$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";

							$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";

							$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";

							mysqli_query($conn,$sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");

						}

					 }

					else

					{

						if(providing_code=='yes'){

							$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";

							$rsempcode=mysqli_query($conn,$sqlempcode);

							$rowempcode=mysqli_fetch_array($rsempcode);

							$emp_code=$rowempcode['emp_code'];

						   $sqlcustomercode="SELECT customer_code,emp_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";

						}

						else

						{

							$sqlcustomercode="SELECT customer_code,emp_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";

						}

						$rscustomercode=mysqli_query($conn,$sqlcustomercode);

						$countcustomercode=mysqli_num_rows($rscustomercode);

						

						if($countcustomercode <1 && !in_array($customer_code_name,$customeroutstandingmissmatchArr))

						{

							$customeroutstandingmissmatch='';

							$customeroutstandingmissmatch.=$customer_code_name.',';

							array_push($customeroutstandingmissmatchArr,$customer_code_name);

							//$lineexcel .= $customeroutstandingmissmatch."\n";

						}

						$rowcustomercode=mysqli_fetch_array($rscustomercode);

						$customer_code=$rowcustomercode['customer_code'];

						$emp_code=$rowcustomercode['emp_code'];

	

						$sql  = "insert into outstanding ";

						$sql .= " SET customer_code='".mysqli_real_escape_string($customer_code)."'";

						$sql .= " ,route_code='".mysqli_real_escape_string($route_code)."'";

						$sql .= " , invoice_id='".mysqli_real_escape_string($invoice_id)."'";

						$sql .= " , date='".mysqli_real_escape_string($finaldate)."'";

						$sql .= " , invoice_amount='".mysqli_real_escape_string($invoice_amount)."'";

						$sql .= " , due_amount='".mysqli_real_escape_string($due_amount)."'";

					

						mysqli_query($conn,$sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");

					}

					/*if($emp_code!='')

					 {

						modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

					 }*/

				 }

				 $rec_count++;

			}

			//exit();

			//Customer code checking start

				//print_r($customeroutstandingmissmatchArr);

				$customeroutstandingmissmatch=substr($customeroutstandingmissmatch,0,-1);

				$errorcustomeroutstanding=$customeroutstandingmissmatch.' exists in outstanding but not exists in customer_master.';

				array_push($error_array,$errorcustomeroutstanding);

				/*$data = str_replace("\r","",$lineexcel);

				

				header("Content-type: application/x-msdownload"); 

				header("Content-Disposition: attachment; filename=customermissmatch.xls"); 

				header("Pragma: no-cache"); 

				header("Expires: 0"); 

				print "$data";*/

			//Customer code checking end		

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Outstanding.csv is wrong.";

			exit();

		}*/

		

	

	//For MRP CSV

	if(similar_file_exists("../csv/$folderName/MRP.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/MRP.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

			$lines = file($filename);

			/*$sqldelete="truncate mrp";

			$rsdelete=mysqli_query($conn,$sqldelete);*/

			$branch_code_array=array();

			foreach($lines as $line)

			{

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

					

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				  $data[]=$value;

				//print_r($data);

				  	$csv_row_count=$rec_count+1;

				    $branch_code_name=trim($data[0]);

					

					if(branch_wise_mrp == 'yes')

					{

						if($branch_code_name == '')

						{

							echo "Please provide valid branch code at row ".$csv_row_count;

							die;

						}

					}

					

					$prod_code_name=trim($data[1]);

					//$brand_code_name=trim($data[2]);

					//$brand_form_code_name=trim($data[3]);

					$dns_mrp_code=trim($data[2]);

					$mrp=trim($data[3]);

					if(strpos($mrp,',')!=false){

						$mrppos=strpos($mrp,',');

					$mrp = substr($mrp,0,$mrppos).substr(strstr($mrp, ","),1);

					}

					$sale_rate=trim($data[4]);

					if(strpos($sale_rate,',')!=false){

						$sale_ratepos=strpos($sale_rate,',');

						$sale_rate = substr($sale_rate,0,$sale_ratepos).substr(strstr($sale_rate, ","),1);

					}



					$vertical_value=trim($data[5]);

					$acedns=trim($data[8]);

					$ws_price=trim($data[9]);

					$distributor_price=trim($data[10]);

					$ss_price=trim($data[11]);

					$depot_price=trim($data[12]);

					$state_code_name=trim($data[13]);

					

					if($folderName=='SHYAM'){

						if($distributor_price==0){

							$distributor_price=$sale_rate;

						}

						if($sale_rate==0){

							$sale_rate=$distributor_price;

						}

					}

				

					if(providing_code=='yes'){

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";

						$sqlstatecode="SELECT state_code FROM state_master WHERE dns_state_code='".$state_code_name."'";

					}

					else

					{

						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";

						$sqlstatecode="SELECT state_code FROM state_master WHERE statename LIKE '%".$state_code_name."%'";

						//$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' 

								//AND product_group_code='".$brand_code_name."' AND product_sub_group_code='".$brand_form_code_name."'";

					}

					$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

					$rowbranchcode=mysqli_fetch_array($rsbranchcode);

					$branch_code=$rowbranchcode['branch_code'];

					

					$rsstatecode=mysqli_query($conn,$sqlstatecode);

					$rowstatecode=mysqli_fetch_array($rsstatecode);

					$state_code=$rowstatecode['state_code'];

					if(providing_code=='yes'){

						if(branch_wise_product=='yes' || $folderName=='DNV')

						{

							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND branch_code='".$branch_code."'";

						}

						else

						{

							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";

						}

					}

					else if($folderName=='HALDIRAM' || $folderName=='SKIPPER')

					{

						$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";

					}

					else

					{

						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";

					}

					$rsprodcode=mysqli_query($conn,$sqlprodcode);

					$rowprodcode=mysqli_fetch_array($rsprodcode);

					$prod_code=$rowprodcode['prod_code'];

					

					/*if($prod_code=='' && !in_array($prod_code_name,$prod_code_array))

					{

						echo $prod_code_name.'<br />';

						array_push($prod_code_array,$prod_code_name);

					}*/

					

					if(uom_wise_mrp=='yes' && branch_wise_mrp=='yes')

					{

						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND 	UOM='".$UOM."' AND branch_code='".$branch_code."'";

					}

					else if(branch_wise_mrp=='yes')

					{

						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND branch_code='".$branch_code."'";

					}

					else if(state_wise_mrp=='yes')

					{

						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND state_code='".$state_code."'";

					}

					else

					{

						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."'";

					}

					$rsmrpchk=mysqli_query($conn,$sqlmrpchk);

					$countmrpchk=mysqli_num_rows($rsmrpchk);

					$csv_row_count=$rec_count+1;

					$insertflag=0;

					$updateflag=0;

					if($countmrpchk<1){

						$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";

						$rsmaxmrpcode=mysqli_query($conn,$sqlmaxmrpcode);

						$rowmaxmrpcode=mysqli_fetch_array($rsmaxmrpcode);

						$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];

						

						if($max_mrp_code=='')

						{

							$max_mrp_code='001';

						}

						else

						{

							$max_mrp_code++;

						}

						$max_mrp_code='z'.$max_mrp_code;



						$sql  = "insert into mrp ";

						$sql .= " SET product_code='".$prod_code."'";

						$sql .= " , branch_code='".$branch_code."'";

						$sql .= " , mrp_code='".$max_mrp_code."'";

						$sql .= " , dns_mrp_code='".$dns_mrp_code."'";

						$sql .= " , mrp='".mysqli_real_escape_string($mrp)."'";

						$sql .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";

						$sql .= " , vertical_value='".mysqli_real_escape_string($vertical_value)."'";

						$sql .= " , UOM='".mysqli_real_escape_string($UOM)."'";

						$sql .= " , acedns='".mysqli_real_escape_string($acedns)."'";

						$sql .= " , state_code='".mysqli_real_escape_string($state_code)."'";

						$sql .= " , ws_rate='".mysqli_real_escape_string($ws_price)."'";

						$sql .= " , distributor_rate='".mysqli_real_escape_string($distributor_price)."'";

						$sql .= " , ss_rate='".mysqli_real_escape_string($ss_price)."'";

						$sql .= " , depot_rate='".mysqli_real_escape_string($depot_price)."'";

						$sql .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sql)  or  array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count on Mrp code  columns in Mrp.csv.Please check.");

						//exit();

						$insertflag=1;

					}

					else

					{

						$rowmrpchk=mysqli_fetch_array($rsmrpchk);

						$mrp_db=$rowmrpchk['mrp'];

						$sale_rate_db=$rowmrpchk['sale_rate'];

						$acedns_db=$rowmrpchk['acedns'];

						$ws_price_db=$rowmrpchk['ws_rate'];

						$distributor_price_db=$rowmrpchk['distributor_rate'];

						$ss_price_db=$rowmrpchk['ss_rate'];

						$depot_price_db=$rowmrpchk['depot_rate'];	

						if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate || $acedns_db!=$acedns || $ws_price_db!=$ws_price || $distributor_price_db!=$distributor_price || $ss_price_db!=$ss_price || $depot_price_db!=$depot_price ){

							$sqlupdate  = "UPDATE mrp ";

							$sqlupdate .= " SET mrp='".mysqli_real_escape_string($mrp)."'";

							$sqlupdate .= " , sale_rate='".mysqli_real_escape_string($sale_rate)."'";

							$sqlupdate .= " , acedns='".mysqli_real_escape_string($acedns)."'";

							$sqlupdate .= " , ws_rate='".mysqli_real_escape_string($ws_price)."'";

							$sqlupdate .= " , distributor_rate='".mysqli_real_escape_string($distributor_price)."'";

							$sqlupdate .= " , ss_rate='".mysqli_real_escape_string($ss_price)."'";

							$sqlupdate .= " , depot_rate='".mysqli_real_escape_string($depot_price)."'";

							$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE product_code='".$prod_code."' 

											AND branch_code='".$branch_code."' AND state_code='".$state_code."'";

									

							mysqli_query($conn,$sqlupdate) or  array_push($error_array,".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");

							$updateflag=1;

						}

					}

					/*if(branch_wise_mrp=='yes' && ($updateflag==1 || $insertflag==1))//Start For emp data download log

					{

						if(!in_array($branch_code,$branch_code_array))

						{

							array_push($branch_code_array,$branch_code);

							$sqlbranchwiseemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$branch_code."', branch_code)";

							$rsbranchwiseemp=mysqli_query($conn,$sqlbranchwiseemp);

							while($rowbranchwiseemp=mysqli_fetch_array($rsbranchwiseemp))

							{

								$emp_code_branchwise=$rowbranchwiseemp['emp_code'];

								modifyempdatadownloadlog($conn,$emp_code_branchwise,strtoupper($folderName));

							}

						}

					}*///End For emp data download log

				}

				 $rec_count++;

			}

			 if(branch_wise_mrp=='no')//Start For emp data download log with no branch tagging

			{

				$emp_code='';

				modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

			}//End For emp data download log with no branch tagging



			//Product code checking start

				$sqlprodcodeprice="SELECT product_code FROM mrp WHERE product_code NOT IN

									(SELECT prod_code FROM product_master) GROUP BY product_code";

				$rsprodcodeprice=mysqli_query($conn,$sqlprodcodeprice);

				$cntprodcodeprice=mysqli_num_rows($rsprodcodeprice);

				if($cntprodcodeprice>0)

				{

					$prodcodeprice='';

					while($rowprodcodeprice=mysqli_fetch_array($rsprodcodeprice))

					{

						$prodcodeprice=$prodcodeprice.$rowprodcodeprice['product_code'].',';

					}

					$prodcodeprice=substr($prodcodeprice,0,-1);

					$errorprodcodeprice=$prodcodeprice.' exists in MRP but not exists in Sku Master.';

					array_push($error_array,$errorprodcodeprice);

				}

			//Product code checking end		

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for MRP.csv is wrong.";

			exit();

		}*/

	//For Destination Master CSV

	if(similar_file_exists("../csv/$folderName/Destination master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Destination master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_destination_code=trim($data[0]);

				$destination_name=trim($data[1]);

				if(providing_code=='yes'){

					$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($dns_destination_code)."'";

				}

				else

				{

					$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE destination_name='".addslashes($destination_name)."'";

				}

				

				$rsdestinationnamechk=mysqli_query($conn,$sqldestinationnamechk);

				$countdestinationnamechk=mysqli_num_rows($rsdestinationnamechk);

				

				$csv_row_count=$rec_count+1;

				if($countdestinationnamechk<1)

				{

					$sqlmaxdestinationcode="SELECT MAX(destination_code) AS max_destination_code FROM  destination_master WHERE 1";

					$rsmaxdestinationcode=mysqli_query($conn,$sqlmaxdestinationcode);

					$rowmaxdestinationcode=mysqli_fetch_array($rsmaxdestinationcode);

					$max_destination_code=$rowmaxdestinationcode['max_destination_code'];

					

					if($max_destination_code=='')

					{

						$max_destination_code='D0001';

					}

					else

					{

						$max_destination_code++;

					}

				

					$sqldestination  = "insert into destination_master SET ";

					$sqldestination .= "   destination_code='".mysqli_real_escape_string($max_destination_code)."'";

					$sqldestination .= " , dns_destination_code='".mysqli_real_escape_string($dns_destination_code)."'";

					$sqldestination .= " , destination_name='".mysqli_real_escape_string($destination_name)."'";

					$sqldestination .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($conn,$sqldestination) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");

					$emp_code='';

					//modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));		

				}

				else

				{

					$rowdestinationnamechk=mysqli_fetch_array($rsdestinationnamechk);

					$destination_code=$rowdestinationnamechk['destination_code'];



					$sqldestination  = "UPDATE destination_master SET ";

					$sqldestination .= "  dns_destination_code='".mysqli_real_escape_string($dns_destination_code)."',

										  destination_name='".addslashes($destination_name)."',

										download_time=CURRENT_TIMESTAMP() WHERE destination_code='".mysqli_real_escape_string($destination_code)."'";

					mysqli_query($conn,$sqldestination) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");

					$emp_code='';

					//modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));		

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Destination master.csv is wrong.";

		exit();

	}*/

	//For Star Dealer Target CSV

	if(similar_file_exists("../csv/$folderName/Star Dealer Target.csv")!=false || similar_file_exists("../csv/$folderName/Dealer Target.csv")!=false)

	{

		if(similar_file_exists("../csv/$folderName/Star Dealer Target.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Star Dealer Target.csv");

		}

		if(similar_file_exists("../csv/$folderName/Dealer Target.csv")!=false)

		{

			$filename=similar_file_exists("../csv/$folderName/Dealer Target.csv");

		}

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_customer_wise";

		$rsdelete=mysqli_query($conn,$sqldelete);*/



		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_customer_code=trim($data[0]);

				$customer_name=trim($data[1]);

				$april_target=str_replace(',','',trim($data[2]));

				

				$april_achievement=str_replace(',','',trim($data[3]));

				$may_target=str_replace(',','',trim($data[4]));

				$may_achievement=str_replace(',','',trim($data[5]));

				$june_target=str_replace(',','',trim($data[6]));

				$june_achievement=str_replace(',','',trim($data[7]));

				$july_target=str_replace(',','',trim($data[8]));

				$july_achievement=str_replace(',','',trim($data[9]));

				$august_target=str_replace(',','',trim($data[10]));

				$august_achievement=str_replace(',','',trim($data[11]));

				$september_target=str_replace(',','',trim($data[12]));

				$september_achievement=str_replace(',','',trim($data[13]));

				$october_target=str_replace(',','',trim($data[14]));

				$october_achievement=str_replace(',','',trim($data[15]));

				$november_target=str_replace(',','',trim($data[16]));

				$november_achievement=str_replace(',','',trim($data[17]));

				$december_target=str_replace(',','',trim($data[18]));

				$december_achievement=str_replace(',','',trim($data[19]));

				$january_target=str_replace(',','',trim($data[20]));

				$january_achievement=str_replace(',','',trim($data[21]));

				$february_target=str_replace(',','',trim($data[22]));

				$february_achievement=str_replace(',','',trim($data[23]));

				$march_target=str_replace(',','',trim($data[24]));

				$march_achievement=str_replace(',','',trim($data[25]));

				

				if(providing_code=='yes'){

				$sqlchkcustomercode="SELECT customer_code FROM self_appraisal_customer_wise WHERE customer_code='".addslashes($dns_customer_code)."'";

				$customer_code=$dns_customer_code;

				}

				else

				{

					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";

					$rscustomercode=mysqli_query($conn,$sqlcustomercode);

					$rowcustomercode=mysqli_fetch_array($rscustomercode);

					$customer_code=$rowcustomercode['customer_code'];

					$sqlchkcustomercode="SELECT customer_code FROM self_appraisal_customer_wise WHERE customer_code='".$customer_code."'";

				}

				$rschkcustomercode=mysqli_query($conn,$sqlchkcustomercode);

				$countchkcustomercode=mysqli_num_rows($rschkcustomercode);

				//$rowchkcustomercode=mysqli_fetch_array($rschkcustomercode);

				//$customer_code=$rowcustomercode['customer_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkcustomercode ==0)

				{

					$sqlselfappraisal  = "insert into self_appraisal_customer_wise SET ";

					$sqlselfappraisal .= "   customer_code='".mysqli_real_escape_string($customer_code)."'";

					$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisal .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisal .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisal .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisal .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisal .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisal .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisal .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisal .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisal .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisal .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisal .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisal .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisal .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($conn,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Star Dealer Target.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_customer_wise SET ";

					$sqlselfappraisalupdate .= "  apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisalupdate .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";

					mysqli_query($conn,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Star Dealer Target.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Star Dealer Target.csv is wrong.";

		exit();

	}*/

	//For Star Target CSV

	if(similar_file_exists("../csv/$folderName/Star Branch target.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Star Branch target.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

		$rsdelete=mysqli_query($conn,$sqldelete);*/



		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_branch_code=trim($data[0]);

				$branch_name=trim($data[1]);

				$april_target=trim($data[2]);

				$april_achievement=trim($data[3]);

				$may_target=trim($data[4]);

				$may_achievement=trim($data[5]);

				$june_target=trim($data[6]);

				$june_achievement=trim($data[7]);

				$july_target=trim($data[8]);

				$july_achievement=trim($data[9]);

				$august_target=trim($data[10]);

				$august_achievement=trim($data[11]);

				$september_target=trim($data[12]);

				$september_achievement=trim($data[13]);

				$october_target=trim($data[14]);

				$october_achievement=trim($data[15]);

				$november_target=trim($data[16]);

				$november_achievement=trim($data[17]);

				$december_target=trim($data[18]);

				$december_achievement=trim($data[19]);

				$january_target=trim($data[20]);

				$january_achievement=trim($data[21]);

				$february_target=trim($data[22]);

				$february_achievement=trim($data[23]);

				$march_target=trim($data[24]);

				$march_achievement=trim($data[25]);

				

				$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($dns_branch_code)."'";

				$rsbranchcode=mysqli_query($conn,$sqlbranchcode);

				$rowbranchcode=mysqli_fetch_array($rsbranchcode);

				$branch_code=$rowbranchcode['branch_code'];

				

				$sqlchkbranchcode="SELECT branch_code FROM self_appraisal_branch_wise WHERE branch_code='".addslashes($branch_code)."'";

				$rschkbranchcode=mysqli_query($conn,$sqlchkbranchcode);

				$countchkbranchcode=mysqli_num_rows($rschkbranchcode);

				//$rowbranchcode=mysqli_fetch_array($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkbranchcode==0){

				$sqlselfappraisal  = "insert into self_appraisal_branch_wise SET ";

				$sqlselfappraisal .= "   branch_code='".mysqli_real_escape_string($branch_code)."'";

					$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisal .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisal .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisal .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisal .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisal .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisal .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisal .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisal .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisal .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisal .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisal .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisal .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisal .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($conn,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Star Target.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_branch_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisalupdate .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$branch_code."'";

			mysqli_query($conn,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Star Target.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Star Target.csv is wrong.";

		exit();

	}*/

	//For Product Group Target ACh CSV

	if(similar_file_exists("../csv/$folderName/Product Group Target ACh.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Product Group Target ACh.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

		$rsdelete=mysqli_query($conn,$sqldelete);*/



		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_emp_code=trim($data[0]);

				$emp_name=trim($data[1]);

				$product_group_name=trim($data[2]);

				$april_target=trim($data[3]);

				$april_achievement=trim($data[4]);

				$may_target=trim($data[5]);

				$may_achievement=trim($data[6]);

				$june_target=trim($data[7]);

				$june_achievement=trim($data[8]);

				$july_target=trim($data[9]);

				$july_achievement=trim($data[10]);

				$august_target=trim($data[11]);

				$august_achievement=trim($data[12]);

				$september_target=trim($data[13]);

				$september_achievement=trim($data[14]);

				$october_target=trim($data[15]);

				$october_achievement=trim($data[16]);

				$november_target=trim($data[17]);

				$november_achievement=trim($data[18]);

				$december_target=trim($data[19]);

				$december_achievement=trim($data[20]);

				$january_target=trim($data[21]);

				$january_achievement=trim($data[22]);

				$february_target=trim($data[23]);

				$february_achievement=trim($data[24]);

				$march_target=trim($data[25]);

				$march_achievement=trim($data[26]);

				

				$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_name)."'";

				$rsempcode=mysqli_query($conn,$sqlempcode);

				$rowempcode=mysqli_fetch_array($rsempcode);

				$emp_code=$rowempcode['emp_code'];

				

				$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";

				$rsproductgroupcode=mysqli_query($conn,$sqlproductgroupcode);

				$rowproductgroupcode=mysqli_fetch_array($rsproductgroupcode);

				$product_group_code=$rowproductgroupcode['product_group_code'];

								

				$sqlchkempproductgroup="SELECT emp_code,product_group_code FROM self_appraisal_productgroup_wise 

									WHERE emp_code='".$emp_code."' AND product_group_code='".$product_group_code."'";

				$rschkempproductgroup=mysqli_query($conn,$sqlchkempproductgroup);

				$countchkempproductgroup=mysqli_num_rows($rschkempproductgroup);

				//$rowbranchcode=mysqli_fetch_array($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkempproductgroup==0){

				$sqlselfappraisal  = "insert into self_appraisal_productgroup_wise SET ";

				$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";

				$sqlselfappraisal .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

				$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";

				$sqlselfappraisal .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

				$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

				$sqlselfappraisal .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

				$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

				$sqlselfappraisal .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

				$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

				$sqlselfappraisal .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

				$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

				$sqlselfappraisal .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

				$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

				$sqlselfappraisal .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

				$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

				$sqlselfappraisal .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

				$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

				$sqlselfappraisal .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

				$sqlselfappraisal .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

				$sqlselfappraisal .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

				$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

				$sqlselfappraisal .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

				$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

				$sqlselfappraisal .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

				$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

				$sqlselfappraisal .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

				$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($conn,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Product Group Target ACh.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_productgroup_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisalupdate .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."' 

												AND product_group_code='".$product_group_code."'";

					mysqli_query($conn,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Product Group Target ACh.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Star  Target.csv is wrong.";

		exit();

	}*/

   //For Product Wise Target Ach

	if(similar_file_exists("../csv/$folderName/Product Wise Target Ach.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Product Wise Target Ach.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

		$rsdelete=mysqli_query($conn,$sqldelete);*/



		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$emp_code_name=trim($data[0]);

				$prod_code_desc_name=trim($data[1]);

				$april_target=trim($data[2]);

				$april_achievement=trim($data[3]);

				$april_prev_target=trim($data[4]);

				$april_prev_achievement=trim($data[5]);

				$may_target=trim($data[6]);

				$may_achievement=trim($data[7]);

				$may_prev_target=trim($data[8]);

				$may_prev_achievement=trim($data[9]);

				$june_target=trim($data[10]);

				$june_achievement=trim($data[11]);

				$june_prev_target=trim($data[12]);

				$june_prev_achievement=trim($data[13]);

				$july_target=trim($data[14]);

				$july_achievement=trim($data[15]);

				$july_prev_target=trim($data[16]);

				$july_prev_achievement=trim($data[17]);

				$august_target=trim($data[18]);

				$august_achievement=trim($data[19]);

				$august_prev_target=trim($data[20]);

				$august_prev_achievement=trim($data[21]);

				$september_target=trim($data[22]);

				$september_achievement=trim($data[23]);

				$september_prev_target=trim($data[24]);

				$september_prev_achievement=trim($data[25]);

				$october_target=trim($data[26]);

				$october_achievement=trim($data[27]);

				$october_prev_target=trim($data[28]);

				$october_prev_achievement=trim($data[29]);

				$november_target=trim($data[30]);

				$november_achievement=trim($data[31]);

				$november_prev_target=trim($data[32]);

				$november_prev_achievement=trim($data[33]);

				$december_target=trim($data[34]);

				$december_achievement=trim($data[35]);

				$december_prev_target=trim($data[36]);

				$december_prev_achievement=trim($data[37]);

				$january_target=trim($data[38]);

				$january_achievement=trim($data[39]);

				$january_prev_target=trim($data[40]);

				$january_prev_achievement=trim($data[41]);

				$february_target=trim($data[42]);

				$february_achievement=trim($data[43]);

				$february_prev_target=trim($data[44]);

				$february_prev_achievement=trim($data[45]);

				$march_target=trim($data[46]);

				$march_achievement=trim($data[47]);

				$march_prev_target=trim($data[48]);

				$march_prev_achievement=trim($data[49]);

				

				if(providing_code=='yes'){

					$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";

				}

				else{

					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

				}

				$rsempcode=mysqli_query($conn,$sqlempcode);

				$rowempcode=mysqli_fetch_array($rsempcode);

				$emp_code=$rowempcode['emp_code'];

				

				if(providing_code=='yes'){

					$sqlproductcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".addslashes($prod_code_desc_name)."'";

				}

				else{

					$sqlproductcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_desc_name)."'";

				}

				$rsproductcode=mysqli_query($conn,$sqlproductcode);

				$rowproductcode=mysqli_fetch_array($rsproductcode);

				$prod_code=$rowproductcode['prod_code'];

								

				$sqlchkempproduct="SELECT emp_code,prod_code FROM self_appraisal_product_wise 

									WHERE emp_code='".$emp_code."' AND prod_code='".$prod_code."'";

				$rschkempproduct=mysqli_query($conn,$sqlchkempproduct);

				$countchkempproduct=mysqli_num_rows($rschkempproduct);

				//$rowbranchcode=mysqli_fetch_array($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkempproduct==0){

				$sqlselfappraisal  = "insert into self_appraisal_product_wise SET ";

				$sqlselfappraisal .= "   emp_code='".mysqli_real_escape_string($emp_code)."'";

				$sqlselfappraisal .= " , prod_code='".mysqli_real_escape_string($prod_code)."'";

				$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";

				$sqlselfappraisal .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

				$sqlselfappraisal .= " , apr_prev_y_target='".mysqli_real_escape_string($april_prev_target)."'";

				$sqlselfappraisal .= " , apr_prev_y_achievement='".mysqli_real_escape_string($april_prev_achievement)."'";

				$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

				$sqlselfappraisal .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

				$sqlselfappraisal .= " , may_prev_y_target='".mysqli_real_escape_string($may_prev_target)."'";

				$sqlselfappraisal .= " , may_prev_y_achievement='".mysqli_real_escape_string($may_prev_achievement)."'";

				$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

				$sqlselfappraisal .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

				$sqlselfappraisal .= " , jun_prev_y_target='".mysqli_real_escape_string($june_prev_target)."'";

				$sqlselfappraisal .= " , jun_prev_y_achievement='".mysqli_real_escape_string($june_prev_achievement)."'";

				$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

				$sqlselfappraisal .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

				$sqlselfappraisal .= " , jul_prev_y_target='".mysqli_real_escape_string($july_prev_target)."'";

				$sqlselfappraisal .= " , jul_prev_y_achievement='".mysqli_real_escape_string($july_prev_achievement)."'";

				$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

				$sqlselfappraisal .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

				$sqlselfappraisal .= " , aug_prev_y_target='".mysqli_real_escape_string($august_prev_target)."'";

				$sqlselfappraisal .= " , aug_prev_y_achievement='".mysqli_real_escape_string($august_prev_achievement)."'";

				$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

				$sqlselfappraisal .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

				$sqlselfappraisal .= " , sep_prev_y_target='".mysqli_real_escape_string($september_prev_target)."'";

				$sqlselfappraisal .= " , sep_prev_y_achievement='".mysqli_real_escape_string($september_prev_achievement)."'";

				$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

				$sqlselfappraisal .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

				$sqlselfappraisal .= " , oct_prev_y_target='".mysqli_real_escape_string($october_prev_target)."'";

				$sqlselfappraisal .= " , oct_prev_y_achievement='".mysqli_real_escape_string($october_prev_achievement)."'";

				$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

				$sqlselfappraisal .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

				$sqlselfappraisal .= " , nov_prev_y_target='".mysqli_real_escape_string($november_prev_target)."'";

				$sqlselfappraisal .= " , nov_prev_y_achievement='".mysqli_real_escape_string($november_prev_achievement)."'";

				$sqlselfappraisal .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

				$sqlselfappraisal .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

				$sqlselfappraisal .= " , dec_prev_y_target='".mysqli_real_escape_string($december_prev_target)."'";

				$sqlselfappraisal .= " , dec_prev_y_achievement='".mysqli_real_escape_string($december_prev_achievement)."'";

				$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

				$sqlselfappraisal .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

				$sqlselfappraisal .= " , jan_prev_y_target='".mysqli_real_escape_string($january_prev_target)."'";

				$sqlselfappraisal .= " , jan_prev_y_achievement='".mysqli_real_escape_string($january_prev_achievement)."'";

				$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

				$sqlselfappraisal .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

				$sqlselfappraisal .= " , feb_prev_y_target='".mysqli_real_escape_string($february_prev_target)."'";

				$sqlselfappraisal .= " , feb_prev_y_achievement='".mysqli_real_escape_string($february_prev_achievement)."'";

				$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

				$sqlselfappraisal .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

				$sqlselfappraisal .= " , mar_prev_y_target='".mysqli_real_escape_string($march_prev_target)."'";

				$sqlselfappraisal .= " , mar_prev_y_achievement='".mysqli_real_escape_string($march_prev_achievement)."'";

				$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($conn,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Product Wise Target Ach.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_product_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdate .= " , apr_prev_y_target='".mysqli_real_escape_string($april_prev_target)."'";

					$sqlselfappraisalupdate .= " , apr_prev_y_achievement='".mysqli_real_escape_string($april_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisalupdate .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdate .= " , may_prev_y_target='".mysqli_real_escape_string($may_prev_target)."'";

					$sqlselfappraisalupdate .= " , may_prev_y_achievement='".mysqli_real_escape_string($may_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_prev_y_target='".mysqli_real_escape_string($june_prev_target)."'";

				    $sqlselfappraisalupdate .= " , jun_prev_y_achievement='".mysqli_real_escape_string($june_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_prev_y_target='".mysqli_real_escape_string($july_prev_target)."'";

				    $sqlselfappraisalupdate .= " , jul_prev_y_achievement='".mysqli_real_escape_string($july_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_prev_y_target='".mysqli_real_escape_string($august_prev_target)."'";

				    $sqlselfappraisalupdate .= " , aug_prev_y_achievement='".mysqli_real_escape_string($august_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_prev_y_target='".mysqli_real_escape_string($september_prev_target)."'";

				    $sqlselfappraisalupdate .= " , sep_prev_y_achievement='".mysqli_real_escape_string($september_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_prev_y_target='".mysqli_real_escape_string($october_prev_target)."'";

				    $sqlselfappraisalupdate .= " , oct_prev_y_achievement='".mysqli_real_escape_string($october_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_prev_y_target='".mysqli_real_escape_string($november_prev_target)."'";

					$sqlselfappraisalupdate .= " , nov_prev_y_achievement='".mysqli_real_escape_string($november_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_prev_y_target='".mysqli_real_escape_string($december_prev_target)."'";

					$sqlselfappraisalupdate .= " , dec_prev_y_achievement='".mysqli_real_escape_string($december_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_prev_y_target='".mysqli_real_escape_string($january_prev_target)."'";

					$sqlselfappraisalupdate .= " , jan_prev_y_achievement='".mysqli_real_escape_string($january_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_prev_y_target='".mysqli_real_escape_string($february_prev_target)."'";

					$sqlselfappraisalupdate .= " , feb_prev_y_achievement='".mysqli_real_escape_string($february_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_prev_y_target='".mysqli_real_escape_string($march_prev_target)."'";

					$sqlselfappraisalupdate .= " , mar_prev_y_achievement='".mysqli_real_escape_string($march_prev_achievement)."'";

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."' 

												AND prod_code='".$prod_code."'";

					mysqli_query($conn,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Product Wise Target Ach.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Product Wise Target Ach.csv is wrong.";

		exit();

	}*/



	//For Product Group Customer Target ACh CSV

	if(similar_file_exists("../csv/$folderName/Product Group Customer Target ACh.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Product Group Customer Target ACh.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_branch_wise";

		$rsdelete=mysqli_query($conn,$sqldelete);*/



		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_customer_code=trim($data[0]);

				$product_group_name=trim($data[1]);

				$april_target=trim($data[2]);

				$april_achievement=trim($data[3]);

				$may_target=trim($data[4]);

				$may_achievement=trim($data[5]);

				$june_target=trim($data[6]);

				$june_achievement=trim($data[7]);

				$july_target=trim($data[8]);

				$july_achievement=trim($data[9]);

				$august_target=trim($data[10]);

				$august_achievement=trim($data[11]);

				$september_target=trim($data[12]);

				$september_achievement=trim($data[13]);

				$october_target=trim($data[14]);

				$october_achievement=trim($data[15]);

				$november_target=trim($data[16]);

				$november_achievement=trim($data[17]);

				$december_target=trim($data[18]);

				$december_achievement=trim($data[19]);

				$january_target=trim($data[20]);

				$january_achievement=trim($data[21]);

				$february_target=trim($data[22]);

				$february_achievement=trim($data[23]);

				$march_target=trim($data[24]);

				$march_achievement=trim($data[25]);

				

				$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";

				$rscustomercode=mysqli_query($conn,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_array($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				

				$sqlproductgroupcode="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_name)."'";

				$rsproductgroupcode=mysqli_query($conn,$sqlproductgroupcode);

				$rowproductgroupcode=mysqli_fetch_array($rsproductgroupcode);

				$product_group_code=$rowproductgroupcode['product_group_code'];

								

				$sqlchkempproductgroup="SELECT product_group_code FROM self_appraisal_cust_productgroup_wise 

									WHERE customer_code='".$customer_code."' AND product_group_code='".$product_group_code."'";

				$rschkempproductgroup=mysqli_query($conn,$sqlchkempproductgroup);

				$countchkempproductgroup=mysqli_num_rows($rschkempproductgroup);

				//$rowbranchcode=mysqli_fetch_array($rsbranchcode);

				//$branch_code=$rowbranchcode['branch_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkempproductgroup==0){

				$sqlselfappraisal  = "insert into self_appraisal_cust_productgroup_wise SET ";

				$sqlselfappraisal .= "   customer_code='".mysqli_real_escape_string($customer_code)."'";

				$sqlselfappraisal .= " , product_group_code='".mysqli_real_escape_string($product_group_code)."'";

				$sqlselfappraisal .= " , apr_30_target='".mysqli_real_escape_string($april_target)."'";

				$sqlselfappraisal .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

				$sqlselfappraisal .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

				$sqlselfappraisal .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

				$sqlselfappraisal .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

				$sqlselfappraisal .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

				$sqlselfappraisal .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

				$sqlselfappraisal .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

				$sqlselfappraisal .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

				$sqlselfappraisal .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

				$sqlselfappraisal .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

				$sqlselfappraisal .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

				$sqlselfappraisal .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

				$sqlselfappraisal .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

				$sqlselfappraisal .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

				$sqlselfappraisal .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

				$sqlselfappraisal .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

				$sqlselfappraisal .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

				$sqlselfappraisal .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

				$sqlselfappraisal .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

				$sqlselfappraisal .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

				$sqlselfappraisal .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

				$sqlselfappraisal .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

				$sqlselfappraisal .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

				$sqlselfappraisal .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($conn,$sqlselfappraisal) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Product Group Customer Target ACh.csv.Please check.");

				}

				else

				{

					$sqlselfappraisalupdate  = "update self_appraisal_cust_productgroup_wise SET ";

					$sqlselfappraisalupdate .= " apr_30_target='".mysqli_real_escape_string($april_target)."'";

					$sqlselfappraisalupdate .= " , apr_30_achievement='".mysqli_real_escape_string($april_achievement)."'";

					$sqlselfappraisalupdate .= " , may_31_target='".mysqli_real_escape_string($may_target)."'";

					$sqlselfappraisalupdate .= " , may_31_achievement='".mysqli_real_escape_string($may_achievement)."'";

					$sqlselfappraisalupdate .= " , jun_30_target='".mysqli_real_escape_string($june_target)."'";

					$sqlselfappraisalupdate .= " , jun_30_achievement='".mysqli_real_escape_string($june_achievement)."'";

					$sqlselfappraisalupdate .= " , jul_31_target='".mysqli_real_escape_string($july_target)."'";

					$sqlselfappraisalupdate .= " , jul_31_achievement='".mysqli_real_escape_string($july_achievement)."'";

					$sqlselfappraisalupdate .= " , aug_31_target='".mysqli_real_escape_string($august_target)."'";

					$sqlselfappraisalupdate .= " , aug_31_achievement='".mysqli_real_escape_string($august_achievement)."'";

					$sqlselfappraisalupdate .= " , sep_30_target='".mysqli_real_escape_string($september_target)."'";

					$sqlselfappraisalupdate .= " , sep_30_achievement='".mysqli_real_escape_string($september_achievement)."'";

					$sqlselfappraisalupdate .= " , oct_31_target='".mysqli_real_escape_string($october_target)."'";

					$sqlselfappraisalupdate .= " , oct_31_achievement='".mysqli_real_escape_string($october_achievement)."'";

					$sqlselfappraisalupdate .= " , nov_30_target='".mysqli_real_escape_string($november_target)."'";

					$sqlselfappraisalupdate .= " , nov_30_achievement='".mysqli_real_escape_string($november_achievement)."'";

					$sqlselfappraisalupdate .= " , dec_31_target='".mysqli_real_escape_string($december_target)."'";

					$sqlselfappraisalupdate .= " , dec_31_achievement='".mysqli_real_escape_string($december_achievement)."'";

					$sqlselfappraisalupdate .= " , jan_31_target='".mysqli_real_escape_string($january_target)."'";

					$sqlselfappraisalupdate .= " , jan_31_achievement='".mysqli_real_escape_string($january_achievement)."'";

					$sqlselfappraisalupdate .= " , feb_28_target='".mysqli_real_escape_string($february_target)."'";

					$sqlselfappraisalupdate .= " , feb_28_achievement='".mysqli_real_escape_string($february_achievement)."'";

					$sqlselfappraisalupdate .= " , mar_31_target='".mysqli_real_escape_string($march_target)."'";

					$sqlselfappraisalupdate .= " , mar_31_achievement='".mysqli_real_escape_string($march_achievement)."'";

					$sqlselfappraisalupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."' 

												AND product_group_code='".$product_group_code."'";

					mysqli_query($conn,$sqlselfappraisalupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Product Group Customer Target ACh.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Product Group Customer Target ACh.csv is wrong.";

		exit();

	}*/

	//For Customer product wise order plan.csv

	if(similar_file_exists("../csv/$folderName/Customer product wise order plan.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Customer product wise order plan.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate self_appraisal_customer_wise";

		$rsdelete=mysqli_query($conn,$sqldelete);*/



		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_customer_code=trim($data[0]);

				$dns_prod_code=trim($data[1]);

				$apr_purchase=trim($data[2]);

				$apr_plan=trim($data[3]);

				$may_purchase=trim($data[4]);

				$may_plan=trim($data[5]);

				$jun_purchase=trim($data[6]);

				$jun_plan=trim($data[7]);

				$jul_purchase=trim($data[8]);

				$jul_plan=trim($data[9]);

				$aug_purchase=trim($data[10]);

				$aug_plan=trim($data[11]);

				$sep_purchase=trim($data[12]);

				$sep_plan=trim($data[13]);

				$oct_purchase=trim($data[14]);

				$oct_plan=trim($data[15]);

				$nov_purchase=trim($data[16]);

				$nov_plan=trim($data[17]);

				$dec_purchase=trim($data[18]);

				$dec_plan=trim($data[19]);

				$jan_purchase=trim($data[20]);

				$jan_plan=trim($data[21]);

				$feb_purchase=trim($data[22]);

				$feb_plan=trim($data[23]);

				$mar_purchase=trim($data[24]);

				$mar_plan=trim($data[25]);

				

				

				$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."'";

				$rscustomercode=mysqli_query($conn,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_array($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				

				$sqlproductcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".addslashes($dns_prod_code)."'";

				$rsproductcode=mysqli_query($conn,$sqlproductcode);

				$rowproductcode=mysqli_fetch_array($rsproductcode);

				$prod_code=$rowproductcode['prod_code'];



				$sqlchkcustomercode="SELECT customer_code FROM customer_product_wise_orderplan_details WHERE 

									customer_code='".$customer_code."' AND 	prod_code='".$prod_code."'";

				$rschkcustomercode=mysqli_query($conn,$sqlchkcustomercode);

				$countchkcustomercode=mysqli_num_rows($rschkcustomercode);

				//$rowchkcustomercode=mysqli_fetch_array($rschkcustomercode);

				//$customer_code=$rowcustomercode['customer_code'];

				

				$csv_row_count=$rec_count+1;

				if($countchkcustomercode ==0)

				{

					$sqlorderplan  = "insert into customer_product_wise_orderplan_details SET ";

					$sqlorderplan .= "   customer_code='".mysqli_real_escape_string($customer_code)."'";

					$sqlorderplan .= " ,  prod_code='".mysqli_real_escape_string($prod_code)."'";

					$sqlorderplan .= " , apr_plan='".mysqli_real_escape_string($apr_plan)."'";

					$sqlorderplan .= " , apr_purchase='".mysqli_real_escape_string($apr_purchase)."'";

					$sqlorderplan .= " , may_plan='".mysqli_real_escape_string($may_plan)."'";

					$sqlorderplan .= " , may_purchase='".mysqli_real_escape_string($may_purchase)."'";

					$sqlorderplan .= " , jun_plan='".mysqli_real_escape_string($jun_plan)."'";

					$sqlorderplan .= " , jun_purchase='".mysqli_real_escape_string($jun_purchase)."'";

					$sqlorderplan .= " , jul_plan='".mysqli_real_escape_string($jul_plan)."'";

					$sqlorderplan .= " , jul_purchase='".mysqli_real_escape_string($jul_purchase)."'";

					$sqlorderplan .= " , aug_plan='".mysqli_real_escape_string($aug_plan)."'";

					$sqlorderplan .= " , aug_purchase='".mysqli_real_escape_string($aug_purchase)."'";

					$sqlorderplan .= " , sep_plan='".mysqli_real_escape_string($sep_plan)."'";

					$sqlorderplan .= " , sep_purchase='".mysqli_real_escape_string($sep_purchase)."'";

					$sqlorderplan .= " , oct_plan='".mysqli_real_escape_string($oct_plan)."'";

					$sqlorderplan .= " , oct_purchase='".mysqli_real_escape_string($oct_purchase)."'";

					$sqlorderplan .= " , nov_plan='".mysqli_real_escape_string($nov_plan)."'";

					$sqlorderplan .= " , nov_purchase='".mysqli_real_escape_string($nov_purchase)."'";

					$sqlorderplan .= " , dec_plan='".mysqli_real_escape_string($dec_plan)."'";

					$sqlorderplan .= " , dec_purchase='".mysqli_real_escape_string($dec_purchase)."'";

					$sqlorderplan .= " , jan_plan='".mysqli_real_escape_string($jan_plan)."'";

					$sqlorderplan .= " , jan_purchase='".mysqli_real_escape_string($jan_purchase)."'";

					$sqlorderplan .= " , feb_plan='".mysqli_real_escape_string($feb_plan)."'";

					$sqlorderplan .= " , feb_purchase='".mysqli_real_escape_string($feb_purchase)."'";

					$sqlorderplan .= " , mar_plan='".mysqli_real_escape_string($mar_plan)."'";

					$sqlorderplan .= " , mar_purchase='".mysqli_real_escape_string($mar_purchase)."'";

					$sqlorderplan .= " , download_time=CURRENT_TIMESTAMP()";

					mysqli_query($conn,$sqlorderplan) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Customer product wise order plan.csv .Please check.");

				}

				else

				{

						$prev_month=strtolower(date('M', mktime(0, 0, 0, date('m')-1, 1, date('Y'))));

						$prev_month_column=$prev_month.'_purchase';

						$sqlupdatecondition=" ,$prev_month_column='".${$prev_month.'_purchase'}."'";



					

					$sqlupdateorderplan  = "update customer_product_wise_orderplan_details SET ";

					$sqlupdateorderplan .= "  apr_plan='".mysqli_real_escape_string($apr_plan)."'";

					$sqlupdateorderplan .= " , may_plan='".mysqli_real_escape_string($may_plan)."'";

					$sqlupdateorderplan .= " , jun_plan='".mysqli_real_escape_string($jun_plan)."'";

					$sqlupdateorderplan .= " , jul_plan='".mysqli_real_escape_string($jul_plan)."'";

					$sqlupdateorderplan .= " , aug_plan='".mysqli_real_escape_string($aug_plan)."'";

					$sqlupdateorderplan .= " , sep_plan='".mysqli_real_escape_string($sep_plan)."'";

					$sqlupdateorderplan .= " , oct_plan='".mysqli_real_escape_string($oct_plan)."'";

					$sqlupdateorderplan .= " , nov_plan='".mysqli_real_escape_string($nov_plan)."'";

					$sqlupdateorderplan .= " , dec_plan='".mysqli_real_escape_string($dec_plan)."'";

					$sqlupdateorderplan .= " , jan_plan='".mysqli_real_escape_string($jan_plan)."'";

					$sqlupdateorderplan .= " , feb_plan='".mysqli_real_escape_string($feb_plan)."'";

					$sqlupdateorderplan .= " , mar_plan='".mysqli_real_escape_string($mar_plan)."'";

					$sqlupdateorderplan .= " , download_time=CURRENT_TIMESTAMP() ".$sqlupdatecondition;

					$sqlupdateorderplan .= "  WHERE customer_code='".$customer_code."' AND prod_code='".$prod_code."'";

					mysqli_query($conn,$sqlupdateorderplan) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Customer product wise order plan.csv.Please check.");

				}

			}

			 $rec_count++;

		}

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Product Group Customer Target ACh.csv is wrong.";

		exit();

	}*/



	//For Distributor route CSV

	if(similar_file_exists("../csv/$folderName/Distributor_emp_route_relation.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Distributor_emp_route_relation.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$customer_code_name=trim($data[0]);

				$emp_code_name=trim($data[1]);

				$route_code_name=trim($data[2]);

				//$actual_route=trim($data[4]);

				$emp_code_name_array=explode(',',$emp_code_name);

				

				/*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";

				$rsempnamechk=mysqli_query($conn,$sqlempnamechk);

				$rowempnamechk=mysqli_fetch_array($rsempnamechk);

				$emp_code=$rowempnamechk['emp_code'];*/

				

				if(providing_code=='yes'){

					$sqlroutecode="SELECT route_code FROM route_master WHERE dns_route_code='".addslashes($route_code_name)."'";

				}

				else

				{

					$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".addslashes($route_code_name)."'";

				}

				$rsroutecode=mysqli_query($conn,$sqlroutecode);

				//$actual_route_code=$rowactualroutecode['route_code'];

				$countroutecode=mysqli_num_rows($rsroutecode);

				if($countroutecode<1 )

					{

						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";

						$rsmaxroutecode=mysqli_query($conn,$sqlmaxroutecode);

						$rowmaxroutecode=mysqli_fetch_array($rsmaxroutecode);

						$new_route_code=$rowmaxroutecode['new_route_code'];

						

						if($new_route_code=='')

						{

							$max_route_code='RT/1';

						}

						else

						{

							$max_route_code='RT/'.($new_route_code+1);

						}

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code='".$route_code_name."'";

						$sqlroute .= " ,route_name='".$route_code_name."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlroute) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				

						//modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						$route_code=$max_route_code;

					}

				else

				{	

					$rowroutecode=mysqli_fetch_array($rsroutecode);

					$route_code=$rowroutecode['route_code'];

				}



				if(providing_code=='yes'){

					$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($customer_code_name)."'";

				}

				else{

					/*if($folderName=='HALDIRAM')

					{

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' 

											AND route_code='".$actual_route_code."'";

					}

					else

					{*/

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";

					//}

				}

				$rscustomercode=mysqli_query($conn,$sqlcustomercode);

				$rowcustomercode=mysqli_fetch_array($rscustomercode);

				$customer_code=$rowcustomercode['customer_code'];

				foreach($emp_code_name_array as $emp_code_name_value_next)

					{

						if(providing_code=='yes'){

						 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";

						}

						else{

						 $sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";	

						}

						$rsempnamechk=mysqli_query($conn,$sqlempnamechk);

						$rowempnamechk=mysqli_fetch_array($rsempnamechk);

						$emp_code=$rowempnamechk['emp_code'];

					$sqlchkdistributorroute="SELECT distributor_code FROM distributor_route_relation WHERE distributor_code='".$customer_code."',

											route_code='".$route_code."',emp_code='".$emp_code."'";

				   $rschkdistributorroute=mysqli_query($conn,$sqlchkdistributorroute);

				   $countchkdistributorroute=mysqli_num_rows($rschkdistributorroute);

					   if($countchkdistributorroute==0)

					   {						

						   $sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$customer_code."',

													route_code='".$route_code."',emp_code='".$emp_code."',download_time=CURRENT_TIMESTAMP()";

						   $rsinsertdistributorroute=mysqli_query($conn,$sqlinsertdistributorroute);

					   }

					}

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}	*/

	//For Depot Destination Freight

	if(similar_file_exists("../csv/$folderName/Depot Destination Freight.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Depot Destination Freight.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$branch_code_name=trim($data[0]);

				$destination_code_name=trim($data[1]);

				$freight=trim($data[2]);

				$acedns=trim($data[3]);

				$date=trim($data[4]);

			

				$dateArr=explode('-',$date);

				if(strlen($dateArr[2])==2)

				{

					$year='20'.$dateArr[2];

				}

				else

				{

					$year=$dateArr[2];

				}

				$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];

				

				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

				$rsbranchnamechk=mysqli_query($conn,$sqlbranchnamechk);

				$rowbranchnamechk=mysqli_fetch_array($rsbranchnamechk);

				$branch_code=$rowbranchnamechk['branch_code'];

				

				$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE dns_destination_code='".addslashes($destination_code_name)."'";

				$rsdestinationnamechk=mysqli_query($conn,$sqldestinationnamechk);

				$rowdestinationnamechk=mysqli_fetch_array($rsdestinationnamechk);

				$destination_code=$rowdestinationnamechk['destination_code'];



				$sqlbranchdestinationfreight="SELECT branch_code FROM branch_destination_freight WHERE branch_code='".addslashes($branch_code)."' 

											AND destination_code='".$destination_code."'";

				$rsbranchdestinationfreight=mysqli_query($conn,$sqlbranchdestinationfreight);

				$countbranchdestinationfreight=mysqli_num_rows($rsbranchdestinationfreight);

				if($countbranchdestinationfreight<1 )

					{

						$sqlbranchdestinationfreight  = "insert into branch_destination_freight ";

						$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code."'";

						$sqlbranchdestinationfreight .= " ,destination_code='".$destination_code."'";

						$sqlbranchdestinationfreight .= " ,acedns='Y'";

						$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlbranchdestinationfreight) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on depot destination freight table.PLease contact aceDNS admin.");				

					}

					/*else

					{

						$sqlbranchdestinationfreightupd  = "update branch_destination_freight ";

						$sqlbranchdestinationfreightupd .= " SET freight='".$freight."'";

						$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' AND route_code='".$route_code."' AND acedns='".$acedns."' AND `date`='".$finaldate."'";

						

						mysqli_query($conn,$sqlbranchdestinationfreightupd);

						

					}*/

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}	*/

	//For BRANCH WISE COMPETITOR

	if(similar_file_exists("../csv/$folderName/BRANCH WISE COMPETITOR.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/BRANCH WISE COMPETITOR.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$brand_name=trim($data[1]);

				$branch_code_name=trim($data[2]);



				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

				$rsbranchnamechk=mysqli_query($conn,$sqlbranchnamechk);

				$rowbranchnamechk=mysqli_fetch_array($rsbranchnamechk);

				$branch_code=$rowbranchnamechk['branch_code'];

				

				$sqlbranchdestinationfreight="SELECT branch_code FROM competitor_group_master WHERE branch_code='".addslashes($branch_code)."' 

											AND competitor_name	='".addslashes($brand_name)."'";

				$rsbranchdestinationfreight=mysqli_query($conn,$sqlbranchdestinationfreight);

				$countbranchdestinationfreight=mysqli_num_rows($rsbranchdestinationfreight);

				if($countbranchdestinationfreight<1)

					{

						$sqlbranchdestinationfreight  = "insert into competitor_group_master ";

						$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code."'";

						$sqlbranchdestinationfreight .= " ,competitor_name	='".$brand_name."'";

						$sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlbranchdestinationfreight) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on competitor_group_master table.PLease contact aceDNS admin.");				

					}

					/*else

					{

						$sqlbranchdestinationfreightupd  = "update branch_destination_freight ";

						$sqlbranchdestinationfreightupd .= " SET freight='".$freight."'";

						$sqlbranchdestinationfreightupd .= " , download_time=CURRENT_TIMESTAMP() WHERE branch_code='".addslashes($branch_code)."' AND route_code='".$route_code."' AND acedns='".$acedns."' AND `date`='".$finaldate."'";

						

						mysqli_query($conn,$sqlbranchdestinationfreightupd);

						

					}*/

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}*/

	//For discount csv

	if(similar_file_exists("../csv/$folderName/Discount.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/Discount.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$branch_code_name=trim($data[0]);

				$prod_code_name=trim($data[1]);

				$qty_slab=trim($data[2]);

				$TD_percent=trim($data[3]);

				$cust_class=trim($data[4]);

				$discount_code=trim($data[5]);

				$tax_code=trim($data[6]);

				$acedns=trim($data[7]);

				$SGST=trim($data[8]);

				$CGST=trim($data[9]);

				$IGST=trim($data[10]);

			

				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".addslashes($branch_code_name)."'";

				$rsbranchnamechk=mysqli_query($conn,$sqlbranchnamechk);

				$rowbranchnamechk=mysqli_fetch_array($rsbranchnamechk);

				$branch_code=$rowbranchnamechk['branch_code'];

				

				$sqlprodnamechk="SELECT prod_code FROM product_master WHERE dns_prod_code='".addslashes($prod_code_name)."'";

				$rsprodnamechk=mysqli_query($conn,$sqlprodnamechk);

				$rowprodnamechk=mysqli_fetch_array($rsprodnamechk);

				$prod_code=$rowprodnamechk['prod_code'];



				$sqldiscountchk="SELECT TD_percent FROM prodqty_custclass_wise_TD WHERE branch_code='".$branch_code."' 

								AND prod_code='".$prod_code."' AND qty_slab='".$qty_slab."' AND cust_class='".$cust_class."'";

				$rsdiscountchk=mysqli_query($conn,$sqldiscountchk);

				$countdiscountchk=mysqli_num_rows($rsdiscountchk);

				$rowdiscountchk=mysqli_fetch_array($rsdiscountchk);



				if($countdiscountchk<1 )

					{

						$sqlinsertdiscount  = "insert into prodqty_custclass_wise_TD ";

						$sqlinsertdiscount .= " SET branch_code='".$branch_code."'";

						$sqlinsertdiscount .= " ,	prod_code='".$prod_code."'";

						$sqlinsertdiscount .= " ,	qty_slab='".$qty_slab."'";

						$sqlinsertdiscount .= " ,	TD_percent='".$TD_percent."'";

						$sqlinsertdiscount .= " ,	cust_class='".$cust_class."'";

						$sqlinsertdiscount .= " , acedns='".$acedns."'";

						$sqlinsertdiscount .= " , discount_code='".$discount_code."'";

						$sqlinsertdiscount .= " , tax_code='".$tax_code."'";

						$sqlinsertdiscount .= " , SGST='".$SGST."'";

						$sqlinsertdiscount .= " , CGST='".$CGST."'";

						$sqlinsertdiscount .= " , IGST='".$IGST."'";

						$sqlinsertdiscount .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlinsertdiscount) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on prodqty_custclass_wise_TD table.PLease contact aceDNS admin.");				

					}

					else

					{

						$sqlupdatediscount  = "update prodqty_custclass_wise_TD ";

						$sqlupdatediscount .= " SET TD_percent='".$TD_percent."'";

						$sqlupdatediscount .= ", discount_code='".$TD_percent."'";

						$sqlupdatediscount .= ", tax_code='".$tax_code."'";

						$sqlupdatediscount .= ", SGST='".$SGST."'";

						$sqlupdatediscount .= ", CGST='".$CGST."'";

						$sqlupdatediscount .= ", IGST='".$IGST."'";

						$sqlupdatediscount .= " ,acedns='".$acedns."',download_time=CURRENT_TIMESTAMP() WHERE branch_code='".$branch_code."' 

						AND prod_code='".$prod_code."' AND qty_slab='".$qty_slab."' AND cust_class='".$cust_class."'";

						

						mysqli_query($conn,$sqlupdatediscount);

						

					}

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Company master.csv is wrong.";

		exit();

	}*/

	//For Branch costcenter CSV

	if(similar_file_exists("../csv/$folderName/branch costcenter.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/branch costcenter.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		

		$lines = file($filename);

		/*$sqldelete="truncate branch_master";

		$rsdelete=mysqli_query($conn,$sqldelete);*/

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dns_branch_code=trim($data[0]);

				$cost_center=trim($data[3]);

				if(providing_code=='yes'){

					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";

				}

				else

				{

					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_name."'";

				}

				$rsbranchnamechk=mysqli_query($conn,$sqlbranchnamechk);

				$countbranchnamechk=mysqli_num_rows($rsbranchnamechk);

				$rowbranchnamecheck=mysqli_fetch_array($rsbranchnamechk);

				$branch_code=$rowbranchnamecheck['branch_code'];

				

				$sqlbranch  = "UPDATE branch_master SET ";

				$sqlbranch .= "  	costcenter='".mysqli_real_escape_string($cost_center)."'";

				$sqlbranch .= "  WHERE branch_code='".$branch_code."'";

				mysqli_query($conn,$sqlbranch) or array_push($error_array,"mysqli_error().Duplicate key @row $csv_row_count in Branch master.csv.Please check.");

			}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for Branch costcenter.csv is wrong.";

		exit();

	}*/

	

		if(similar_file_exists("../csv/$folderName/ABDOS Customer Master New.csv")!=false)

		{

		$filename=similar_file_exists("../csv/$folderName/ABDOS Customer Master New.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

			$lines = file($filename);

			$countroute=0;

			  foreach($lines as $line)

			  {

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   $data[]=$value;

					$dns_customer_code =trim($data[0]);

					$customer_name	=trim($data[1]);

					$phone_no		=trim($data[2]);

					$dns_route_code	  =trim($data[3]);

					$route_name	  =trim($data[4]);  

					$emp_code_name		=trim($data[5]);

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					 $emp_code_name_array=explode(',',$emp_code_name);

					$acedns		  =trim($data[6]);

					$credit_limit	=trim($data[7]);

					$credit_days	 =trim($data[8]);

					$current_balance =trim($data[9]);

					$black_list	  =trim($data[10]); 

					$TD	  		  =trim($data[11]);

					$branch_code_name =trim($data[12]);

					$customer_type   =trim($data[13]);

					$rds_tag   =trim($data[14]);

					$sauda_validity_period  =trim($data[15]);

					$address  =trim($data[16]);

					$landline_no  =trim($data[17]);

					$owner_name  =trim($data[18]);

					$owner_phone  =trim($data[19]);

					$cust_class  =trim($data[20]);

					$weekly_closing_day  =trim($data[21]);

					$coverage_type  =trim($data[22]);

					$TIN  =trim($data[23]);

					$PAN  =trim($data[24]);

					$district  =trim($data[25]);

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim($data[27]);

					$bank_account_number  =trim($data[28]);

					$email  =trim($data[29]);

					$visit_day  =trim($data[30]);

					

					$mapped_emp_code_string='';

					//For employee code and branch code

					if($acedns =='Y'){

					foreach($emp_code_name_array as $emp_code_name_value_next)

					{

					//For distributor tagged

					if(providing_code=='yes'){

						$sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."'";

					}

					else

					{

						$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y'";

					}

					$rsrdscode=mysqli_query($conn,$sqlrdscode);

					$rowrdscode=mysqli_fetch_array($rsrdscode);

					$rds_code=$rowrdscode['customer_code'];

					

					//For route

					if(providing_code=='yes'){

						$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."'";

					}

					else

					{

						$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";

					}

					$rsroutechk=mysqli_query($conn,$sqlroutechk);

					$countroutechk=mysqli_num_rows($rsroutechk);

					if($countroutechk<1 && $route_name!='')

					{

						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";

						$rsmaxroutecode=mysqli_query($conn,$sqlmaxroutecode);

						$rowmaxroutecode=mysqli_fetch_array($rsmaxroutecode);

						$new_route_code=$rowmaxroutecode['new_route_code'];

						

						if($new_route_code=='')

						{

							$max_route_code='RT/1';

						}

						else

						{

							$max_route_code='RT/'.($new_route_code+1);

						}

						$sqlroute  = "insert into route_master ";

						$sqlroute .= " SET route_code='".$max_route_code."'";

						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";

						$sqlroute .= " ,route_name='".$route_name."'";

						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";

						mysqli_query($conn,$sqlroute) or  array_push($error_array,"mysqli_error().

										Internal DATA execution problem on route table.PLease contact aceDNS admin.");				

						//modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

						$route_code=$max_route_code;

					}

					else

					{

						$rowroutechk=mysqli_fetch_array($rsroutechk);

						$route_code=$rowroutechk['route_code'];

					}

							$sqlupdated  = "update customer_master ";

							$sqlupdated .= " SET route_code='".$route_code."'";

							echo $sqlupdated .= " , rds_tag='".$rds_code."',download_time=CURRENT_TIMESTAMP() 

											 WHERE  customer_code='".$dns_customer_code."'";

							mysqli_query($conn,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

							//modifyempdatadownloadlog($conn,$emp_code,strtoupper($folderName));

							//exit();

				 	}

				 }

			  }

					$rec_count++;

		   }//End of for loop

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Customer Master.csv is wrong.";

			exit();

		}*/

		if(similar_file_exists("../csv/$folderName/HALDIRAM Customer Master.csv")!=false)

		{

		$filename=similar_file_exists("../csv/$folderName/HALDIRAM Customer Master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

			$lines = file($filename);

			$countroute=0;

			  foreach($lines as $line)

			  {

				$i = 0;

				$char = substr($line, $i, 1);

				$value ="";

				$data="";

				$double_coute_found = false;

				if($rec_count>=1)

				{ 

					while($char!="")

					{

						if($double_coute_found && $char=="\"")

						{

							$double_coute_found = false;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

				

						if(!$double_coute_found && $char=="\"")

						{  

							$double_coute_found = true;

							$i++;

							$char = substr($line, $i, 1);

							continue;

						}

						if($char=="," && !$double_coute_found)

						{

							$data[]=$value;

							$value = "";

						}

						else 

						{

						$value .= $char;

						}

						$i++;

						$char = substr($line, $i, 1);

					} //end of while

				   $data[]=$value;

					$dns_customer_code =trim($data[0]);

					$customer_name	=trim($data[1]);

					$phone_no		=trim($data[2]);

					$dns_route_code	  =trim($data[3]);

					$route_name	  =trim($data[4]);  

					$emp_code_name		=trim($data[5]);

					 if(strpos($emp_code_name,';')!=false)

					 {

						$emp_code_name=str_replace(';',',',$emp_code_name);

					 }

					 $emp_code_name_array=explode(',',$emp_code_name);

					$acedns		  =trim($data[6]);

					$credit_limit	=trim($data[7]);

					$credit_days	 =trim($data[8]);

					$current_balance =trim($data[9]);

					$black_list	  =trim($data[10]); 

					$TD	  		  =trim($data[11]);

					$branch_code_name =trim($data[12]);

					$customer_type   =trim($data[13]);

					$rds_tag   =trim($data[14]);

					$sauda_validity_period  =trim($data[15]);

					$address  =trim($data[16]);

					$landline_no  =trim($data[17]);

					$owner_name  =trim($data[18]);

					$owner_phone  =trim($data[19]);

					$cust_class  =trim($data[20]);

					$weekly_closing_day  =trim($data[21]);

					$coverage_type  =trim($data[22]);

					$TIN  =trim($data[23]);

					$PAN  =trim($data[24]);

					$district  =trim($data[25]);

					$minimum_stock  =trim($data[26]);

					$bank_name  =trim($data[27]);

					$bank_account_number  =trim($data[28]);

					$email  =trim($data[29]);

					$visit_day  =trim($data[30]);

					

					$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND acedns='Y' 

									AND cust_type='D'";

					$rsrdscode=mysqli_query($conn,$sqlrdscode);

					$rowrdscode=mysqli_fetch_array($rsrdscode);

					$rds_code=$rowrdscode['customer_code'];

					

					$sqlupdated  = "update customer_master ";

					$sqlupdated .= " SET rds_tag='".$rds_code."'";

					$sqlupdated .= " ,download_time=CURRENT_TIMESTAMP() WHERE  customer_code='".$dns_customer_code."'";

					mysqli_query($conn,$sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");

			  }

					$rec_count++;

		   }//End of for loop

			$successval=1;

		}

		/*else

		{

			echo $successval="Naming convention for Customer Master.csv is wrong.";

			exit();

		}*/

	//For scheme master csv

	if(similar_file_exists("../csv/$folderName/scheme master.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/scheme master.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$scheme_id=trim($data[0]);

				$start_date=trim($data[1]);

				if(strpos($start_date,'/')!=false){

				 $dateArr=explode('/',$start_date);

				}

				if(strpos($start_date,'-')!=false){

				 $dateArr=explode('-',$start_date);

				}

				if(strlen($dateArr[2])==2)

				{

					$year='20'.$dateArr[2];

				}

				else

				{

					$year=$dateArr[2];

				}

				$finalstartdate=$year.'-'.$dateArr[1].'-'.$dateArr[0];



				$end_date=trim($data[2]);

				if(strpos($end_date,'/')!=false){

				 $dateArr=explode('/',$end_date);

				}

				if(strpos($end_date,'-')!=false){

				 $dateArr=explode('-',$end_date);

				}

				if(strlen($dateArr[2])==2)

				{

					$year='20'.$dateArr[2];

				}

				else

				{

					$year=$dateArr[2];

				}

				$finalenddate=$year.'-'.$dateArr[1].'-'.$dateArr[0];

				$scheme_prod_code_name=trim($data[3]);

				$scheme_qty=trim($data[4]);

				$scheme_amount=trim($data[5]);

				$scheme_type=trim($data[6]);

				$freebies_prod_code_name=trim($data[7]);

				$freebies_qty=trim($data[8]);

				$freebies_val_percent=trim($data[9]);

				$freebies_val_amount=trim($data[10]);

				$scheme_filter=trim($data[11]);

				$scheme_UOM =trim($data[12]);

				$freebies_UOM=trim($data[13]);

				

				 if(providing_code=='yes'){

						$sqlschemeprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$scheme_prod_code_name."'";

						$sqlfreebiesprodcode="SELECT prod_code,prod_desc FROM product_master WHERE dns_prod_code='".$freebies_prod_code_name."'";

					}

					else

					{

						$sqlschemeprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($scheme_prod_code_name)."'";

						$sqlfreebiesprodcode="SELECT prod_code,prod_desc FROM product_master WHERE prod_desc='".addslashes($freebies_prod_code_name)."'";

					}

				 $rsschemeprodcode=mysqli_query($conn,$sqlschemeprodcode);

				 $countschemeprodcode=mysqli_num_rows($rsschemeprodcode);

				 if($countschemeprodcode >0)

				 {

					 $rowschemeprodcode=mysqli_fetch_array($rsschemeprodcode);

					 $scheme_prod_code=$rowschemeprodcode['prod_code'];

				 }

				 else

				 {

					 $scheme_prod_code=$scheme_prod_code_name;

				 }

				 $rsfreebiesprodcode=mysqli_query($conn,$sqlfreebiesprodcode);

				 $countfreebiesprodcode=mysqli_num_rows($rsfreebiesprodcode);

				 if($countfreebiesprodcode > 0)

				 {

					$rowfreebiesprodcode=mysqli_fetch_array($rsfreebiesprodcode);

				 	$freebies_prod_code=$rowfreebiesprodcode['prod_code'];

					$freebies_prod_desc=$rowfreebiesprodcode['prod_desc'];

				 }

				 else{

					 $freebies_prod_code='';

					 $freebies_prod_desc=$freebies_prod_code_name;

				 }

				/*$sqlschemechk="SELECT freebies_prod_code,freebies_prod_desc FROM freebies_master WHERE scheme_id=(SELECT scheme_id FROM scheme_master WHERE prod_code='".$scheme_prod_code."' AND start_date='".$finalstartdate."' AND end_date='".$finalenddate."')";*/

				$sqlschemechk="SELECT scheme_id FROM scheme_master WHERE prod_code='".$scheme_prod_code_name."' AND start_date='".$finalstartdate."' AND end_date='".$finalenddate."'";

				$rsschemechk=mysqli_query($conn,$sqlschemechk);

				$countschemechk=mysqli_num_rows($rsschemechk);

				/*if($countschemechk >0){

					echo "Scheme on this product is ongoing please choose another product";

					die;

				}

				$freebies_prod_code_array=array();

				$freebies_prod_desc_array=array();

				if($countschemechk >0){

					while($rowschemechk=mysqli_fetch_array($rsschemechk))

					{

						array_push($freebies_prod_code_array,$rowschemechk['freebies_prod_code']);

						array_push($freebies_prod_desc_array,$rowschemechk['freebies_prod_desc']);

					}

				}*/

				if($countschemechk ==0){

				$sqlmaxscheme="SELECT MAX(scheme_id) AS max_scheme_id FROM  scheme_master WHERE 1";

				$rsmaxscheme=mysqli_query($conn,$sqlmaxscheme);

				$rowmaxscheme=mysqli_fetch_array($rsmaxscheme);

				$max_scheme_id=$rowmaxscheme['max_scheme_id'];

				

				if($max_scheme_id=='')

				{

					$max_scheme_id='S00001';

				}

				else

				{

					$max_scheme_id++;

				}



				$sqlinsertscheme  = "insert into scheme_master ";

				$sqlinsertscheme .= " SET scheme_id='".$max_scheme_id."'";

				$sqlinsertscheme .= " ,	dns_scheme_id='".$scheme_id."'";

				$sqlinsertscheme .= " ,	start_date='".$finalstartdate."'";

				$sqlinsertscheme .= " ,	end_date='".$finalenddate."'";

				$sqlinsertscheme .= " ,	prod_code='".$scheme_prod_code_name."'";

				$sqlinsertscheme .= " , qty='>=".$scheme_qty."'";

				$sqlinsertscheme .= " , amount='".$scheme_amount."'";

				$sqlinsertscheme .= " , scheme_type='single'";

				$sqlinsertscheme .= " , scheme_filter='".$scheme_filter."'";

				$sqlinsertscheme .= " , UOM='".$scheme_UOM."'";

				$sqlinsertscheme .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($conn,$sqlinsertscheme) or  array_push($error_array,"mysqli_error().

								Internal DATA execution problem on scheme master table.PLease contact aceDNS admin.");

				$scheme_id=$max_scheme_id;				

				

				}

				else

				{

					$rowschemechk=mysqli_fetch_array($rsschemechk);

					$scheme_id=$rowschemechk['scheme_id'];

				}

				$sqlinsertfreebies  = "insert into freebies_master ";

				$sqlinsertfreebies .= " SET scheme_id='".$scheme_id."'";

				$sqlinsertfreebies .= " ,	freebies_prod_code='".$freebies_prod_code_name."'";

				$sqlinsertfreebies .= " ,	freebies_prod_desc='".$freebies_prod_desc."'";

				$sqlinsertfreebies .= " ,	qty='".$freebies_qty."'";

				$sqlinsertfreebies .= " ,	value_percent='".$freebies_val_percent."'";

				$sqlinsertfreebies .= " , value_amount='".$freebies_val_amount."'";

				$sqlinsertfreebies .= " , UOM='".$freebies_UOM."'";

				mysqli_query($conn,$sqlinsertfreebies) or  array_push($error_array,"mysqli_error().

								Internal DATA execution problem on freebies master table.PLease contact aceDNS admin.");

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for scheme master.csv is wrong.";

		exit();

	}*/

	//For customer sku msl csv

	if(similar_file_exists("../csv/$folderName/customer sku msl.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/customer sku msl.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$customer_code_name=trim($data[0]);

				$prod_code_name=trim($data[1]);

				$msl=trim($data[2]);

				

				 if(providing_code=='yes'){

						$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."'";

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";

					}

					else

					{

						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".trim(addslashes($prod_code_name))."'";

						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".trim(addslashes($customer_code_name))."'";

					}

				 $rsprodcode=mysqli_query($conn,$sqlprodcode);

				 $rowprodcode=mysqli_fetch_array($rsprodcode);

				 $prod_code=$rowprodcode['prod_code'];

				 $rscustomercode=mysqli_query($conn,$sqlcustomercode);

				 $rowcustomercode=mysqli_fetch_array($rscustomercode);

				 $customer_code=$rowcustomercode['customer_code'];

				 

				 $sqlselectcustomerprodmsl="SELECT msl FROM customer_product_wise_msl WHERE customer_code='".$customer_code."' 

				 						AND prod_code='".$prod_code."'";

				 $rsselectcustomerprodmsl=mysqli_query($conn,$sqlselectcustomerprodmsl);

				 $countselectcustomerprodmsl=mysqli_num_rows($rsselectcustomerprodmsl);	

				 

				 if($countselectcustomerprodmsl >0)

				 {

					 $sqlupdate="UPDATE customer_product_wise_msl SET acedns='N' WHERE customer_code='".$customer_code."' 

				 						AND prod_code='".$prod_code."'";

					 $rsupdate=mysqli_query($conn,$sqlupdate);					

				 }



				$sqlinsertmsl  = "insert into customer_product_wise_msl ";

				$sqlinsertmsl .= " SET customer_code='".$customer_code."'";

				$sqlinsertmsl .= " ,	prod_code='".$prod_code."'";

				$sqlinsertmsl .= " ,	msl='".$msl."'";

				$sqlinsertmsl .= " ,	acedns='Y'";

				$sqlinsertmsl .= " , download_time=CURRENT_TIMESTAMP()";

				mysqli_query($conn,$sqlinsertmsl) or  array_push($error_array,"mysqli_error().

								Internal DATA execution problem on customer_product_wise_msl table.PLease contact aceDNS admin.");

			   }

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for customer sku msl.csv is wrong.";

		exit();

	}*/



	if(similar_file_exists("../csv/$folderName/stock allocation.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/stock allocation.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$prodarray="";

		$date=gmdate('d',strtotime('+330 minute'));

		$month=gmdate('m',strtotime('+330 minute'));

		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));

		$minute=gmdate('i',strtotime('+330 minute'));

		$second=gmdate('s',strtotime('+330 minute'));

		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if(!$double_coute_found && $char=="\"")

					{  

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					 $value .= $char;

					}

					

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

				//echo $value;

			   $data[]=$value;

				//echo count($data);

			   if($rec_count==0)

				{

					foreach($data as $key=>$value)

					{

						//if($key >1){

							$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".rtrim(addslashes($value))."'";

							$rsprodcode=mysqli_query($conn,$sqlprodcode);

							$countprodcode=mysqli_num_rows($rsprodcode);

							if($countprodcode >0){

								$rowprodcode=mysqli_fetch_array($rsprodcode);

								$prod_code=$rowprodcode['prod_code'];

								$prodarray[]=$prod_code;

							}

						//}

					}

					//print_r($prodarray);

				}

				else

				{

				  //print_r($data);

				  //print_r($prodarray);

				  $customer_name=trim($data[0]);

				  $from_date=trim($data[1]);

				  $to_date=trim($data[2]);

				  $from_date=date('Y-m-d',strtotime(str_replace('/','-',$from_date)));

				  $to_date=date('Y-m-d',strtotime(str_replace('/','-',$to_date)));

				  $sqlselcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";

				  $rsselcustomercode=mysqli_query($conn,$sqlselcustomercode);

				  $rowcustomercode=mysqli_fetch_array($rsselcustomercode);

				  $customer_code=$rowcustomercode['customer_code'];

				  $allocation_id='CA'.str_replace('/','#',$customer_code).$contentsdatetime;

				  $sqldelete="DELETE FROM customer_product_allocation WHERE customer_code='".$customer_code."'";

				  mysqli_query($conn,$sqldelete);

				  for($k=0;$k <count($prodarray);$k++)

				  {

					  $sqlinsert="INSERT INTO customer_product_allocation SET allocation_id='".$allocation_id."',customer_code='".$customer_code."',

					  			from_date='".$from_date."',to_date='".$to_date."',acedns='Y',download_time=CURRENT_TIMESTAMP(),

					  			prod_code='".$prodarray[$k]."',qty='".$data[$k+3]."'";

					  mysqli_query($conn,$sqlinsert);

					  

					  $sqlinsertlog="INSERT INTO customer_product_allocation_log SET allocation_id='".$allocation_id."',customer_code='".$customer_code."',

					  			from_date='".$from_date."',to_date='".$to_date."',acedns='Y',download_time=CURRENT_TIMESTAMP(),

					  			prod_code='".$prodarray[$k]."',qty='".$data[$k+3]."'";

					  mysqli_query($conn,$sqlinsertlog);				

				  }

				}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for stock allocation.csv is wrong.";

		exit();

	}*/

	if(similar_file_exists("../csv/$folderName/billing information.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/billing information.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$prodarray="";

		$date=gmdate('d',strtotime('+330 minute'));

		$month=gmdate('m',strtotime('+330 minute'));

		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));

		$minute=gmdate('i',strtotime('+330 minute'));

		$second=gmdate('s',strtotime('+330 minute'));

		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if(!$double_coute_found && $char=="\"")

					{  

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					 $value .= $char;

					}

					

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

				//echo $value;

			     $data[]=$value;

				  //print_r($data);

				  //print_r($prodarray);

				  $customer_name=trim($data[0]);

				  $invoice_no=trim($data[1]);

				  $invoice_date=trim($data[2]);

				  $invoice_date_final=date('Y-m-d',strtotime(str_replace('/','-',$invoice_date)));

				  $prod_desc=trim($data[3]);

				  $IMEI=trim($data[4]);

				  

				   $sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".rtrim(addslashes($prod_desc))."'";

				   $rsprodcode=mysqli_query($conn,$sqlprodcode);

				   $countprodcode=mysqli_num_rows($rsprodcode);

				   $rowprodcode=mysqli_fetch_array($rsprodcode);

				   $prod_code=$rowprodcode['prod_code'];



				  $sqlselcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";

				  $rsselcustomercode=mysqli_query($conn,$sqlselcustomercode);

				  $rowcustomercode=mysqli_fetch_array($rsselcustomercode);

				  $customer_code=$rowcustomercode['customer_code'];

				  //$allocation_id='CA'.str_replace('/','#',$customer_code).$contentsdatetime;

				 // $sqldelete="DELETE FROM customer_product_allocation WHERE customer_code='".$customer_code."'";

				 // mysqli_query($conn,$sqldelete);

				  $sqlinsert="INSERT INTO customer_product_billing SET customer_code='".$customer_code."',

							invoice_no='".$invoice_no."',invoice_date='".$invoice_date_final."',acedns='Y',download_time=CURRENT_TIMESTAMP(),

							prod_code='".$prod_code."',IMEI='".$IMEI."'";

				  mysqli_query($conn,$sqlinsert);

					  

					  /*$sqlinsertlog="INSERT INTO customer_product_allocation_log SET allocation_id='".$allocation_id."',customer_code='".$customer_code."',

					  			from_date='".$from_date."',to_date='".$to_date."',acedns='Y',download_time=CURRENT_TIMESTAMP(),

					  			prod_code='".$prodarray[$k]."',qty='".$data[$k+3]."'";

					  mysqli_query($conn,$sqlinsertlog);*/				

				}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for billing information.csv is wrong.";

		exit();

	}*/

	if(similar_file_exists("../csv/$folderName/activation.csv")!=false)

	{

		$filename=similar_file_exists("../csv/$folderName/activation.csv");

		$rec_count = 0;

		$ins_count = 0;

		$err = "";

		$prodarray="";

		$date=gmdate('d',strtotime('+330 minute'));

		$month=gmdate('m',strtotime('+330 minute'));

		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));

		$minute=gmdate('i',strtotime('+330 minute'));

		$second=gmdate('s',strtotime('+330 minute'));

		$contentsdatetime =$year.$month.$date.$hour.$minute.$second;

		

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));

		$lines = file($filename);

		foreach($lines as $line)

		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if(!$double_coute_found && $char=="\"")

					{  

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					 $value .= $char;

					}

					

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

				//echo $value;

			     $data[]=$value;

				  //print_r($data);

				  //print_r($prodarray);

				  $IMEI_one="'".trim($data[0])."'";

				  $IMEI_two="'".trim($data[1])."'";

				  

				  $sqlIMEI="SELECT IMEI FROM customer_product_billing WHERE IMEI IN($IMEI_one,$IMEI_two)";

				  $rsIMEI=mysqli_query($conn,$sqlIMEI);

				  $countIMEI=mysqli_num_rows($rsIMEI);

				  if($countIMEI >0)

				  {

					  $rowIMEI=mysqli_fetch_array($rsIMEI);

					  $active_IMEI=$rowIMEI['IMEI'];

					  $sqlupdate="UPDATE  customer_product_billing SET active_IMEI='".$active_IMEI."',

							,active_IMEI_upload_time=CURRENT_TIMESTAMP() WHERE IMEI='".$active_IMEI."'";

				  	mysqli_query($conn,$sqlupdate);

				  }

				}

			 $rec_count++;

		}		

		$successval=1;

	}

	/*else

	{

		echo $successval="Naming convention for activation.csv is wrong.";

		exit();

	}*/


	if($successval==1)
	{
			$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP(),uploading_ip='".$_SERVER['REMOTE_ADDR']."'";
			if(mysqli_query($conn,$sqlInsert))
			{
if(count($error_array)>0)
{
$error_string=implode('#',$error_array);
$submsg .= 'Zip file extracted and data has been uploaded successfully with the following error(s).';
}
else{
$submsg .= 'Zip file extracted and data has been uploaded successfully';
}
$submsg .= $error_string;
				
			}
			else 
			{
				$submsg .= "Problem with uploading Zip file";
				
			}
	}else 
			{
				$submsg .= "Something went wrong.";
				
			}
		/*---------------------------------CODE END--------------------------------------------*/
		
	}else{
		$submsg = "Failed to unzip the file. Please try later.".$zipresfun;
	}
	}else{
		$submsg = "Failed to upload the file. Please try later.";
	}	
}else{
	$submsg = "Please browse the ZIP file first...";
}
}
$add_page_name = "upload_data.php";
$page_name = "upload_data.php";

include "web_header.php";
?>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>Upload Data</h2>
                        </div>
                        <div class="body">
<div class="table-responsive">
<form action="" method="POST" name="upld_data_form" id="upld_data_form" enctype="multipart/form-data">
<div class="row clearfix" style="margin:0px;">
<div class="col-sm-12">
    <div class="form-group" style="margin-top:10px;">
        <label for="zip_file">Upload ZIP file <strong><font color="#FF0000">[File extension will be .zip]</font></strong></label>
        <div class="form-line">
<input type="file" class="form-control" id="zip_file" name="zip_file" placeholder="Select Zip file">
        </div>
    </div>
<div class="form-group">
<input type="submit" class="btn bg-red waves-effect upld_btn" name="upload" value="Upload" />
</div>
<div class="form-group">
<?php if($submsg!=""){ echo $submsg;}?>
</div>  
</div>
</div>
</form>
</div>
</div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->
            
            <!-- #END# Exportable Table -->
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){

jQuery("form#upld_data_form").submit(function(){
	var zip_file = jQuery("#zip_file").val();
	if(zip_file==""){
		alert("Please browse the ZIP file first...");
		jQuery("#zip_file").focus();
		return false;
	}else{
		var fname = zip_file.toUpperCase();
		var pos1 = fname.indexOf(".ZIP");
		if(pos1==-1){
		alert("Invalid File Type\nPlease use ZIP only...");
		jQuery("#zip_file").focus();
		return false;
		}else{
		return true;
		}
	}
	
});

});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>