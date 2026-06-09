<?php

include "star_connection.php"; 

$filename=similar_file_exists("user_email_address.csv");
$rec_count = 0;
$ins_count = 0;
$err = "";
$header_id = "0";
//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
$lines = file($filename);
foreach($lines as $line)
{
    $i = 0;
    $char = substr($line, $i, 1);
    $value ="";
    //$data="";
    $data = array();
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
                //$data[]=$value;
                array_push($data,$value);
                $value = "";
            }
            else 
            {
            $value .= $char;
            }
            $i++;
            $char = substr($line, $i, 1);
        } //end of while
       //$data[]=$value;
       array_push($data,$value);
      //print_r($data);
        $emp_code=trim($data[0]);
        $email=trim($data[1]);
        $user_address=trim($data[2]);
      
     
        $csv_row_count=$rec_count+1;
        
            $sqlupdate  = "UPDATE  user_details  SET ";
            $sqlupdate .= " user_address='".mysqli_real_escape_string($link,$user_address)."'";
            $sqlupdate .= " , email='".mysqli_real_escape_string($link,$email)."'";
          
            $sqlupdate .= " WHERE user_id='".addslashes($emp_code)."'";
            echo sqlupdate;
            //mysqli_query($link,$sqlupdate) or array_push($error_array,"mysqli_error().Internal error occurs @row $csv_row_count in Please check.");
        
    }
     $rec_count++;
}




?>