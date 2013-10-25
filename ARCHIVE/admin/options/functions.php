<?php

function displayOptions(){
	
	global $db, $data_validation;
	
	$result =  '<div data-role="header">
					<a href="javascript:window.history.back()" data-icon="back">Back</a>
					<h1>Option Management</h1>
					<a href="?action=addOption" data-icon="plus">Add</a>
				</div>
				<div class="ui-grid-a content">
					<div class="ui-block-a">Name</div>
					<div class="ui-block-b">Options</div>';
		 		
						$db->query('SELECT * FROM `option` ORDER BY `name` ASC', 'options');
						while($row = $db->fetch_array('options')){
							
							$html['id'] 	= $data_validation->escape_html($row['id']);
							$html['name'] 	= $data_validation->escape_html($row['name']);
		
							$result .= '<div class="ui-block-a"><h2>' . $html['name'] . '</h2></div>
										<div class="ui-block-b">
											<a href="?action=editOption&id=' . $html['id'] . '" data-role="button" data-inline="true" data-icon="gear">edit</a>
											<a href="?action=deleteOption&id=' . $html['id'] . '" onclick="return confirm(\'Are you sure you want to delete this option?\');" data-role="button" data-inline="true" data-transition="fade" data-icon="delete">Delete</a>
										</div>';
					}
	   
		$result .='</div><!-- /grid-a -->';
		echo $result;
}

function addOption(){

	global $db, $data_validation;
	
?>
        <div data-role="header">
            <a onclick="window.history.back();" data-icon="back">Cancel</a>
            <h1>Options</h1>
        </div>
		<div>
            <form action="?action=addOptionDo" method="post">
                    <label for="optionName">Option Name</label><input id="optionName" type="text" name="optionName" placeholder="Name of Option" />
                    <input type="submit" name="submitOption" value="Add Option"  />     
            </form>
        </div>
<?php
}

function addOptionDo($name){

	global $db, $data_validation;

	$sql['name'] = $data_validation->escape_sql($name);

	$db->query('INSERT INTO `option` (`name`) VALUES (\'' . $sql['name'] . '\');', 'addOption');

	header('Location:?');
}

function editOption($id){
	
	global $db, $data_validation;

	$sql['optionId'] 	= $data_validation->escape_sql($id);
	$html['optionId']	= $data_validation->escape_html($sql['optionId']);
	
	$db->query('SELECT name FROM `option` WHERE id = ' . $sql['optionId'], 'optionName');

		$html['name'] = $data_validation->escape_html($db->result('optionName', 0, 'name'));
?>		
        <div data-role="header">
            <a onclick="window.history.back();" data-icon="back">Cancel</a>
            <h1>Options</h1>
        </div>
		<div>
            <form action="?action=editOptionDo" method="post">
                    <br /><label for="optionName">Option Name</label><input id="optionName" type="text" name="optionName" value="<?php echo $html['name']?>" placeholder="Name of Option" />
                    <input type="submit" name="submit" value="Submit"  /> 
                    <input type="hidden" name="optionId" value="<?php echo $html['optionId']?>" id="optionId" />    

            </form>
        </div>
<?php
}

function editOptionDo($id, $name){

	global $db, $data_validation;

	$sql['id'] = $data_validation->escape_sql($id);
	$sql['name'] = $data_validation->escape_sql($name);
	
	// update name and time for option
	$db->query('UPDATE `option` SET name = \''.$sql['name'].'\' WHERE id = ' . $sql['id'] . ';', 'updateOption');
	
	
}

function deleteOption($id){

	global $db, $data_validation;

	$sql['id'] = $data_validation->escape_sql($id);
	$db->query('DELETE FROM `option` WHERE id =  ' . $sql['id'], 'optionInfo');
	header('location:?');

}