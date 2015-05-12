<?php
/*****************************************************************
 *	inc/classes/auth.php
 *	------------------------
 *  Created			: October 11, 2006
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2006 Twin Falls High School.
 *	Description		: This class handles authentication including
 					  logging in, validating privileges, registering,
					  profile editing and logging out.
 ****************************************************************/


/************************************************
 *	Begin classes or functions
************************************************/

	//Instantiate authentication class
		$auth = new authentication;
			$auth->root_path 		= ROOT_PATH;
			$auth->db				= &$db;
			$auth->data_validation 	= &$data_validation;
			$auth->form			 	= &$form;
			$auth->functions	 	= &$functions;



	class authentication{

		var $root_path;
		var $db;
		var $data_validation;
		var $form;
		var $functions;
		var $allowedHosts = array(	'127.0.0.1',
									'96.5.145.174',
									'192.168.3.197');

		//Initialize construct method
			function authentication()	{
				//starts the session for user
				if(!session_id()){
					session_start();
				}
			}

	/******************************************
	*	New athentication class begins here	***
	******************************************/

		public function process_login($username, $password, $redirect = ''){

			if($username && $password){
				//Clean username and password
				$sql = array();
					$sql['username'] 	= $this->data_validation->escape_sql($username);
					$sql['password'] 	= md5($password);
					$html['redirect'] 	= $this->data_validation->escape_html($redirect);


				//Query database for user information
					$this->db->query('SELECT * FROM `user`
										WHERE `username` = \'' . $sql['username'] . '\'
											AND `password` = \'' . $sql['password'] . '\'', 'login');
								//or $com->error('Could not contact the database to validate your account.  Please try again.');

				//Validate user
					if($this->db->num_rows('login')){

						//Clean user information
							$html['user_id'] 			= $this->data_validation->escape_html($this->db->result('login', 0, 'id'));
							$html['first_name'] 		= $this->data_validation->escape_html($this->db->result('login', 0, 'firstName'));
							$html['last_name'] 			= $this->data_validation->escape_html($this->db->result('login', 0, 'lastName'));
							$html['full_name'] 			= ucwords($html['first_name'] . ' ' . $html['last_name']);
							$html['redirect'] 			= $this->data_validation->decode_redirect($redirect);

						//Register session variables
							$_SESSION['AUTHENTICATED']				= TRUE;					//Sets user as logged in
							$_SESSION['USER_ID'] 					= $html['user_id'];		//Sets user's id
							$_SESSION['FIRST_NAME'] 				= $html['first_name'];	//Sets user's full name
							$_SESSION['SESSION_REGENERATE_COUNTER'] = 0;					//Sets user's regenerate counting variable
							$_SESSION['SYSTEM_PRIVILEGES']			= array();

						//BEGIN SETTING PRIVILEGES FOR USER
							//Privileges are set in the $_SESSION['SYSTEM_PRIVILEGES'] array
							$sql['user_id'] = $this->data_validation->escape_sql($html['user_id']);

							$this->db->query('	SELECT privilege.name
												FROM user_privilege
												LEFT JOIN privilege
													ON privilege.id = user_privilege.privilege_id
												WHERE user_privilege.user_id = ' . $sql['user_id'], 'privileges');

							while($row = $this->db->fetch_array('privileges')){
								//Escape data
									$privilege = $this->data_validation->escape_html($row['name']);
								//Validate that the privilege doesn't already exist
									if(!in_array($privilege, $_SESSION['SYSTEM_PRIVILEGES'])){
										$_SESSION['SYSTEM_PRIVILEGES'][] = $privilege;
									}
							}


						//END SETTING PRIVILEGES FOR USER

						//Redirect user
							if($html['redirect']){
								header('location:' . $this->root_path . $html['redirect']);
							}else{
								header('location:' . $this->root_path . 'admin/');
							}

					} else {
						header('location:index.php?message=Your user credentials could not be verified.  Please try again.');
						exit();
					}
			}else{
				return FALSE;
			}
		}


		public function logout(){

			//Set authenticated variable to FALSE
				$_SESSION['AUTHENTICATED'] = FALSE;

			//Destroy session
				session_destroy();
			//Redirect user
				header('Location:' . $this->root_path . '/admin');
				die();
		}

		private function regenerate_session(){

			if(isset($_SESSION['SESSION_REGENERATE_COUNTER'])){

				if($_SESSION['SESSION_REGENERATE_COUNTER'] == 15){

					//Regenerate session
						session_regenerate_id();

					//Reload privileges
						$this->reload_privileges();

					//Reset counting variable
						$_SESSION['SESSION_REGENERATE_COUNTER'] = 0;

				}else{

					//Increment counting variable
						$_SESSION['SESSION_REGENERATE_COUNTER'] += 1;

				}
			}

		}

		private function require_ssl(){

			//if($_SERVER['SERVER_PROTOCOL'] != 'https'){
			//	header('location:https://' . $_SERVER['HTTP_HOST'] . $_SERVER['QUERY_STRING']);
			//}
		}

		private function reload_privileges(){

			//FLUSH PRIVILEGES
				$_SESSION['SYSTEM_PRIVILEGES'] = array();

			//BEGIN RELOAD PRIVILEGES FOR USER
				//Privileges are set in the $_SESSION['SYSTEM_PRIVILEGES'] array
				$sql['user_id'] = $this->data_validation->escape_sql($_SESSION['USER_ID']);

				$this->db->query('	SELECT privilege.name
									FROM user_privilege
									LEFT JOIN privilege
										ON privilege.id = user_privilege.privilege_id
									WHERE user_privilege.user_id = ' . $sql['user_id'], 'privileges');

				while($row = $this->db->fetch_array('privileges')){
					//Escape data
						$privilege = $this->data_validation->escape_html($row['name']);
					//Validate that the privilege doesn't already exist
						if(!in_array($privilege, $_SESSION['SYSTEM_PRIVILEGES'])){
							$_SESSION['SYSTEM_PRIVILEGES'][] = $privilege;
						}
				}

			//END RELOAD PRIVILEGES FOR USER
		}

		public function validate_user_access($privilege){

			if($privilege == 'PUBLIC'){

				return TRUE;

			}else if($privilege == 'AUTH'){

				//Force SSL connection
					$this->require_ssl();

				//Regenerate session
					$this->regenerate_session();

				//Validate that the user is authenticated
					$this->require_authenticated();
			}else{
				header('location:' . $this->root_path . 'admin/');
				die();
			}
		}

		public function require_allowed_host(){
			if(!in_array($_SERVER['REMOTE_ADDR'], $this->allowedHosts)){
				header('location:http://www.tfhsbruins.com');
				exit();
			}
		}

 		public function require_authenticated(){

			if(!isset($_SESSION['AUTHENTICATED']) || $_SESSION['AUTHENTICATED'] != TRUE)	{
				header('location:' . $this->root_path . 'admin/');
				exit();
			}
		}

		public function check_authenticated(){
			if(isset($_SESSION['AUTHENTICATED']) && $_SESSION['AUTHENTICATED'] == TRUE)	{
				return TRUE;
			}
		}

		public function require_privilege(){

			//Require that user is authenticated
				$this->require_authenticated();

			//Grab arguements passed to method
				$required_privileges = func_get_args();

			//Grab user privileges from session variable
				$temp_privileges = $_SESSION['SYSTEM_PRIVILEGES'];

			//Check for system privilege
				if(count($_SESSION['SYSTEM_PRIVILEGES'])){
					$hasPrivilege = false;
					foreach($required_privileges as $index => $access_value){
						if(in_array($access_value, $temp_privileges)){
							$hasPrivilege = true;
						}
					}
					if(!$hasPrivilege){
						header('location:' . $this->root_path . 'admin/');
						exit();
					}else{
						return true;
					}

				}else{
					header('location:' . $this->root_path . 'admin/');
					exit();
				}
			header('location:' . $this->root_path . 'admin/');
			exit();
		}

		public function check_privilege(){

			//Grab arguements passed to method
				$required_privileges = func_get_args();
				//Add global user to array
					array_push($required_privileges, 'global');

			//Check system privileges
				if(isset($_SESSION['SYSTEM_PRIVILEGES']) && count($_SESSION['SYSTEM_PRIVILEGES'])){

					$temp_privileges = $_SESSION['SYSTEM_PRIVILEGES'];

						foreach($required_privileges as $index => $access_value){
							if(in_array($access_value, $temp_privileges)){
								return TRUE;
							}
						}
				}
		}


	}
?>
