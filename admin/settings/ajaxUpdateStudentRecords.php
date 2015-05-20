<?php
/*****************************************************************
 *	DOCUMENT_TEMPLATE.php
 *	------------------------
 *  Created			: September 6, 2006
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2006 Twin Falls High School.
 *	Description		: (overview of file purpose here)
****************************************************************/

/************************************************
 *	PAGE VARIABLES AND CONSTANTS
************************************************/

	//Defines the path from this file to the root of the site
		//Define to path to the root of our site in the quotes.
		define('ROOT_PATH', '../../');

/************************************************
 *	SERCURITY AND INCLUDES
************************************************/

	//Includes all classes and variables common to all pages in the site.
		require_once(ROOT_PATH . 'common.php');

	//Validate authorized user access to this page
		$auth->validate_user_access('AUTH');
		$auth->require_privilege('admin');

/************************************************
 *	DATA HANDLING
 *	description: Section used for filtering
 				 incoming data and escaping
				 outgoing data passed to this
				 page.
 ************************************************/

 	$file = file('../'.$config['dataFilePath']);

	$studentExists = $db->query('SELECT id FROM student');
	$studentArray = array();
	while($row = $studentExists->fetch_assoc()){
		array_push($studentArray, $row['id']);
	}

	foreach($file AS $key => $value){
		$info = explode(',', $value);
		$sql['id'] 			= $data_validation->escape_sql($info[0]);
		$sql['firstName'] 	= $data_validation->escape_sql($info[1]);
		$sql['lastName']	= $data_validation->escape_sql($info[2]);
		$sql['gender'] 		= $data_validation->escape_sql($info[3]);
		$sql['gradeLevel'] 	= $data_validation->escape_sql($info[4]);
		$sql['P1'] 			= $data_validation->escape_sql($info[5]);
		$sql['P2'] 			= $data_validation->escape_sql($info[6]);
		$sql['P3'] 			= $data_validation->escape_sql($info[7]);
		$sql['P4'] 			= $data_validation->escape_sql($info[8]);
		$sql['P5'] 			= $data_validation->escape_sql($info[9]);
		$sql['P6'] 			= $data_validation->escape_sql($info[10]);
		$sql['P7'] 			= $data_validation->escape_sql($info[11]);
		$sql['P8'] 			= $data_validation->escape_sql($info[12]);


		if(in_array($sql['id'], $studentArray)){
			$sql['statement'] = 'UPDATE student SET	firstName = \''.$sql['firstName'].'\',
													lastName = \''.$sql['lastName'].'\',
													gender = \''.$sql['gender'].'\',
													gradeLevel= \''.$sql['gradeLevel'].'\',
													P1 = \''.$sql['P1'].'\',
													P2 = \''.$sql['P2'].'\',
													P3 = \''.$sql['P3'].'\',
													P4 = \''.$sql['P4'].'\',
													P5 = \''.$sql['P5'].'\',
													P6 = \''.$sql['P6'].'\',
													P7 = \''.$sql['P7'].'\',
													P8 = \''.$sql['P8'].'\'
								where ID = '.$sql['id'];

			//Remove student from studentArray
			$file[$key] = '~';
			sort($file);
			array_pop($file);

		}else{
			$sql['statement'] = 'INSERT INTO student 	(id, firstName, lastName, gender, gradeLevel, p1, p2, p3, p4, p5, p6, p7, p8)
														VALUES
														(\''.$sql['id'].'\',
														 \''.$sql['firstName'].'\',
														 \''.$sql['lastName'].'\',
														 \''.$sql['gender'].'\',
														 \''.$sql['gradeLevel'].'\',
														 \''.$sql['p1'].'\',
														 \''.$sql['p2'].'\',
														 \''.$sql['p3'].'\',
														 \''.$sql['p4'].'\',
														 \''.$sql['p5'].'\',
														 \''.$sql['p6'].'\',
														 \''.$sql['p7'].'\',
														 \''.$sql['p8'].'\');';
		}

		$db->query($sql['statement']);
	}

	//Delete students remaining in the studentArray from the database
