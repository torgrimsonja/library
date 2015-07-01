<?php

function installForm(){
	?>
    	<h2 class="title">Welcome to the Library Sign In Installer</h2>
        <div class="content">
            Please input the following information...
            <div class="content">
                <form method="post" action="?action=install" name="installer" id="installer">
                    <label for="organizationName">Organization Name</label>
                    <input type="text" name="organizationName" id="organizationName" placeholder="Organization Name" />
                    <label for="numberOfTimeBlocks">Number of Periods/Time Blocks</label>
                    <input type="number" name="numberOfTimeBlocks" id="numberOfTimeBlocks" placeholder="Number of Periods/Time Blocks" />
                    
                    <p align="center">Choose how to build your teacher emails!</p>
                    <div class="ui-grid-a">
                    	<div id="templateButtonDiv" class="ui-block-a"><input type="button" value="Templates" name="templateButtonOption" id="templateButtonOption"  onClick="$('#emailBuilderDiv').css('visibility', 'visible');"/></div>
                    	<div id="manualButtonDiv" class="ui-block-b"><input type="button" value="Manual Input" name="manualButtonOption" id="manualButtonOption" onClick="$('#manualTeacherEmailDiv').css('visibility', 'visible');" /></div>                        
                    </div>
                    
                    <div id="emailBuilderDiv" class="ui-grid-d" style="visibility: hidden;">
                    	<div class="ui-block-a"><input type="button" id="emailChoiceOne" value="Cheddar" /></div>	
                        <div class="ui-block-b"><input type="button" id="emailChoiceTwo" value="Cheddar" /></div>	
                        <div class="ui-block-c"><input type="button" id="emailChoiceThree" value="Cheddar" /></div>	
                        <div class="ui-block-d"><input type="button" id="emailChoiceFour" value="Cheddar" /></div>	
                        <div class="ui-block-e"><input type="button" id="emailChoiceFive" value="Cheddar" /></div>	
                    </div>
                    
                    <div id="manualTeacherEmailDiv" style="visibility: hidden;">
                        <label for="manualTeacherEmailSetup">Enter teacher emails manually</label>
                        <input type="text" name="manualTeacherEmailSetup" id="manualTeacherEmailSetup" placeholder="Teacher email goes here" hidden="true" />
                    </div>
                    
                    <div>
                    	<input type="radio" name="teacherEmailRadio" id="emailChoiceOne" />
                        <input type="radio" name="teacherEmailRadio" id="emailChoiceTwo" />
                        <input type="radio" name="teacherEmailRadio" id="emailChoiceThree" />
                        <input type="radio" name="teacherEmailRadio" id="emailChoiceFour" />
                        <input type="radio" name="teacherEmailRadio" id="emailChoiceFive" />
                        

                    </div>
                   
                    <input type="submit" name="submit" value="Submit" />
                </form>
            </div>
        </div>
	<?php	
}

/* Mia's pseudocode

type "select:"
make button "First name" 
make button "last name"
when(firstnamebuttonselected){
	create input box with type "input # of first name letters used in email"
	take # of letters and add to email generation algorithm thingy
}
when(lastnamebuttonselected){
	create input box with type "input # of last name letters used in email"
	take # of letters and add to email generation algorithm thingy
}

add "@" to email template;

create input field for the stuff that comes after the @ sign
take what is entered into the input thing and add to email template thing

create buttons with ".com", ".edu", ".net"
whatever one is selected, add to email template thing

output an example to verify:
"For teacher John Doe, the email is "jdoe@tfsd.org" is this correct? "
if yes, the email process is complete

*/



function installSystem($organizationName, $organizationStartTime, $numberOfTimeBlocks){
	
	global $db, $data_validation;

	$sql['name'] 		= $data_validation->escape_sql($organizationName);
	$sql['blocks'] 		= $data_validation->escape_sql($numberOfTimeBlocks);
	
	// remove any organizations that exist
	$deleteOrganization = $db->query('DELETE FROM organization');

	$insertOrganization = $db->query('INSERT INTO organization (`name`) VALUES (\'' . $sql['name'] . '\');');
	
	$lastId = $db->query('SELECT id FROM organization ORDER BY id DESC LIMIT 1');
	$lastIdArray = $lastId->fetch_assoc();
	
	$sql['organizationId'] = $data_validation->escape_sql($lastIdArray['id']);
	
	// remove any timeblocks that exist
	$deleteBlocks = $db->query('DELETE FROM time_blocks;');
	
	for($i=1;$i<=$sql['blocks'];$i++){
		
		$db->query('INSERT INTO organization_timeblock (`id`,`organization_id`, `order`) VALUES (\'' . $i . '\',\'' . $sql['organizationId'] . '\', \'' . $i . '\');');
	
	}
	header('location:?action=manageBlocks&organizationId='.$sql['organizationId']);
}

function manageBlocks($id){
	
	global $db, $data_validation;

	// get schedule name
	$sql['id'] = $data_validation->escape_sql($id);
	$scheduleName = $db->query('SELECT name FROM organization WHERE id = ' . $sql['id']);
	$scheduleNameArray = $scheduleName->fetch_assoc();
		$html['name'] = $data_validation->escape_html($scheduleNameArray['name']);
?>		
        <div data-role="header" data-theme="a">
            <a onclick="window.history.back();" data-icon="back">Back</a>
            <h1>Time Blocks for <span class="important"><?php echo $html['name']; ?></span></h1>
        </div>
        <div class="instructions">
        	Instructions: Enter a display name for each time block (i.e. Period 1 or 1st Period).
        </div>
		<div class="content">
            <p>
            <form name="manageBlocks" action="?action=manageBlocksDo" method="post">
                h
                <?php
				$blockInfo = $db->query('SELECT id, name FROM organization_timeblock WHERE organization_id = ' . $sql['id'] . ' ORDER BY id ASC;');
				
					$i=1;
					while($row = $blockInfo->fetch_assoc()){
						
						$html['id'] = $data_validation->escape_html($row['id']);
						$html['name'] = $data_validation->escape_html($row['name']);
					
						echo 'Block ' . $i . '<br />';
						
						echo '<input type="text" id="blockName['.$html['id'].']" value="' . $html['name'] . '" name="blockName['.$i.']" placeholder="Name to be used for this block" />';
						$i++;
					}				
				?>
                <input type="hidden" id="scheduleId" name="scheduleId" value="<?php echo $html['id']; ?>" />
                <input type="submit" id="submit" name="submit" value="Submit" />
            </form>
            </p>
        </div>
<?php
}

function manageBlocksDo($blockArray){
	global $db, $data_validation;
	var_dump($blockArray);
	
	$count = count($blockArray);
	for($i=1; $i<=$count; $i++){
		$sql['value'] = $data_validation->escape_sql($blockArray[$i]);
		$db->query('UPDATE organization_timeblock SET name = \'' . $sql['value'] . '\' WHERE id = \'' . $i . '\'');
	}
	header('Location:?installationComplete');
}

function installationComplete(){
	echo '
			<div class="content">
			<h2>Installation Complete</h2>	
			<div class="content"><h3>Your next step is to delete the "install" folder from the web server.  Once you have deleted the folder click on the button below.</h3>
			<a href="../index.php" title="Launch Application" data-role="button" data-theme="b">Launch Application</a></div>
			</div>';
}
