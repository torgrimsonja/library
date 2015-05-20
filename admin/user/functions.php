<?php

function displayUsers(){
	
	global $db, $data_validation;
	?>
	<div data-role="header">
        <a href="javascript:window.history.back()" data-icon="back">Back</a>
        <h1>User Management</h1>
        <a href="?action=addUser" title="Add User" data-icon="plus">Add</a>
    </div>
	<div class="ui-grid-a content">
		<div class="ui-block-a" data-theme="b">Name</div>
		<div class="ui-block-b" data-theme="b">Options</div>
				<?php 
				
					$users = $db->query('SELECT * FROM user ORDER BY firstName');
					while($row = $users->fetch_assoc()){
						
						$html['firstName'] 	= $data_validation->escape_html($row['firstName']);
						$html['lastName'] 	= $data_validation->escape_html($row['lastName']);
						$html['username'] 	= $data_validation->escape_html($row['username']);
						$html['password'] 	= $data_validation->escape_html($row['password']);
						$html['id'] 		= $data_validation->escape_html($row['id']);
	
						echo '	<div class="ui-block-a">
										<h2>' . $html['firstName'] . '</h2>
								</div>
								<div class="ui-block-b">
										<a href="?action=editUser&id=' . $html['id'] . '" data-role="button" data-inline="true">edit</a><a href="?action=deleteUser&userId=' . $html['id'] . '" data-role="button" data-inline="true">delete</a>
								</div>';
								
					}
				?>
	</div><!-- /grid-a -->
	<?php	
}
function addUserForm(){
	
	global $db, $data_validation;
?>
        <div data-role="header" class="ui-header ui-bar-a" role="banner">
        <a href="javascript:window.history.back()" data-icon="back" class="ui-btn-left ui-btn ui-btn-up-a ui-shadow ui-btn-corner-all ui-btn-icon-left" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a"><span class="ui-btn-inner"><span class="ui-btn-text">Cancel</span><span class="ui-icon ui-icon-back ui-icon-shadow">&nbsp;</span></span></a>
        <h1 class="ui-title" role="heading" aria-level="1">Add User</h1>
        </div>
        <div class="content">
        <form name="addUser" action="?action=addUserDo" method="post">
        		<label for="firstName">First Name</label><input type="text" placeholder="First Name" name="firstName" id="firstName" />
            	<label for="lastName">Last Name</label><input type="text" placeholder="Last Name" name="lastName" id="lastName" />
				<br />
                <label for="permission">User Type</label>
                <select name="permission" id  data-native-menu="false">
                	<?php
						$permissions = $db->query('	SELECT id, name FROM privilege');
						while($row = $permissions->fetch_assoc()){
							//Escape data
								$html['id'] 	= $data_validation->escape_html($row['id']);
								$html['name'] 	= ucwords($data_validation->escape_html($row['name']));
								echo '<option value="' . $html['id'] . '">' . $html['name'] . '</option>';
						}
					?>
                </select>
                <br />
            	<label for="username">Username</label><input type="text" placeholder="Username" name="username" id="username" />
				<label for="password">Password</label><input type="password" name="password" placeholder="Password" id="password" />
                <label for="submit">&nbsp;</label>
                <input type="submit" value="Submit" name="submit" id="submit" />
        </form>
        </div>

<?php	
}

function deleteUser($id){
	global $db, $data_validation;
	$sql['id'] = $data_validation->escape_sql($id);
	
	$db->query('DELETE FROM user WHERE id=\'' . $sql['id'] . '\'');
	
}

function addUserPermissionDo($id, $privilege){
	global $db;
	$db->query('INSERT INTO `user_privilege` (`user_id`, `privilege_id`) VALUES (' . $id . ',' . $privilege . ')');
}

function selectUserId($username, $privilege){
	global $db, $data_validation;
	$userId = $db->query('SELECT id FROM user WHERE username=\'' . $username . '\'');
	$userIdArray = $userId->fetch_assoc();
	$sql['id'] = $userIdArray['id'];
	addUserPermissionDo($sql['id'], $privilege);
}

function addUserDo($firstName, $lastName, $username, $password, $privilege){
	global $db, $data_validation;
			$sql['firstName'] = $data_validation->escape_sql($firstName);
			$sql['lastName'] = $data_validation->escape_sql($lastName);
			$sql['username'] = $data_validation->escape_sql($username);
			$sql['privilege'] = $data_validation->escape_sql($privilege);
			$sql['password'] = md5($password);

	$db->query('INSERT INTO user (firstName, lastName, username, password) VALUES (\'' . $sql['firstName'] . '\', \'' . $sql['lastName'] . '\', \'' . $sql['username'] . '\', \'' . $sql['password'] . '\')');
	
	selectUserId($sql['username'], $sql['privilege']);
}

function editUserPrivilegeGet($id){
	global $db, $data_validation;
	$sql['id'] = $data_validation->escape_html($id);
	$permissionGet = $db->query('SELECT * FROM `user_privilege` WHERE user_id=\''. $sql['id'] .'\'', 'permission');
	//START HERE
	$html['privilege_id']= $data_validation->escape_html($db->result('permission', 0, 'privilege_id'));
}

