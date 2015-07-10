<?php

function installForm(){
	?>
    	<h2 class="title">Welcome to the Library Sign In Installer</h2>
        <div class="content">
            Please input the following information
            
            <div class="content">
                <form method="post" action="?action=install" name="installer" id="installer">
                    <label for="organizationName">Organization Name</label>
                    <input type="text" name="organizationName" id="organizationName" placeholder="Organization Name" />
                    <label for="numberOfTimeBlocks">Number of Periods/Time Blocks</label>

                    <input type="number" name="numberOfTimeBlocks" min="1" id="numberOfTimeBlocks" placeholder="Number of Periods/Time Blocks" /> 

                    
                    <p align="center">Choose how to build your teacher emails</p>
                    <p style="background-color: yellow; color: black; font-size: 1.2em;">If no template is chosen, emails will have to be manually entered later</p>


                    <br />
                    <input type="button" value="Templates" name="templateButtonOption" id="templateButtonOption" onClick="$('#emailBuilderDiv').css('visibility', 'visible');"/>
                        
                                                                     
                    

                    <div data-role="fieldcontain" id="emailBuilderDiv" style="visibility: hidden">

                        

                        <p>Choose a template:  <br />   (Template examples for the name Tanner Purves)</p>


                        <select name="select-choice-1" id="select-choice-1" data-native-menu="false">
                            <option value="emailChoice1">purvesta</option>
                            <option value="emailChoice2">tannerpurves</option>
                            <option value="emailChoice3">purvestanner</option>
                            <option value="emailChoice4">tpurves</option>
                            <option value="emailChoice5">tapurves</option>
                            <option value="emailChoice6">purvest</option>
                            <option value="emailChoice7">No Template</option>
                        </select>

						<br />
                        <br />
                        
                        
                        <label for="Jeffery">Type second half of email below. DO NOT INCLUDE PREVIOUSLY SELECTED PART OR '@' SYMBOL: <br /> (i.e. typing "hostname.domain" would create the email "purvesta@hostname.domain") </label>
						<br />
                        template@<input type="text" id="emailDomain" name="Jeffery" />

                        <br />



                      <!--  <p>
                        	"emailChoice" "@" "Jeffery"       Trying to build dynamically but then i realized that i suck at life

                        	also I need to create the variable where the email will be stored and make it into a string so it can be sent to the options email build stuff

                        	$emailtemp = 'select-choice-1' . '@' . 'emailDomain'/'Jeffery'
								To prevent client from reentering @ and template:
									parse through 'Jeffery'
									if (any character == to '@')
										delete all characters before and including '@' in 'Jeffery'  (do this before concatenating stuff to make $emailtemp)


                        </p>  -->

					</div>
            
            <input type="submit" name="submit" value="Submit" />
            
            </form>
            </div>
            
			
	<?php	
}

// ADD A DYNAMICALLY BUILDING VISIBLE EMAIL SO THAT THE CLIENT DOESN'T SCREW IT UP

function installSystem($organizationName, $numberOfTimeBlocks, $Jeffery, $selectVal){
	global $db, $data_validation;

	$sql['name'] 		= $data_validation->escape_sql($organizationName);
	$sql['blocks'] 		= $data_validation->escape_sql($numberOfTimeBlocks);
	
	
	// remove any organizations that exist
	$deleteOrganization = $db->query('DELETE FROM organization');

	$insertOrganization = $db->query('INSERT INTO organization (`name`) VALUES (\'' . $sql['name'] . '\');'); 
	
	$lastId = $db->query('SELECT id FROM organization ORDER BY id DESC LIMIT 1');
	$lastIdArray = $lastId->fetch_assoc();

	
      if(strchr($Jeffery, '@')){
            $arrayay = explode($string, '@');
            $emailPart2 = $arrayay[1];

        }else{
        	$emailPart2 = $Jeffery;
        }

      $emTemStr = $selectVal . '@' . $emailPart2;

      die($emTemStr);

	/*
	So right here we need to put the Jeffery template stuff in so that teacher emails can be built according to the chosen template
	*/

	
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
