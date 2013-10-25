<?php
/*****************************************************************
 *	DOCUMENT_TEMPLATE.php
 *	------------------------
 *  Created			: September 6, 2006
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2006 Twin Falls High School.
 *	Description		: (overview of file purpose here)
****************************************************************/
   error_reporting(E_ALL);
ini_set('display_errors', '1');
/************************************************
 *	PAGE VARIABLES AND CONSTANTS
************************************************/

	//Defines the path from this file to the root of the site
		//Define to path to the root of our site in the quotes.
		define('ROOT_PATH', '../');
		
	//Defines page title in the title bar and in the header.
		//Place the title of your project in the quotes.
		define('TITLE', 'Library Checkin Administration');

/************************************************
 *	SERCURITY AND INCLUDES
************************************************/

	//Includes all classes and variables common to all pages in the site.
		require_once(ROOT_PATH . 'common.php');

	//Validate authorized user access to this page
		$auth->validate_user_access('PUBLIC');

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


 
/************************************************
 *	HEADER
 *	description: Section calls the header
 				 container for this page.
************************************************/
	
	//Establishes the structure for the header container
		$template->admin_page_header(TITLE);
		

/************************************************
 *	PAGE OUTPUT
 *	description: Section used for all page output
************************************************/

?>
	<!-- THE ONLY THINGS YOU NEED TO CHANGE ABOVE ARE THE ROOT_PATH AND TITLE, and navigation method!!! -->

	<!-- ENTER THE CONTENT FOR YOUR PAGE HERE!!! -->
	
	<!-- Begin HTML5 content -->

    <?php
			if(		array_key_exists('action', $_GET) &&
					$_GET['action'] == 'login' &&
					array_key_exists('username', $_POST) &&
					array_key_exists('password', $_POST)){
			
					$auth->process_login($_POST['username'], $_POST['password']);
				
				}
	
		if($auth->check_privilege('manager', 'admin', 'reports')){
			$html['name'] = $data_validation->escape_html($_SESSION['FIRST_NAME']);
			echo '<h2>Welcome back ' . $html['name'] . '!</h2>
					<p>
						Choose from the menu options above to manage your Library Checkin System.
					</p>';
			
		}else{
			?>
            
            <a style="width: 80px;" href="#popupInfo" data-role="button" data-rel="popup" data-theme="b">Help</a>
            <form name="login" method="post" action="?action=login">
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Username" id="username" />
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Password" /><br />
            <input type="submit" id="button" value="Sign In" style="font-size: 14pt; width: 150px; height: 50px;" />
            </form>
            <div data-role="popup" id="popupInfo" data-transition="pop" style="max-width:350px;">
                <p>To sign into the Check in system, please input your log in information at this time. If you can't remember your username or password, please contact the Librarian for assistance.</p>
            </div>

		<?php
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