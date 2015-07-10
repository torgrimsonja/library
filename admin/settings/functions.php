<?php

function displaySettings(){
	
	global $db, $data_validation;
	
	//query all settings
	$settings = $db->query('SELECT * FROM settings');
	while($row = $settings->fetch_assoc()){
		$html[$row['name']] = $data_validation->escape_html($row['value']);
	}
	?>
        <div data-role="header">
            <a href="javascript:window.history.back()" data-icon="back">Back</a>
            <h1>System Settings</h1>
        </div>
		<div class="content">
        <form method="post" action="?updateSettings">
            <div class="content">
                <label for="systemStatus">System Status:</label>
                <select name="systemStatus" id="systemStatus" data-role="slider">
                    <?php
                        if($html['systemStatus'] == 0){
                        ?>
                            <option value="0" selected="selected">Off</option>
                            <option value="1">On</option>
                        <?php
                        }else{
                        ?>
                            <option value="0">Off</option>
                            <option value="1" selected="selected">On</option>
                        <?php
                        }
                        ?>
                </select>
            </div>
            <div class="content">
                <label for="currentSchedule">Select the schedule to use for today.</label>
                <select name="currentSchedule" id="currentSchedule" data-native-menu="false" data-inline="true">
                <?php
                    $schedules = $db->query('SELECT id, name FROM schedule');
                    while($row = $schedules->fetch_assoc()){
                        $html['id'] = $data_validation->escape_html($row['id']);
                        $html['name'] = $data_validation->escape_html($row['name']);
                        $selected = $html['currentSchedule'] == $html['id'] ? 'selected="selected"' : '';
                        echo '<option value="'.$html['id'].'" '.$selected.'>'.$html['name'].'</option>';
                    }
                ?>
                </select>
            </div>
            <div class="content">
                <label for="sendEmail">Send email notifications when students arrive and depart.</label>
                <select name="sendEmail" id="sendEmail" data-role="slider">
                    <?php
                        if($html['sendEmail'] == 0){
                        ?>
                            <option value="0" selected="selected">No</option>
                            <option value="1">Yes</option>
                        <?php
                        }else{
                        ?>
                            <option value="0">No</option>
                            <option value="1" selected="selected">Yes</option>
                        <?php
                        }
                        ?>
                </select>
            </div>
            
			<input type="submit" value="Submit" id="submit" name="submit" />
        </form>
		</div>
        <div class="content">
            <h3>Update Student Records</h3>
        	<div class="content">
            <form action="?upload" method="post" enctype="multipart/form-data" data-ajax="false">
            	<input type="file" name="studentRecords" id="btnUpdateStudents" />
               	<select name="option"  data-native-menu="false">
                	<option value="replace">Remove all students from the database and replace them with these students.</option>
                    <option value="add">Add these students to existing students in the database.</option>
                </select>
                <input type="submit" value="Submit" />
            </form>
            </div>
        </div>
         <div class="content">
            <h3>Teacher Email Manual Entry</h3>
            <h5>Enter the cooresponding email for each teacher listed below...</h5>
        	<div class="content">
            <form action="?teacherEmails" method="post" data-ajax="false">
            	<!--  HTML code that will download an empty csv file for admin to edit with teacher names 
            	<label>Download an Empty CSV File to Work With</label>
            	<input type=""  -->
            	<input type="submit" value="Submit" />
            </form>
            <p>Or...</p>
            <form action="?uploadCSV" method="POST" enctype="multipart/form-data" data-ajax="false">
            	<label>Upload a CSV file containing Teacher Names and Emails</label>
            	<input type="file" name="emailUpload" id="btnUploadTeacherEmail" />
                <input type="submit" value="Submit" />
            </form>
            </div>
        </div>
<?php
}

function upload($filecsv, $option){
	global $db;
	echo $option;
	//$csvfile = $_FILES['studentRecords']['tmp_name'];
	$csvfile = $filecsv;
	if (($handle = fopen($csvfile, "r")) !== FALSE) {
		if($option === 'replace'){
			$db->query('DELETE FROM `student`');
		}
		
		$insertionCount = 0;
		
		while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
			//echo $data[0];
			$count = count($data);
			if($count == 13){
				$query = "INSERT INTO `local_library`.`student` (`id`, `firstName`, `lastName`, `gender`, `gradeLevel`, `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `p8`) 	VALUES ('". $data['0']. "', '".
						$data['1']. "', '".
						$data['2']. "', '".
						$data['3']. "', '".
						$data['4']. "', '".
						$data['5']. "', '".
						$data['6']. "', '".
						$data['7']. "', '".
						$data['8']. "', '".
						$data['9']. "', '".
						$data['10']. "', '".
						$data['11']. "', '".
						$data['12']. "');";
					
						$db->query($query);
				$insertionCount++;
			}
		}
    	fclose($handle);
	}
}

function updateSettings($systemStatus, $currentSchedule, $sendEmail){

	global $db, $data_validation;

	$sql['systemStatus'] 		= $data_validation->escape_sql($systemStatus);
	$sql['currentSchedule']		= $data_validation->escape_sql($currentSchedule);
	$sql['sendEmail'] 			= $data_validation->escape_sql($sendEmail);
	
	// update name and time for schedule
	$db->query('UPDATE settings SET value = \''.$sql['systemStatus'].'\' WHERE name = \'systemStatus\'');
	$db->query('UPDATE settings SET value = \''.$sql['currentSchedule'].'\' WHERE name = \'currentSchedule\'');
	$db->query('UPDATE settings SET value = \''.$sql['sendEmail'].'\' WHERE name = \'sendEmail\'');
	
	header('Location:?');	
	exit();
}?>