function changePassword($id){
	global $db, $data_validation;
	
	$sql['userId'] = $data_validation->escape_sql($id);

		$db->query('SELECT * FROM user WHERE id = ' . $sql['userId'], 'userInfo');
	
		?>

						<form name="editUser" action="?action=changePasswordDo" method="post">
								New Password: <input type="password" name="newPassword" id="newPassword" />
							<br />
								Confirm New Password: <input type="password" name="newPasswordConfirm" id="newPasswordConfirm" />
							<br />	
                            	<input type="hidden" name="id" id="id" value="<?php echo $sql['userId']; ?>" />
                            	<input type="submit" value="Save" />
                                
                        </form>
							
							<?php	
	
}

function changePasswordDo($password, $id){
	global $db, $data_validation;
	
		$sql['password'] 	= $data_validation->escape_sql($password);
		$sql['id'] 			= $data_validation->escape_sql($id);
	
	$db->query('UPDATE user 
							SET password=\''.md5($sql['password']).'\' 
							WHERE id=' . $sql['id'], 'update');
	header('Location:index.php');
}

function editUser($id){
	global $db, $data_validation;
	
	$sql['userId'] = $data_validation->escape_sql($id);

		$userInfo = $db->query('SELECT * FROM user WHERE id = ' . $sql['userId']);
		$userInfoArray = $userInfo->fetch_assoc();
						$html['firstName']	= $data_validation->escape_html($userInfoArray['firstName']);
						$html['lastName'] 	= $data_validation->escape_html($userInfoArray['lastName']);
						$html['username'] 	= $data_validation->escape_html($userInfoArray['username']);
						$html['id'] 		= $data_validation->escape_html($userInfoArray['id']);
						
					?>
						<div data-role="header" class="ui-header ui-bar-a" role="banner">
        <a href="javascript:window.history.back()" data-icon="back" class="ui-btn-left ui-btn ui-btn-up-a ui-shadow ui-btn-corner-all ui-btn-icon-left" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a"><span class="ui-btn-inner"><span class="ui-btn-text">Cancel</span><span class="ui-icon ui-icon-back ui-icon-shadow">&nbsp;</span></span></a>
        <h1 class="ui-title" role="heading" aria-level="1">Edit User</h1>
        
    </div>
						<form name="editUser" action="?action=editUserDo" method="post" style="margin-top:22px;">
								First Name: <input type="text" name="firstName" id="firstName" value="<?php echo $html['firstName'] ?>"/>
							<br />
								Last Name: <input type="text" name="lastName" id="lastName" value="<?php echo $html['lastName'] ?>"/>
							<br />
								Username: <input type="text" name="username" id="username" value="<?php echo $html['username'] ?>"/>
							<br />
								<a href="?action=changePassword&id=<?php echo $html['id'] ?>"><input type="button" name="password" value="Change Password" id="btnPassword"  /></a>
                            <br />
								<label for="permission">User Type</label>
                                <select name="permission" data-native-menu="false" >
                        
                                
                                   <?php
										$db->query('SELECT user_privilege.privilege_id AS asdf
													FROM user
														LEFT JOIN user_privilege
														ON user.id = user_privilege.user_id
													WHERE user.id = '.$sql['userId'], 'userPermission');
																										
										$html['userPrivilege'] = $db->result('userPermission', 0, 'asdf');
									
										$db->query('	SELECT id, name
														FROM privilege', 'permissions');
										while($row = $db->fetch_array('permissions')){
											//Escape data
												$html['id'] 	= $data_validation->escape_html($row['id']);
												$html['name'] 	= ucwords($data_validation->escape_html($row['name']));
												if($html['userPrivilege'] == $html['id']){
													echo '<option value="' . $html['id'] . '" id="' . $html['id'] . '" selected="selected">' . $html['name'] . '</option>';
												}else{
													echo '<option value="' . $html['id'] . '" id="' . $html['id'] . '" >' . $html['name'] . '</option>';
												}
										}
										
										
										
										
								   ?> 
								</select>
							<br />
								<input type="hidden" name="userId" id="userId" value="<?php  echo $sql['userId']; ?>" />
								<input type="submit" value="Save" />
						</form>
                       	
					<?php
}

function editUserDo($id, $firstName, $lastName, $username, $privilege){

			global $db, $data_validation;

			$sql['firstName'] 	= $data_validation->escape_sql($firstName);
			$sql['lastName'] 	= $data_validation->escape_sql($lastName);
			$sql['username'] 	= $data_validation->escape_sql($username);
			$sql['privilege'] 	= $data_validation->escape_sql($privilege);
			$sql['id'] 			= $data_validation->escape_sql($id);

				$db->query('UPDATE user 
							SET firstName=\''.$sql['firstName'].'\', 
								lastName=\''.$sql['lastName'].'\', 
								username=\''.$sql['username'].'\'
							WHERE id=' . $sql['id'], 'update');
				
				$db->query('UPDATE user_privilege
							
							SET privilege_id=\''.$sql['privilege'].'\'
							WHERE user_id='.$sql['id'].'');
						
			header('Location:index.php');
			
}
   
?>

	
	