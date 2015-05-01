<?php
/*****************************************************************
 *	DOCUMENT_TEMPLATE.php
 *	------------------------
 *  Created			: January 25, 2013
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2013 Twin Falls High School.
 *	Description		: Application landing page for the public.
****************************************************************/

/************************************************
 *	PAGE VARIABLES AND CONSTANTS
************************************************/

	//Defines the path from this file to the root of the site
		//Define to path to the root of our site in the quotes.
		define('ROOT_PATH', '');

	//Defines page title in the title bar and in the header.
		//Place the title of your project in the quotes.
		define('TITLE', 'Library Checkin Software, v1.0');

/************************************************
 *	SERCURITY AND INCLUDES
************************************************/

	//Includes all classes and variables common to all pages in the site.
		require_once(ROOT_PATH . 'common.php');

	//Validate authorized user access to this page
		$auth->validate_user_access('PUBLIC');
		$auth->require_allowed_host();

/************************************************
 *	DATA HANDLING
 *	description: Section used for filtering
 				 incoming data and escaping
				 outgoing data passed to this
				 page.
 ************************************************/

	 if(	array_key_exists('checkin', $_GET) &&
	 		array_key_exists('inputBarcode', $_POST) &&
			is_numeric($_POST['inputBarcode'])){

		 requestCheckin($_POST['inputBarcode']);

	 }

/************************************************
 *	PAGE SPECIFIC FUNCTIONS
 *	description: Section used for creating functions
 				 used ONLY on this page.  All other
				 functions must be included in the
				 appropriate file in the INC folder.
 ************************************************/

	function requestCheckin($barcode){
		global $data_validation, $db;

		// capture barcode
		$sql['id'] = $db->escape_string($barcode);
		
		// Check to see if student exists in the database
		$query = 'SELECT * FROM student WHERE id = \'' . $sql['id'] .'\'';
		$checkForStudent = $db->query($query);
	
		if($checkForStudent->num_rows){

			//Grab current system time
			$sql['currDate'] = date('Y-m-d');
			$sql['currTime'] = date('G:i:s');

			//checks to see if it is a checkin or checkout request
			$checkoutValidation = $db->query('SELECT id FROM `log` WHERE studentId = \''.$sql['id'].'\' AND date = \''.$sql['currDate'].'\' AND timeOut IS NULL');
			if($checkoutValidation->num_rows){

				//Process checkout request
				$sql['logId'] = $db->escape_string($checkoutValidation->fetch_assoc()/*['id']*/);
				$db->query('UPDATE `log` SET timeOut = \''.$sql['currTime'].'\' WHERE id = \''.$sql['logId'].'\'');


					//Send email to current instructor

				//build period field for database call
				$blockId = '';
				$currTime = strtotime(date('G:i:s'));
				$endTime = strtotime($_SESSION['SCHEDULE']['ENDTIME']);

				//sequence through blocks to find current period
				foreach($_SESSION['SCHEDULE']['BLOCK'] as $key => $value){
					$startTime = strtotime($value);
					if($currTime >= $startTime && $currTime <= $endTime){
						$blockId = $key;
					}
				}

				if($blockId != ''){
					$sql['block'] = 'P'.$blockId;
				}else{
					$sql['block'] = '';
				}

				//Query teacher name
				$teacherQuery = $db->query('SELECT `'.$sql['block'].'` FROM student WHERE id = '.$sql['id']);
				if(!$teacherQuery){
					$template->errorPage('Unable to find email address of current teacher.');
					exit();
				}else{
					$sql['teacherName'] = $db->escape_string($teacherQuery->fetch_assoc()/*$sql['block']*/);
					//query for alternate email address
					$emailAddress = $db->query('SELECT emailAddress FROM alternate_email_address WHERE name = \''.$sql['teacherName'].'\'');
					if($db->num_rows('alternameEmail')){
						$html['to'] = $data_validation->escape_html($emailAddress->fetch_assoc()/*['emailAddress']*/);
					}else{
						$tmpArray = explode(',', $sql['teacherName']);
						$lastname = trim($tmpArray[0]);
						$firstname = substr(trim($tmpArray[1]), 0, 2);
						$html['to'] = strtolower($lastname.$firstname.'@tfsd.org');
					}

					//initialize content

					$html['subject'] = 'Library Alert';
					$html['message'] = $data_validation->escape_html($sql['firstName']).' '.$data_validation->escape_html($sql['lastName']).' checked into the library at '.$data_validation->escape_html($sql['timeIn']).' on '.$data_validation->escape_html($sql['date']);
					$html['headers'] = 'From: '.$system['ADMIN_EMAIL']. "\r\n";

					//send email
					mail($html['to'], $html['subject'], $html['message'], $html['headers']);

				}


					// redirect user to checkout page
					header('Location:checkedOut.php?teacher='.$html['teacherName']);



			}else{
				//Handle the checkin request
				die($sql['id']);
				header('Location:options.php?id=' . $sql['id']);

			}
		}

	}

/************************************************
 *	HEADER
 *	description: Section calls the header
 				 container for this page.
************************************************/

	//Establishes the structure for the header container

		$htmlHead = '';
		$template->page_header(TITLE, $htmlHead);


/************************************************
 *	PAGE OUTPUT
 *	description: Section used for all page output
************************************************/

?>
	<!-- THE ONLY THINGS YOU NEED TO CHANGE ABOVE ARE THE ROOT_PATH AND TITLE, and navigation method!!! -->

	<!-- ENTER THE CONTENT FOR YOUR PAGE HERE!!! -->

	<!-- Begin HTML5 content -->

	<script type="text/javascript">
		<!--
		//Center the content on the page

			// set focus on the input element
			setInterval(function(){if(!$("*:focus").is("input, inputBarcode")){
				//document.getElementById('inputBarcode').focus()
				$("#inputBarcode").focus();
			}},100);

			//Set the input element value to an empty string
			$('#inputBarcode').val('');

		-->
	</script>
        
        <h2 class="title" style="text-align:center;"><a>Welcome to the Library Sign In</a></h2>
            <h2 style="color:#FFF;">Scan your Student ID card below</h2>
            <div data-role="popup" id="popupInfo" data-transition="pop">
              <p style="font-size:20px;">To Sign Into the Library Checkin System, scan your Student ID Card's Barcode, and follow the instructions given.</p>
            </div>

                <div id="checkin" >
                    <form name="barcode" id="barcode" method="post" action="?checkin" >
                    <input type="text" id="inputBarcode" onfocus="this.value=''" name="inputBarcode" placeholder="Student ID Number" style="font-size:18pt;"/><br />

                    </form>
                </div>
            <a style="width: 25%; margin-right: auto; margin-left: auto;" href="#popupInfo" data-role="button" data-rel="popup" data-theme="b">Help</a>

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
