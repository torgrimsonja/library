<?php
/*****************************************************************
 *	common.php
 *	------------------------
 *  Created			: September 12, 2006
 *  Created by:		: Jason Torgrimson
 *  Copyright		: 2006 Twin Falls High School.
 *	Description		: This file is used to include classes and
 					  establish database connectiving for all
					  pages in the site.
 ****************************************************************/

/************************************
* 	REQUIRED CONFIGURATION INCLUDE	*
************************************/

	//Require security and configuration files
		require_once(ROOT_PATH . 'inc/config.inc.php');

/************************************
* 	SET ERROR REPORTING				*
************************************/

	error_reporting(E_ALL);
	ini_set('display_errors', '1');


/************************************
* 	REQUIRED SYSTEM INCLUDES		*
************************************/

	//Create database class and establish connection
		//require_once(ROOT_PATH 	. 'inc/classes/db.php'); Not using the database_driver class
		$db = new mysqli($config['databaseHost'], $config['databaseUser'], $config['databasePassword'], $config['databaseName']);
		//Check connection for fails
		if($db->connect_errno > 0){
			die('Unable to connect to database.<br />Connection Error Number:['.$db->connect_errno.']');
		}


	//Include data balidation class
		require_once(ROOT_PATH 	. 'inc/classes/data_validation.php');

	//Include functions class
		require_once(ROOT_PATH 	. 'inc/classes/functions.php');

	//Include authentication class
		require_once(ROOT_PATH 	. 'inc/classes/auth.php');

	//Include file_upload class
		include(ROOT_PATH 		. 'inc/classes/template.php');


/************************************
* 	QUERY SYSTEM VARIABLES		*
************************************/

	/************************
	* 	CAPTURE SETTINGS	*
	************************/
		$result = $db->query('SELECT `name`, `value` FROM `settings`');

		$_SESSION['SETTINGS'] = array();
		
		while($row = $result->fetch_assoc()){
			$_SESSION['SETTINGS'][$row['name']]	= $data_validation->escape_html($row['value']);
		}

	/********************************
	* 	BUILD SCHEDULE IN SESSION	*
	********************************/

		$result = $db->query('SELECT endTime FROM schedule WHERE id = \''.$_SESSION['SETTINGS']['currentSchedule'].'\'');
		$_SESSION['SCHEDULE'] = array();

			$_SESSION['SCHEDULE']['ENDTIME'] = $result->fetch_assoc()/*['endTime']*/;

		//query for schedule
			$_SESSION['SCHEDULE']['BLOCK'] = array();

			$scheduleBlocks = $db->query('SELECT organization_timeBlock_id, timeStart FROM schedule_block WHERE schedule_id = '.$_SESSION['SETTINGS']['currentSchedule']);
			$currentRow = 0;
			$totalRows = count($scheduleBlocks);
			//I reworked this while loop so that it was more logical, but I'm unsure whether it is fully accomplishing what it needs to.  -Joel 4/15/15
			while($currentRow <= $totalRows){
				$_SESSION['SCHEDULE']['BLOCK'][$row['organization_timeBlock_id']] = $row['timeStart'];
				$currentRow++;
			}

			// sort blocks by time from earlies to lates while keeping association between key and value as key is the ID of the block
			asort($_SESSION['SCHEDULE']['BLOCK']);

/************************************
* 	HANDLE LOGOUT REQUEST			*
************************************/

	//Handle logout
		if(isset($_GET['logout'])){
			$auth->logout();
		}

/************************************
* 	TURN SITE OFF OR ON				*
************************************/

	if(!ENABLED){
		die(SITE_TITLE . ' is currently down for maintenance.  Please try again later.');
	}

?>
