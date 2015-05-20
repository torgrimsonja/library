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
		$auth->require_privilege('reports','admin', 'manager');

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

<script type="text/javascript">
	function showByStudent(){
					alert("in bind");
			$('#searchOptions').css('display', 'none');
			$('#searchByStudent').css('display', 'block');




	}



</script>

	<!-- Begin HTML5 content -->
	<h2>Search Options</h2>
    <div data-role="collapsible-set" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u">
        <div data-role="collapsible">
            <h3>Search By Student</h3>
            <div class="content">
            	<form action="?action=searchByStudent" method="post" data-ajax="false">
        			<input type="text" id="searchByStudentId" name="searchByStudentId" placeholder="Student Info" />
                    <input type="date" id="searchByStudentDate" name="searchByStudentDate" placeholder="Date" />
            		<input type="submit" value="Search" name="searchByStudentSubmit" id="searchByStudentSubmit" />
        		</form>

			</div>
        </div>
        <div data-role="collapsible">
            <h3>Search By Teacher</h3>
            <div class="content">
            	<form action="?action=searchByTeacher" method="post">
        			<input type="text" name="searchByTeacherName" id="searchByTeacherName" placeholder="Teacher Name" data-inline="true" style="width: 30%;" />
        			 <input type="date" name="searchByTeacherDate" id="searchByTeacherDate" placeholder="Date" data-inline="true" style="width: 30%;" />
                    <input type="submit" name="searchByTeacherSubmit" id="searchByTeacherSubmit" value="Search" />
        		</form>
            </div>
        </div>
        <div data-role="collapsible">
            <h3>Search By Date</h3>
            <div class="content">
            	<form action="?action=searchByDate" method="post">
        			<input type="date" name="searchByDateDate" id="searchByDateDate" placeholder="Date" data-inline="true" style="width: 30%;" />
            		<input type="submit" name="searchByDateSubmit" id="searchByDateSubmit" value="Search" />
        		</form>

			</div>
        </div>
        <div data-role="collapsible">
            <h3>Option Stats</h3>
            <div class="content">
            <ul data-role="listview" data-filter="true" data-inset="true">
			<?php
				//Query options
				$options = $db->query('SELECT id, name FROM `option`;');
				while($row = $options->fetch_assoc()){
					$sql['optionId'] 	= $data_validation->escape_html($row['id']);
					$html['optionName']	= $data_validation->escape_html($row['name']);

					//Query count on option
					$optionCount = $db->query('SELECT COUNT(id) AS id FROM log_option WHERE option_id = '.$sql['optionId']);
					$optionCountArray = $optionCount->fetch_assoc();
					$html['optionCount'] = $optionCountArray['id'];
					echo '<li>' . $html['optionName'] . ' = ' . $html['optionCount'] . ' </li>';


				}
			?>
			</ul>
            </div>
        </div>
	</div>
	<div class="content">
	<?php
		if(array_key_exists('action', $_GET) &&
			$_GET['action'] == 'searchByStudent' &&
			array_key_exists('searchByStudentId', $_POST) &&
			array_key_exists('searchByStudentDate', $_POST)){

			echo searchByStudent($_POST['searchByStudentId'], $_POST['searchByStudentDate']);

		}else if(	array_key_exists('action', $_GET) &&
					$_GET['action'] == 'searchByTeacher' &&
					array_key_exists('searchByTeacherName', $_POST) &&
					array_key_exists('searchByTeacherDate', $_POST)){

			echo searchByTeacher($_POST['searchByTeacherName'], $_POST['searchByTeacherDate']);

		}else if(	array_key_exists('action', $_GET) &&
			$_GET['action'] == 'searchByDate' &&
			array_key_exists('searchByDateDate', $_POST)){

			echo searchByDate($_POST['searchByDateDate']);

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
