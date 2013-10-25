<?php
//Page on which i test some code
define('ROOT_PATH', '../../');
require_once(ROOT_PATH . 'common.php');
$auth->validate_user_access('AUTH');
$auth->require_privilege('admin');


if(array_key_exists('action', $_GET)){
	$csvfile = $_FILES['sdf']['tmp_name'];
	
	echo $_FILES['sdf']['type'];
	if (($handle = fopen($csvfile, "r")) !== FALSE) {
		//file_get_contents($csvfile);
    while (($data = explode("/t", $handle)) !== FALSE) {
		//echo $data[0];
		//$delete = "DELETE `local_library`.`student` WHERE `id`=".$data[0].";";
		$query = "INSERT INTO `local_library`.`student` (`id`, `firstName`, `lastName`, `gender`, `gradeLevel`, `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `p8`) VALUES ('". $data['0']. "', '".
				$data['1']. "', '".
				$data['2']. "', '".
				$data['3']. "', '".
				$data['4']. "', '".
				$data['5']. "', '".
				$data['6']. "', '".
				$data['7']. "', '".
				$data['8']. "', '".
				$data['9']. "', '".
				$data['10']. "', '".
				$data['11']. "', '".
				$data['12']. "');";
	mysql_query($query);
	//mysql_query($delete);
    }
    fclose($handle);
}
	}
?>

<form action="?action" method="post" enctype="multipart/form-data">
<input type="file" name="sdf" />
<input type="submit" />
</form>