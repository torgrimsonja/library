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
		define('ROOT_PATH', '');

	//Defines page title in the title bar and in the header.
		//Place the title of your project in the quotes.
		define('TITLE', '');

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

 //Check to see if checkin options have been passed
 if(array_key_exists('action', $_POST) &&
 	$_POST['action'] == 'processCheckin'){

	 processCheckin($_POST['studentId'], $_POST['options']);

 }

/************************************************
 *	PAGE SPECIFIC FUNCTIONS
 *	description: Section used for creating functions
 				 used ONLY on this page.  All other
				 functions must be included in the
				 appropriate file in the INC folder.
 ************************************************/

	function processCheckin($studentId, $options){
		global $db, $data_validation, $template;

		$sql['id'] = $db->escape_string($studentId);
		$optionsArray = $options;

		//build period field for database call
		$blockId = '';
		$currTime = strtotime(date('G:i:s'));
		$endTime = strtotime($_SESSION['SCHEDULE']['ENDTIME']['endTime']);
		
		//select current block
		foreach($_SESSION['SCHEDULE']['BLOCK'] as $key => $value){
			$startTime = strtotime($value);
			if($currTime >= $startTime && $currTime <= $endTime){
				$blockId = $key;
			}
		}

		//set block for field in student table
		if($blockId != ''){
			$sql['block'] = 'P'.$blockId;
		}else{
			$sql['block'] = '';
		}


		//Query student information
		$studentInfo = $db->query('SELECT * FROM student WHERE id = '.$sql['id']);
		$studentInfoArray = $studentInfo->fetch_assoc();
		$sql['studentId'] = $db->escape_string($studentInfoArray['id']);
		$sql['firstName'] = $db->escape_string($studentInfoArray['firstName']);
		$sql['lastName'] = $db->escape_string($studentInfoArray['lastName']);
		$sql['date'] = date('Y-m-d');
		$sql['timeIn'] = date('G:i:s');
		//Get teacher name for current block
		if($sql['block'] != ''){
			$sql['teacherName'] = $db->escape_string($studentInfoArray['block']);
		}else{
			$sql['teacherName'] = 'Not found';
		}

		// insert checkin log
		
		if(!$db->query('INSERT INTO `log` (`studentId`, `firstName`, `lastName`, `teacherName`, `date`, `timeIn`) VALUES (	\''.$sql['studentId'].'\',  \''.$sql['firstName'].'\',  \''.$sql['lastName'].'\', \''.$sql['teacherName'].'\', \''.$sql['date'].'\',  \''.$sql['timeIn'].'\' );')){
			$template->errorPage('Unable to insert the log.');
			exit();
		}
		
		$lastId = $db->query('SELECT id FROM `log` ORDER BY id DESC LIMIT 1');
		$lastIdArray = $lastId->fetch_assoc();
		if($lastId = $db->query('SELECT id FROM `log` ORDER BY id DESC LIMIT 1')){
			$sql['logId'] = $db->escape_string($lastIdArray['id']);
		}else{
			$template->errorPage('Unable to select the log after it was inserted.');
			exit();
		}
		

		// insert options 
	
		foreach($optionsArray AS $index => $value){

			$sql['optionId'] = $db->escape_string($value);   //Does this need to be changed??
			if(!$db->query('INSERT INTO `log_option`
						(`log_id`, `option_id`)
						VALUES
						(\''.$sql['logId'].'\', \''.$sql['optionId'].'\');')){
				$template->errorPage('Unable to insert log options..');
				exit();
			}

		}
	
		// send email to teacher

			//query for alternate email address
			$fetchEmail = $db->query('SELECT emailAddress FROM alternate_email_address WHERE name = \''.$sql['teacherName'].'\'');
			$emailArray = explode('@', $emTemStr)
			$tmpArray = explode(',', $sql['teacherName']);
			if($fetchEmail->num_rows){
				$html['to'] = $data_validation->escape_html($fetchEmail->fetch_assoc()/*['emailAddress']*/);

			}else if($emailArray[0] === 'emailChoice1'){
				
				$lastname = trim($tmpArray[0]);
				$firstname = substr(trim($tmpArray[1]), 0, 2);
				$html['to'] = strtolower($lastname.$firstname.'@'.$emailArray[1]);

			}else if($emailArray[0] === 'emailChoice2'){
				
				$lastname = trim($tmpArray[0]);
				$firstname = trim($tmpArray[1]);
				$html['to'] = strtolower($firstname.$lastname.'@'.$emailArray[1]);

			}else if($emailArray[0] === 'emailChoice3'){
	
				$lastname = trim($tmpArray[0]);
				$firstname = trim($tmpArray[1]);
				$html['to'] = strtolower($lastname.$firstname.'@'.$emailArray[1]);   //building emails based on template selection

			}else if($emailArray[0] === 'emailChoice4'){
	
				$lastname = trim($tmpArray[0]);
				$firstname = substr(trim($tmpArray[1]), 0, 1);
				$html['to'] = strtolower($firstname.$lastname.'@'.$emailArray[1]);

			}else if($emailArray[0] === 'emailChoice5'){
	
				$lastname = trim($tmpArray[0]);
				$firstname = substr(trim($tmpArray[1]), 0, 2);
				$html['to'] = strtolower($firstname.$lastname.'@'.$emailArray[1]);

			}else if($emailArray[0] === 'emailChoice6'){
	
				$lastname = trim($tmpArray[0]);
				$firstname = substr(trim($tmpArray[1]), 0, 1);
				$html['to'] = strtolower($lastname.$firstname.'@'.$emailArray[1]);

			}else{
	
				echo '<script type="text/javascript">alert(\'Email could not be sent to teacher. Please enter teacher emails under "settings".\');</script>';

			}

		

			//initialize content

			$html['subject'] = 'Library Alert';
			$html['message'] = $data_validation->escape_html($sql['firstName']).' '.$data_validation->escape_html($sql['lastName']).' checked into the library at '.$data_validation->escape_html($sql['timeIn']).' on '.$data_validation->escape_html($sql['date']);
			$html['headers'] = 'From: '.$system['ADMIN_EMAIL']. "\r\n";

			//send email
			//mail($html['to'], $html['subject'], $html['message'], $html['headers']);


		header('Location:checkedIn.php');
		exit();

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
<script type="text/javascript">
<!--
	$(function(){
		$('input:checkbox').change(function () {
			if ($('input[type=checkbox]:checked').length > 0) {
				$('#btnCheckin').show();
			} else {
				$('#btnCheckin').hide();
			}
		});

	});
-->
</script>
	<!-- THE ONLY THINGS YOU NEED TO CHANGE ABOVE ARE THE ROOT_PATH AND TITLE, and navigation method!!! -->

	<!-- ENTER THE CONTENT FOR YOUR PAGE HERE!!! -->

	<!-- Begin HTML5 content -->


<?php
if(array_key_exists('id', $_GET) &&
	is_numeric($_GET['id'])){
	//Declare necessary vars and query
	$html = array();
	$sql['id'] = $db->escape_string($_GET['id']);
	$sql['statement'] = $db->escape_string('SELECT * FROM student WHERE id = ' . $sql['id']);
	$statementInfo = $db->query($sql['statement']);
	$studentArray = $statementInfo->fetch_assoc();
		
	//Set query results to vars for use
	$html['firstName']  = $data_validation->escape_html($studentArray['firstName']);
	$html['lastName']   = $data_validation->escape_html($studentArray['lastName']); 
	$html['id']		    = $data_validation->escape_html($studentArray['id']); 	
	$html['gradeLevel']	= $data_validation->escape_html($studentArray['gradeLevel']); 	
	$html['gender']	    = $data_validation->escape_html($studentArray['gender']); 	
	if($html['gender'] == 'M'){
		$html['genderIcon'] = 'M.png';
	}else{
		$html['genderIcon'] = 'F.png';
	}
	
	?>
		<div class="post">
			<h2 class="title" style="text-align:center;"><a>Welcome to the Library Sign In</a></h2>
			<div class="entry" style="text-align:center;">
				<ul data-role="listview" data-inset="true" class="ui-listview ui-listview-inset ui-corner-all ui-shadow">
					<li data-role="list-divider" data-swatch="a" data-theme="a" data-form="ui-bar-a" role="heading" class="ui-li ui-li-divider ui-bar-a ui-first-child" style="font-size: 16pt;">Welcome: <?php echo $html['id']; ?></li>
					<li data-form="ui-btn-up-a" data-swatch="a" data-theme="a" class="ui-li ui-li-static ui-btn-up-a" style="font-size: 32pt;">
						<?php echo '	<img 	style="position:relative;"
												src="'. ROOT_PATH . 'inc/css/images/'.$html['genderIcon']. '" />'
							. $html['firstName'] . ' ' . $html['lastName'] . '&nbsp - &nbsp' . $html['id']
							. '<br /><div style="font-size: 16pt;">Grade: '. $html['gradeLevel']
							. '&nbsp <div style="float:right;"><a href="index.php" data-role="button" data-theme="e" data-inline="true" data-transition="flip" style="width: 100px;">Not Me</a></div></div>';
						?>
					</li>
				</ul>
				<h3 style="color:#FFF; font-size:22px;">What is the purpose of your visit? Please check all that apply:</h2><br />
				<form method="post" action="options.php" data-ajax="false">
                    <fieldset data-role="controlgroup" data-inline="true" data-type="horizontal">
					<?php
						$result = $db->query('SELECT * FROM `option` ORDER BY name ASC');
						while($row = $result->fetch_assoc()){
							$html['id'] 	= $db->escape_string($row['id']);
							$html['name'] 	= $db->escape_string($row['name']);
							echo '	<input type="checkbox" id="radio'.$html['id'].'" name="options[]" value="'.$html['id'].'" />
									<label for="radio'.$html['id'].'" style="font-size: 13pt; width: 150px;">'.$html['name'].'</label>';
						}
						
					?>
                    </fieldset>
                    <p>&nbsp;</p>
                    <!--
                    <fieldset data-role="controlgroup" data-type="horizontal">
						<input type="checkbox" id="radio1" name="options[]" value="homework" >
							<label for="radio1" style="font-size: 13pt; width: 150px;">Homework</label>
						<input type="checkbox" id="radio2" name="options[]" value="computer">
							<label for="radio2" style="font-size: 13pt; width: 150px;">Computer</label>
					</fieldset>
					<fieldset data-role="controlgroup" data-type="horizontal">
						<input type="checkbox" id="radio3" name="options[]" value="books">
							<label for="radio3" style="font-size: 13pt; width: 150px;">Books</label>
						<input type="checkbox" id="radio4" name="reading" value="reading">
							<label for="radio4" style="font-size: 13pt; width: 150px;">Reading</label>
					</fieldset>
					<fieldset data-role="controlgroup" data-type="horizontal">
						<input type="checkbox" id="radio5" name="options[]" value="tutoring">
							<label for="radio5" style="font-size: 13pt; width: 150px;">Tutoring</label>
					</fieldset>
					  -->
                      <div id="btnCheckin" class="hider" style="display:none;">
						<input type="submit" id="btnCheckinBtn" value="Check In" data-role="button">
						<input type="hidden" name="action" id="action" value="processCheckin" />
						<input type="hidden" name="studentId" id="studentId" value="<?php echo $sql['id']?>" />
					</div>

				</form>
				</div>
				</div>
			</div>
		</div>
	<?php
	}else{
		header('Location:index.php');
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
