<?php
/*****************************************************************
 *	file path and name.php
 *	------------------------
 *  Created			: September 13, 2006
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2006 Twin Falls High School.
 *	Description		: This file include the classes and methods
 					  to draw all public and administrative header,
					  navigation and footer structures
 ****************************************************************/


/************************************************
 *	Initialize data_validation class
************************************************/

	$data_validation = new data_validation;

/************************************************
*	Begin data_validation class
************************************************/

	class data_validation{
	
		var $error;
		
		//This method is called on individual pages.
			//$array must be an array defining the type and value of data to be validated
		public function validate($array){
		
			//Initialize error value to FALSE
				$this->error = FALSE;
			
			//Loop through array to validate data
				foreach($array as $type => $value){
	
					//Evaluate safe data
						$this->safe($type, $value);
								
				}
			
			return TRUE();
		}
	
		private function evaluate_result($string){
		
			if($error){
				die($string);
			}
	
		}
		
		public function escape_clean($value){
		
			$temp_value = htmlentities($value, ENT_QUOTES, 'UTF-8');
			
			return $temp_value;
		}

		public function escape_safe($value){
		
			$temp_value = str_replace(' ', '', $value);
			
			if(eregi('^(<script)$', $temp_value)){
				die('Invalid code passed to page.');
			}else{
				return $value;
			}
		}

		public function escape_html($value){
		
			$temp_value = htmlentities(stripslashes($value), ENT_QUOTES, 'UTF-8');
			
			return $temp_value;
		}
	
		public function escape_sql($value){
		
			$temp_value = mysql_real_escape_string($value);
			
			return $temp_value;
		}
		
		public function encode_redirect($value){

			$temp_redirect = $value;
			if(substr($temp_redirect, 0, 1) != '/'){
				return urlencode($temp_redirect);
			}else{
				return urlencode('/new/index.php');

			}

		}

		public function decode_redirect($value){

			$temp_redirect = $value;
				return urldecode($temp_redirect);


		}
	
	
	/******************************************************/
	/***	BEGIN DATASTRUCTURE VALIDATION FUNCTIONS	***/
	/******************************************************/
	
		function safe($type, $value){
	
			//Validate for safe characters
				if(ereg('[^[:space:]a-zA-Z0-9_.,/;:\'"!\#@$%^&()+=*]{1,}', $value)){
					//Set error to TRUE if invalid characters exist
						$this->error = TRUE;
			   }
	
				//Stop application and return error if invalid characters were found.
					$this->evaluate_result('Invalid characters were passed to this page.  Please use your browser\'s back button and try again.  If you feel you have reached this message in error, please contact TFHSBruins.com at contact@tfhsbruins.com or 208-733-6551.');
	
				//Evaluate data structure given the type	
					if($type == 'num'){
						$this->num($value);
					}else if($type == 'alpha'){
						$this->alpha($value);
					}else if($type == 'alphanum'){
						$this->alphanum($value);
					}else if($type == 'username'){
						$this->username($value);
					}else if($type == 'password'){
						$this->password($value);
					}else if($type == 'address'){
						$this->address($value);
					}else if($type == 'city'){
						$this->city($value);
					}else if($type == 'state'){
						$this->state($type, $value);
					}else if($type == 'zip'){
						$this->zip($value);
					}else if($type == 'phone'){
						$this->phone($value);
					}else if($type == 'email'){
						$this->email($value);
					}else if($type == 'url'){
						$this->url($value);
					}else if($type == 'safe'){
						$this->safe_text($value);
					}
	
				return TRUE;
		}
		
		function num($value){
	
				if(!is_numeric($value)){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result('A numerical element was passed to this page with non-numerical data.');
	
		}
	
		function alpha($value){
	
				if(!ctype_alpha($value)){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result();
	
		}
		
		function alphanum($value){	
	
				if(ereg('[^[:space:]a-zA-Z0-9]{1,}', $value)){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result();
	
		}
		
		function username($value){
	
			//Validate safe characters
				if(	$this->safe('Username', $value)){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result();
	
		}
		
		function password($value){
	
			//Validate safe characters
				$this->safe('Password', $value);
				
				if(strlen($value) < 6){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result();
	
		}
		
		function state($value){
	
			//Validate safe characters
				$this->safe('State', $value);
				
				if(!ctype_alpha($value)){
					$this->error = TRUE;
				}
				if(strlen($value) != 2){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result();
	
		}
	
	
		function zip($value){
	
			//Validate safe characters
				$this->safe('zip', $value);
				
				if(	!is_numeric($value) ||
					strlen($value) != 5){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result();
	
		}
		
		function phone($value){
	
			//Validate xxx-xxx-xxxx format for phone numbers
				if($value != "" && (preg_match("/^[0-9]{3,3}[-]{1,1}[0-9]{3,3}[-]{1,1}[0-9]{4,4}$/", $value)) == FALSE){
					$this->error = TRUE;
				}	
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result('phone number');
	
		}
		
		function email($value){
	
			//Validate safe characters
				$this->safe('Email', $value);
				
				if(FALSE){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result('Email');
	
		}
		
		function url($value){
		
				if(FALSE){
					$this->error = TRUE;
				}
	
			//Stop application and return error if invalid characters were found.
				$this->evaluate_result('url');
	
		}
	
		function redirect($value){
			
			if(substr($value, 0, 1) != '/'){
					$this->error = TRUE;
			}
			
		}
	
		function richtext(){
			$var = TRUE;
		}
		
		function safe_text($value){
		
			$value = strtolower(str_replace(' ', '', $value));
			
			//Validate xxx-xxx-xxxx format for phone numbers
				if($value != "" && (preg_match("/^<script$/", $value)) == TRUE){
					$this->error = TRUE;
				}	
		
		}
	}
?>