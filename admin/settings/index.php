<?php
/*****************************************************************
 *	DOCUMENT_TEMPLATE.php
 *	------------------------
 *  Created			: February 4, 2013
 *  Created by:		: Jason Torgrimson & Bruno Grubisic
 *  Copyright		: (c) 2013 Twin Falls High School.
 *	Description		: Landing page for the schedule management
 						controls
****************************************************************/
   
/************************************************
 *	PAGE VARIABLES AND CONSTANTS
************************************************/

	//Defines the path from this file to the root of the site
		//Define to path to the root of our site in the quotes.
		define('ROOT_PATH', '../../');
		
	//Defines page title in the title bar and in the header.
		//Place the title of your project in the quotes.
		define('TITLE', 'System Settings');

/************************************************
 *	SERCURITY AND INCLUDES
************************************************/

	//Includes all classes and variables common to all pages in the site.
		require_once(ROOT_PATH . 'common.php');

	//Validate authorized user access to this page
		$auth->validate_user_access('AUTH');
		$auth->require_privilege('admin');
		
	//Include functions page for shedule system
		require_once('functions.php');
/************************************************
 *	DATA HANDLING
 *	description: Section used for filtering
 				 incoming data and escaping
				 outgoing data passed to this
				 page.
 ************************************************/
 
 	//Logic for updating settings
	if(	array_key_exists('updateSettings', $_GET) &&
		array_key_exists('systemStatus', $_POST) &&
		array_key_exists('currentSchedule', $_POST) &&
		array_key_exists('sendEmail', $_POST)){
		
		updateSettings($_POST['systemStatus'], $_POST['currentSchedule'], $_POST['sendEmail']);
		
	}
	
	//Uploading studentRecords
	if(array_key_exists('upload', $_GET) &&
		array_key_exists('option', $_POST) &&
		($_POST['option'] === 'replace' || $_POST['option'] === 'add') &&
		array_key_exists('studentRecords', $_FILES)){
			
		upload($_FILES['studentRecords']['tmp_name'], $_POST['option']);
		
	}
	
	//Logic for teacherEmail settings
		//Logic if uploading CSV file
	 	if(array_key_exists('teacherEmails', $_GET) &&
			array_key_exists('emailUpload', $_FILES)){
				
			upload($_FILES['teacherEmails']['tmp_name'], $_POST['option']);
			
		}  //Logic for manual entry option
		else if(array_key_exists('upload') &&
				array_key_exists('manualEntry', $_POST)){
	 		
	 		//Insert manual entry emails into alternate_email_address table in db
	 		
	 	}
 
/************************************************
 *	PAGE SPECIFIC FUNCTIONS
 *	description: Section used for creating functions
 				 used ONLY on this page.  All other
				 functions must be included in the
				 appropriate file in the INC folder.
 ************************************************/


 
/************************************************
 *	HEADER
 *	description: Section calls the header
 				 container for this page.
************************************************/
							
		$template->admin_page_header(TITLE);	

/************************************************
 *	PAGE OUTPUT
 *	description: Section used for all page output
************************************************/

?>

	<!-- THE ONLY THINGS YOU NEED TO CHANGE ABOVE ARE THE ROOT_PATH AND TITLE!!! -->

	<!-- ENTER THE CONTENT FOR YOUR PAGE HERE!!! -->
	
	<!-- Begin HTML5 content -->


	<?php 

		
			displaySettings();
			
	?>

	<!-- End HTML5 content -->
	
	<!-- LEAVE EVERYTHING BELOW THIS LINE ALONE!!! -->

<?php

/************************************************
 *	FOOTER
 *	description: Section calls the advertisement
 				 structure for this page.
************************************************/

	//Establishes the structure for the banner container
		$template->admin_page_footer();


/************************************************
 *	END OF DOCUMENT
************************************************/

?>