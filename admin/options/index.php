<?php
/*****************************************************************
 *	/admin/options/index.php
 *	------------------------
 *  Created			: March 15, 2013
 *  Created by:		: Jason Torgrimson & Bruno Grubisic
 *  Copyright		: (c) 2013 Twin Falls High School.
 *	Description		: Landing page for the options management
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
		define('TITLE', 'Options Management');

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
			$_GET['action'] == 'addOptionDo'){

			addOptionDo($_POST['optionName']);

		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'editOptionDo' &&
					array_key_exists('optionId', $_POST) &&
					is_numeric($_POST['optionId']) &&
					array_key_exists('optionName', $_POST)){

			editOptionDo($_POST['optionId'], $_POST['optionName']);

		}else if(	array_key_exists('action', $_GET) &&
			$_GET['action'] == 'deleteOption' &&
			array_key_exists('id', $_GET) &&
			is_numeric($_GET['id'])){

			deleteOption($_GET['id']);

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

    <div id="options">
	<?php
		if(	array_key_exists('action', $_GET) &&
			$_GET['action'] == 'editOption' &&
			array_key_exists('id', $_GET) &&
			is_numeric($_GET['id'])){

			editOption($_GET['id']);

		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'addOption'){

			addOption();

		}else{

			displayOptions();

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
