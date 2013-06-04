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

	//Include database class and establish connection
		require_once(ROOT_PATH 	. 'inc/classes/db.php');
	
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
		$db->query('SELECT `name`, `value`
					FROM `settings`', 'settingsInfo');
				
		$_SESSION['SETTINGS'] = array();
		
		while($row = $db->fetch_array('settingsInfo')){
			$_SESSION['SETTINGS'][$row['name']]		= $data_validation->escape_html($row['value']);
		}

	/********************************
	* 	BUILD SCHEDULE IN SESSION	*
	********************************/

		$db->query('SELECT endTime FROM schedule WHERE id = \''.$_SESSION['SETTINGS']['currentSchedule'].'\'', 'endOfDay');
		$_SESSION['SCHEDULE'] = array();

			$_SESSION['SCHEDULE']['ENDTIME'] = $db->result('endOfDay', 0, 'endTime');
			
		//query for schedule
			$_SESSION['SCHEDULE']['BLOCK'] = array();
			
			$db->query('SELECT organization_timeBlock_id, timeStart FROM schedule_block WHERE schedule_id = '.$_SESSION['SETTINGS']['currentSchedule'], 'scheduleBlocks');
			while($row = $db->fetch_array('scheduleBlocks')){
				$_SESSION['SCHEDULE']['BLOCK'][$row['organization_timeBlock_id']] = $row['timeStart'];
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