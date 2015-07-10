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
		define('ROOT_PATH', '../');
		
	//Defines page title in the title bar and in the header.
		//Place the title of your project in the quotes.
		define('TITLE', 'Installation');						//what

/************************************************
 *	SERCURITY AND INCLUDES
************************************************/

	//Includes all classes and variables common to all pages in the site.
		require_once(ROOT_PATH . 'common.php');  //what

	//Validate authorized user access to this page
		$auth->validate_user_access('PUBLIC');

	//Include functions page for shedule system
		require_once('functions.php');

/************************************************
 *	DATA HANDLING
 *	description: Section used for filtering
 				 incoming data and escaping
				 outgoing data passed to this
				 page.
 ************************************************/
 
 
 
/************************************************
 *	PAGE SPECIFIC FUNCTIONS
 *	description: Section used for creating functions
 				 used ONLY on this page.  All other
				 functions must be included in the
				 appropriate file in the INC folder.
 ************************************************/

if(	array_key_exists('action', $_GET) &&
	$_GET['action'] == 'install' &&
	array_key_exists('organizationName', $_POST) &&
	array_key_exists('numberOfTimeBlocks', $_POST)){
	
	installSystem($_POST['organizationName'], $_POST['organizationStartTime'], $_POST['numberOfTimeBlocks'], $_POST['Jeffery']);
	
}else if(	array_key_exists('action', $_GET) &&
			$_GET['action'] == 'manageBlocksDo' &&
			array_key_exists('blockName', $_POST)){
	manageBlocksDo($_POST['blockName']);
}
 
/************************************************
 *	HEADER
 *	description: Section calls the header
 				 container for this page.
************************************************/
	
	//Establishes the structure for the header container
		$template->page_header(TITLE);
		

/************************************************
 *	PAGE OUTPUT
 *	description: Section used for all page output
************************************************/

?>

	<!-- THE ONLY THINGS YOU NEED TO CHANGE ABOVE ARE THE ROOT_PATH AND TITLE, and navigation method!!! -->

	<!-- ENTER THE CONTENT FOR YOUR PAGE HERE!!! -->
	
	<!-- Begin HTML5 content -->

     <?php
	 
	 	if(	array_key_exists('action', $_GET) &&
			$_GET['action'] == 'manageBlocks' &&
			array_key_exists('organizationId', $_GET) &&
			is_numeric($_GET['organizationId'])
			){
			manageBlocks($_GET['organizationId']);
		}else if(array_key_exists('installationComplete', $_GET)){
			installationComplete();	
		}else{
			installForm();
		}
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
		$template->page_footer();


/************************************************
 *	END OF DOCUMENT
************************************************/

?>