<?php
$name=$_POST["name"];
$address=$_POST["address"];
$flag=0;

$filename = 'record.txt';  //it has to be existed, readable and writable (run chmod 777 record.txt on server)
$records    = file($filename); 

foreach($records as $record) { 
    list($key, $val) = explode(':', $record); 
	if(trim($key) == trim($name) && trim($val) == trim($address))
		$flag=1;
} 

/*a record is found in file */
if($flag==1)
	echo "$name is already registered."; 
else
{	
	$attendee = "$name : $address\n";	

	/*a record cannot be inserted in file due to the permission denied or some  other reasons */
	if(!file_put_contents($filename, $attendee, FILE_APPEND | LOCK_EX))
		echo "Cannot write to file.";
	else //a record is inserted successfully in file
		echo "$name has been successfully registered.";
}

?>
