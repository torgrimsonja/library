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
                    <input type="submit" name="submit" value="Submit" />
                </form>
            </div>
        </div>
	<?php	
}

function installSystem($organizationName, $organizationStartTime, $numberOfTimeBlocks){
	
	global $db, $data_validation;

	$sql['name'] 		= $data_validation->escape_sql($organizationName);
	$sql['blocks'] 		= $data_validation->escape_sql($numberOfTimeBlocks);
	
	// remove any organizations that exist
	$db->query('DELETE FROM organization', 'deleteOrganization');

	$db->query('INSERT INTO organization (`name`) VALUES (\'' . $sql['name'] . '\');', 'insertOrganization');
	
	$db->query('SELECT id FROM organization ORDER BY id DESC LIMIT 1', 'lastId');
	
	$sql['organizationId'] = $data_validation->escape_sql($db->result('lastId', 0, 'id'));
	
	// remove any timeblocks that exist
	$db->query('DELETE FROM time_blocks;', 'deleteBlocks');
	
	for($i=1;$i<=$sql['blocks'];$i++){
		
		$db->query('INSERT INTO organization_timeblock (`id`,`organization_id`, `order`) VALUES (\'' . $i . '\',\'' . $sql['organizationId'] . '\', \'' . $i . '\');');
	
	}
	header('location:?action=manageBlocks&organizationId='.$sql['organizationId']);
}

function manageBlocks($id){
	
	global $db, $data_validation;

	// get schedule name
	$sql['id'] = $data_validation->escape_sql($id);
	$db->query('SELECT name FROM organization WHERE id = ' . $sql['id'], 'scheduleName');
	
		$html['name'] = $data_validation->escape_html($db->result('scheduleName', 0, 'name'));
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
				$db->query('SELECT id, name FROM organization_timeblock WHERE organization_id = ' . $sql['id'] . ' ORDER BY id ASC;', 'blockInfo');
				
					$i=1;
					while($row = $db->fetch_array('blockInfo')){
						
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
