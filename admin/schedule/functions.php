<?php

function displaySchedules(){
	
	global $db, $data_validation;
	
	$result =  '<div data-role="header">
					<a href="javascript:window.history.back()" data-icon="back">Back</a>
					<h1>Schedule Management</h1>
					<a href="?action=addSchedule" data-icon="plus">Add</a>
				</div>
				<div class="ui-grid-a content">
					<div class="ui-block-a">Name</div>
					<div class="ui-block-b">Options</div>';
		 
						//Query settings for current schedule
						$currentSchedule = $db->query('SELECT value FROM settings WHERE name = \'currentSchedule\'');
						$currentScheduleArray = $currentSchedule->fetch_assoc();
						$html['currentSchedule'] = $data_validation->escape_html($currentScheduleArray['value']);
						
						$schedules = $db->query('SELECT * FROM schedule ORDER BY name ASC');
						while($row = $schedules->fetch_assoc()){
							
							$html['id'] 	= $data_validation->escape_html($row['id']);
							$html['name'] 	= $data_validation->escape_html($row['name']);
		
							$result .= '<div class="ui-block-a"><h2>' . $html['name'] . '</h2></div>
										<div class="ui-block-b"><a href="?action=editSchedule&id=' . $html['id'] . '" data-role="button" data-inline="true" data-icon="gear">edit</a>';
										if($html['currentSchedule'] != $html['id']){
											$result .= '<a href="#deleteSchedule' . $html['id'] . '" data-rel="popup" data-position-to="window" data-role="button" data-inline="true" data-transition="fade" data-icon="delete">delete</a>
														<div data-role="popup" id="deleteSchedule' . $html['id'] . '" data-transition="fade" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
															<div data-role="header" class="ui-corner-top">
																<h1>Delete Schedule?</h1>
															</div>
															<div data-role="content" class="ui-corner-bottom ui-content">
																<h3 class="ui-title">Are you sure you want to delete this schedule?</h3>
																<p>This action cannot be undone.</p>
																<a href="#" data-role="button" data-inline="true" data-rel="back" data-theme="c">Cancel</a>
																<a href="?action=deleteSchedule&id=' . $html['id'] . '" data-role="button" data-inline="true" data-transition="fade">Delete</a>
															</div>
														</div>';
										}else{
											$result .= '<a href="#noDelete" data-role="button" data-rel="popup" data-inline="true" data-position-to="window" data-transition="fade" data-icon="delete">delete</a>
														<div data-role="popup" id="noDelete" data-transition="fade" data-dismissible="false" style="max-width:400px;" class="ui-corner-all">
															<h3 class="ui-title">Schedule is currently being used.  You must change the current schdule in settings deleting.</h3>
															<a href="#" data-rel="back" data-role="button" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>
														</div>';

										}
							$result .= '</div>';
					}
	   
		$result .='</div><!-- /grid-a -->';
		echo $result;
}

function addSchedule(){

	global $db, $data_validation;
	
?>
        <div data-role="header">
            <a onclick="window.history.back();" data-icon="back">Cancel</a>
            <h1>Schedules</h1>
        </div>
		<div>
            <form action="?action=addScheduleDo" method="post">
                    <br /><label for="txtScheduleName">Schedule Name</label><input id="scheduleName" type="text" name="scheduleName" placeholder="Name of Schedule" />
                    <br /><label for="timeEnd">Enter the time the school day ends while using this schedule.</label><input type="time" id="timeEnd" name="timeEnd" />
                    <br /><label for="timeBlocks">Select the blocks you want to include on this schedule and set the start times?</label>
                    <div id="timeBlocks" class="content">
                        <fieldset class="ui-grid-a">
                        <div class="ui-block-a">Time Blocks</div>
                        <div class="ui-block-b">Start Times</div>
                        <?php
						//query database for all timeblocks
						$timeBlocks = $db->query('SELECT * FROM organization_timeblock ORDER BY id ASC');
                        while($row = $timeBlocks->fetch_assoc()){
                            $html['id'] 	= $data_validation->escape_html($row['id']);
                            $html['name'] 	= $data_validation->escape_html($row['name']);
                            ?>
                                    <div class="ui-block-a">
									<label for="block_<?php echo $html['id']; ?>"><?php echo $html['name']; ?></label>
										<input type="checkbox" name="timeBlock[]" id="block_<?php echo $html['id']; ?>" value="<?php echo $html['id']; ?>" />
									</div>
                    				<div class="ui-block-b">
										<input data-inline="true" type="time" name="startTime_<?php echo $html['id']; ?>" id="startTime_<?php echo $html['id']; ?>" />
									</div>
                        	<?php
                        }
                    ?>
                        </fieldset>
                    </div>
                    <input type="submit" name="submitSchedule" value="Add Schedule"  />     

            </form>
        </div>
<?php
}

