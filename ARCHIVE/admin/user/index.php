<?php
/*****************************************************************
 *	/admin/user/index.php
 *	------------------------
 *  Created			: February 4, 2013
 *  Created by:		: Isaac Laris & Thor Lund
 *  Copyright		: (c) 2013 Twin Falls High School.
 *	Description		: Landing page for the adminstrative user management
 						controls
****************************************************************/
   error_reporting(E_ALL);
ini_set('display_errors', '1');
/************************************************
 *	PAGE VARIABLES AND CONSTANTS
************************************************/

	//Defines the path from this file to the root of the site
		//Define to path to the root of our site in the quotes.
		define('ROOT_PATH', '../../');
		
	//Defines page title in the title bar and in the header.
		//Place the title of your project in the quotes.
		define('TITLE', 'User Management');

/************************************************
 *	SERCURITY AND INCLUDES
************************************************/

	//Includes all classes and variables common to all pages in the site.
		require_once(ROOT_PATH . 'common.php');
		require('functions.php');
		
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
 

 	if(	array_key_exists('action', $_GET) &&
		$_GET['action'] == 'addUserDo' &&
		array_key_exists('firstName', $_POST) &&
		array_key_exists('lastName', $_POST) &&
		array_key_exists('username', $_POST) &&
		array_key_exists('password', $_POST) &&
		array_key_exists('permission', $_POST)){
		
					addUserDo($_POST['firstName'], $_POST['lastName'], $_POST['username'], $_POST['password'], $_POST['permission']);
					
	}else if(	array_key_exists('action', $_GET) &&
				$_GET['action'] == 'deleteUser' &&
				array_key_exists('userId', $_GET) &&
				is_numeric($_GET['userId'])){ 
	
					deleteUser($_GET['userId']);
					
	
	}else if(	array_key_exists('action', $_GET) &&
				$_GET['action'] == 'editUserDo' &&
				array_key_exists('userId', $_POST) &&
				array_key_exists('firstName', $_POST) &&
				array_key_exists('lastName', $_POST) &&
				array_key_exists('username', $_POST)){
					
					editUserDo($_POST['userId'], $_POST['firstName'], $_POST['lastName'], $_POST['username']);

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
	
	//Establishes the structure for the header container
		$htmlHead = '';
						
		$template->admin_page_header(TITLE, $htmlHead);
		

/************************************************
 *	PAGE OUTPUT
 *	description: Section used for all page output
************************************************/

?>

	<!-- THE ONLY THINGS YOU NEED TO CHANGE ABOVE ARE THE ROOT_PATH AND TITLE!!! -->

	<!-- ENTER THE CONTENT FOR YOUR PAGE HERE!!! -->
	
	<!-- Begin HTML5 content -->
       <?php
	   if(	array_key_exists('action', $_GET) &&
	   		$_GET['action'] == 'editUser' &&
			array_key_exists('id', $_GET) &&
			is_numeric($_GET['id']) ){ 
	
			editUser($_GET['id']);
					
		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'addUser'){
			
			addUserForm();
			
		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'changePassword'){
				
			changePassword($_GET['id']);
				
		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'changePasswordDo' &&
					$_POST['newPassword'] == $_POST['newPasswordConfirm']){
			
			changePasswordDo($_POST['newPassword'], $_POST['id']);
			
		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'editUserDo' &&
					array_key_exists('userId', $_POST) &&
					array_key_exists('firstName', $_POST) &&
					array_key_exists('lastName', $_POST) &&
					array_key_exists('username', $_POST)){
						
			editUserDo($_POST['userId'], $_POST['firstName'] , $_POST['lastName'], $_POST['username'], $_POST['permission']);

		}else{
			
			displayUsers();	
			
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
		$template->admin_page_footer();


/************************************************
 *	END OF DOCUMENT
************************************************/

?>
