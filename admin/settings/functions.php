<?php



function displaySettings(){
	
	global $db, $data_validation;
	
	//query all settings
	$db->query('SELECT * FROM settings', 'settings');
	while($row = $db->fetch_array('settings')){
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
                    $db->query('SELECT id, name FROM schedule', 'schedules');
                    while($row = $db->fetch_array('schedules')){
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
        	<script type="text/javascript">
					function updateStudentRecords(){
						console.log('bind worked');
						$.ajax({
								url: 'ajaxUpdateStudentRecords.php',
								success: function(data){							
									alert(data);
								},
								error: function(){
									alert('Unable to update student records. Please try again.');
								},
							});
					}
			</script>
        	<button id="btnUpdateStudents" onclick="updateStudentRecords();">Update Student Records</button>
        </div>
<?php
}

function updateSettings($systemStatus, $currentSchedule, $sendEmail){

	global $db, $data_validation;

	$sql['systemStatus'] 		= $data_validation->escape_sql($systemStatus);
	$sql['currentSchedule']		= $data_validation->escape_sql($currentSchedule);
	$sql['sendEmail'] 			= $data_validation->escape_sql($sendEmail);
	
	// update name and time for schedule
	$db->query('UPDATE settings SET value = \''.$sql['systemStatus'].'\' WHERE name = \'systemStatus\'', 'systemStatus');
	$db->query('UPDATE settings SET value = \''.$sql['currentSchedule'].'\' WHERE name = \'currentSchedule\'', 'currentSchedule');
	$db->query('UPDATE settings SET value = \''.$sql['sendEmail'].'\' WHERE name = \'sendEmail\'', 'sendEmail');
	
	header('Location:?');	
	exit();
}