function addScheduleDo($vars){

	global $db, $data_validation;

	$sql['name'] = $data_validation->escape_sql($vars['scheduleName']);
	$sql['endTime'] = $data_validation->escape_sql($vars['timeEnd']);

	$addSchedule = $db->query('INSERT INTO schedule (`name`, `endTime`) VALUES (\'' . $sql['name'] . '\', \'' . $sql['endTime'] . '\');');
	$lastId = $db->query('SELECT id FROM schedule ORDER BY id DESC LIMIT 1');
	
	$lastIdArray = $lastId->fetch_assoc();
	
	$sql['schedule_id'] = $data_validation->escape_sql($lastIdArray['id']);
	
	
	foreach($vars['timeBlock'] AS $key => $value){

		$sql['blockId'] 	= $data_validation->escape_sql($value);
		$sql['timeStart'] 	= $data_validation->escape_sql($vars['startTime_'.$value]);
		$db->query('INSERT INTO schedule_block 
						(	`schedule_id`, `organization_timeBlock_id`, `timeStart`) 
						VALUES 
						(	\'' . $sql['schedule_id'] . '\',
							\'' . $sql['blockId'] . '\',
							\'' . $sql['timeStart'] . '\'
						);');
	}

}

function editSchedule($id){
	
	global $db, $data_validation;

	$sql['scheduleId'] 	= $data_validation->escape_sql($id);
	$html['scheduleId']	= $data_validation->escape_html($sql['scheduleId']);
	
	$scheduleName = $db->query('SELECT name, endTime FROM schedule WHERE id = ' . $sql['scheduleId']);
	$scheduleNameArray = $scheduleName->fetch_assoc();
		$html['name'] = $data_validation->escape_html($scheduleNameArray['name']);
		$html['endTime'] = $data_validation->escape_html($scheduleNameArray['endTime']);
?>		
        <div data-role="header">
            <a onclick="window.history.back();" data-icon="back">Cancel</a>
            <h1>Schedules</h1>
        </div>
		<div>
            <form action="?action=editScheduleDo" method="post">
                    <br /><label for="txtScheduleName">Schedule Name</label><input id="scheduleName" type="text" name="scheduleName" value="<?php echo $html['name']?>" placeholder="Name of Schedule" />
                    <br /><label for="timeEnd">Enter the time the school day ends while using this schedule.</label><input type="time" id="timeEnd" value="<?php echo $html['endTime']; ?>" name="timeEnd" />
                    <br /><label for="timeBlocks">Select the blocks you want to include on this schedule and set the start times?</label>
                    <div id="timeBlocks" class="content">
                        <fieldset class="ui-grid-a">
                        <div class="ui-block-a">Time Blocks</div>
                        <div class="ui-block-b">Start Times</div>
                        <?php
						
						// query all blocks		
                        $timeBlocks = $db->query('SELECT * FROM organization_timeblock ORDER BY id ASC');
                        while($row = $timeBlocks->fetch_assoc()){
                            $html['id'] 	= $data_validation->escape_html($row['id']);
                            $sql['blockId']	= $data_validation->escape_sql($html['id']);
                            $html['name'] 	= $data_validation->escape_html($row['name']);
                            
								// check to see if block is active for this schedule
								$activeBlocks = $db->query('SELECT id, timeStart FROM schedule_block 
											WHERE schedule_id = ' . $sql['scheduleId'] . '
												AND organization_timeBlock_id = '.$sql['blockId'].' ORDER BY schedule_block.id ASC;');
								if($activeBlocks->num_rows){
									$activeBlocksArray = $activeBlocks->fetch_assoc();
									$html['timeStart'] 	= $data_validation->escape_html($activeBlocksArray['timeStart']);
									
								?>
                                    <div class="ui-block-a">
                                    <label for="block_<?php echo $html['id']; ?>"><?php echo $html['name']; ?></label>
                                        <input type="checkbox" name="timeBlock[]" checked="checked" id="block_<?php echo $html['id']; ?>" value="<?php echo $html['id']; ?>" />
                                    </div>
                                    <div class="ui-block-b">
                                        <input data-inline="true" type="time" name="startTime_<?php echo $html['id']; ?>" id="startTime_<?php echo $html['id']; ?>" value="<?php echo $html['timeStart']; ?>" />
                                    </div>
                            	<?php
								
								}else{
	
								?>
                                    <div class="ui-block-a">
                                    <label for="block_<?php echo $html['id']; ?>"><?php echo $html['name']; ?></label>
                                        <input type="checkbox" name="timeBlock[]" id="block_<?php echo $html['id']; ?>" value="<?php echo $html['id']; ?>" />
                                    </div>
                                    <div class="ui-block-b">
                                        <input data-inline="true" type="time" name="startTime_<?php echo $html['id']; ?>" id="startTime_<?php echo $html['id']; ?>" />
                                    </div>
                            	<?php
								}
                        }
                    ?>
                        </fieldset>
                    </div>
                    <input type="submit" name="submit" value="Submit"  /> 
                    <input type="hidden" name="scheduleId" value="<?php echo $html['scheduleId']?>" id="scheduleId" />    

            </form>
        </div>
<?php
}

function editScheduleDo($vars){

	global $db, $data_validation;

	$sql['name'] = $data_validation->escape_sql($vars['scheduleName']);
	$sql['endTime'] = $data_validation->escape_sql($vars['timeEnd']);
	$sql['scheduleId'] = $data_validation->escape_sql($vars['scheduleId']);
	
	// update name and time for schedule
	$db->query('UPDATE schedule SET name = \''.$sql['name'].'\', endTime = \''.$sql['endTime'].'\' WHERE id = ' . $sql['scheduleId'] . ';', 'updateSchedule');
	
	// delete all timeblocks before insertion
	$db->query('DELETE FROM schedule_block WHERE schedule_id = ' . $sql['scheduleId']);
	
	//Insert new timeblocks
	if(is_array($vars['timeBlock'])){
		
		foreach($vars['timeBlock'] AS $value){
	
			$sql['blockId'] 	= $data_validation->escape_sql($value);
			$sql['timeStart'] 	= $data_validation->escape_sql($vars['startTime_'.$value]);
			$db->query('INSERT INTO schedule_block 
							(	`schedule_id`, `organization_timeBlock_id`, `timeStart`) 
							VALUES 
							(	\'' . $sql['scheduleId'] . '\',
								\'' . $sql['blockId'] . '\',
								\'' . $sql['timeStart'] . '\'
							);');

		}
		
	}
	
}

function deleteSchedule($id){
	
	global $db, $data_validation;

	$sql['id'] = $data_validation->escape_sql($id);
	$db->query('DELETE FROM schedule WHERE id =  ' . $sql['id']);
	header('location:?');

}