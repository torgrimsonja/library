<?php
/*****************************************************************
 *	config.inc.php
 *	------------------------
 *  Created			: September 6, 2006
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2006 Twin Falls High School.
 *	Description		: This file holds configuration variables
 					  required by every public and administrative
					  page in the site.
 ****************************************************************/


/*******************************
 * Editable configuration stuff
 *******************************/
	$config['databaseHost']		=	"localhost";
	$config['databaseName']		=	"local_library";
	$config['databaseUser']		=	"library";
	$config['databasePassword']	=	"bru8in2s";
	$config['allowedHosts']		= 	array(	'192.168.3.9');
	$config['dataFilePath']		= 	'docs/data.csv';
	
/*******************************
* SYSTEM CONSTANTS
*******************************/
	$system['ADMIN_EMAIL'] 			= 'wellsvi@tfsd.org';


/*******************************
* CONFIGURATION CONSTANTS
*******************************/

	
		
/*******************************
 * Don't touch things below here
 * unless you know what you are 
 * doing!!!
 *******************************/
 
	//Verify the host accessing the site is in the list of allowe hosts
		if(!in_array($_SERVER['REMOTE_ADDR'], $config['allowedHosts'])){
			header('location:http://www.tfhsbruins.com');
		}

	//Debugging value.  If set to TRUE, all debugging functions will be enabled.
		define('DEBUG', TRUE);
	
	//This constant turns the site on and off.
		//If set to TRUE, the application is turned on.
		//If set to FALSE, the application is turned off and displayes a maintenance message.
		define('ENABLED', TRUE);
?>