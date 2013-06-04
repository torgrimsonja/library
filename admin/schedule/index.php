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
		define('TITLE', 'Schedule Management');

/************************************************
 *	SERCURITY AND INCLUDES
************************************************/

	//Includes all classes and variables common to all pages in the site.
		require_once(ROOT_PATH . 'common.php');

	//Validate authorized user access to this page
		$auth->validate_user_access('AUTH');
		$auth->require_privilege('admin', 'manager');

	//Include functions page for shedule system
		require_once('functions.php');
		
/************************************************
 *	DATA HANDLING
 *	description: Section used for filtering
 				 incoming data and escaping
				 outgoing data passed to this
				 page.
 ************************************************/
 
		if(array_key_exists('action', $_GET) &&
			$_GET['action'] == 'addScheduleDo'){
				
			addScheduleDo($_POST);
		
		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'editScheduleDo' &&
					array_key_exists('scheduleId', $_POST) &&
					is_numeric($_POST['scheduleId']) &&
					array_key_exists('scheduleName', $_POST)){
						
			editScheduleDo($_POST);
			
		}else if(	array_key_exists('action', $_GET) &&
			$_GET['action'] == 'deleteSchedule' &&
			array_key_exists('id', $_GET) &&
			is_numeric($_GET['id'])){
				
			deleteSchedule($_GET['id']);
		
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

    <div id="schedules">
	<?php 
		if(	array_key_exists('action', $_GET) &&
			$_GET['action'] == 'editSchedule' &&
			array_key_exists('id', $_GET) &&
			is_numeric($_GET['id'])){

			editSchedule($_GET['id']);

		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'manageBlocks' &&
					array_key_exists('id', $_GET) &&
					is_numeric($_GET['id'])){
						
			manageBlocks($_GET['id']);
			
		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'addSchedule'){
						
			addSchedule();
			
		}else{
			
			displaySchedules();
			
		}
	?>
   </div>

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