<?php
/*****************************************************************
 *	inc/classes/functions.php
 *	------------------------
 *  Created			: September 25, 2006
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2006 Twin Falls High School.
 *	Description		: This class holds global functions used in
 					  the entire web application
 ****************************************************************/


/************************************************
 *	Initialize data_validation class
************************************************/

	$functions = new functions;
		$functions->db = $db;
		$functions->data_validation = $data_validation;
		$functions->root_path = ROOT_PATH;

/************************************************
 *	Begin data_validation class
************************************************/

	class functions{

		var $db, $data_validation, $location;

	/*****************************************
	**			DATE FUNCTIONS				**
	*****************************************/

		function current_school_year(){

			//create start as July 1, 2006 using mysql_timestamp
				//Find out how many days today is from the start
				//Divide now by 365 and use that value to define a school year
			$date_start = strtotime('2006-07-01');
			$year_start = 2006;

			$now = strtotime(date('Y-m-d'));

			$time_difference 	= $now - $date_start;
			$years_difference 	= (integer)($time_difference / 31536000);

			$current_year = (integer)($year_start + $years_difference);

			$current_school_year = $current_year . '-' . ($current_year + 1);

			return $current_school_year;

		}

		function generate_school_year($start_year){

			//create start as July 1, 2006 using mysql_timestamp
				//Find out how many days today is from the start
				//Divide now by 365 and use that value to define a school year

			if(is_numeric($start_year)){
				$year_start = $start_year;
			}else{
				die('Invalid date passed to school year function.');
			}

			$date_start = strtotime('2006-08-01');

			$now = strtotime(date('Y-m-d'));

			$time_difference 	= $now - $date_start;
			$years_difference 	= (integer)($time_difference / 31536000);

			$current_year = (integer)($year_start + $years_difference);

			$current_school_year = $current_year . '-' . ($current_year + 1);

			return $current_school_year;

		}

	/*****************************************
	**			STATISTICAL FUNCTIONS		**
	*****************************************/

		function log_pageview(){

			//Log page view

				$page_name = $this->data_validation->escape_sql($_SERVER['PHP_SELF']);;

				//Check to see if the page_name exists in the database
					$this->db->query('	SELECT page_name
										FROM system_statistic_pageview
											WHERE page_name = \'' . $page_name . '\'', 'pageview');

					//Create record for page if it doesn't exist
						if(!$this->db->num_rows('pageview')){
							$this->db->query('INSERT INTO system_statistic_pageview
													(page_name, views)
												VALUES
													(\'' . $page_name . '\', 1)', 'log_pageview');
						}else{

						//Increment the views field by 1 for the current page
							$this->db->query('	UPDATE system_statistic_pageview
												SET views = (views + 1)
												WHERE page_name = \'' . $page_name . '\'', 'log_pageview');
						}
		}

		function system_log_page_error(){


			//Initialize variables
				$sql['ip_address'] 		= $this->data_validation->escape_sql($_SERVER['REMOTE_ADDR']);
				if(isset($_SESSION['username'])){
					$sql['username'] 	= $this->data_validation->escape_sql($_SESSION['username']);
				}else{
					$sql['username'] 	= 'Not Authenticated';
				}
				$sql['page'] 			= $this->data_validation->escape_sql($_SERVER['PHP_SELF']);
				$sql['querystring'] 	= $this->data_validation->escape_sql($_SERVER['QUERY_STRING']);
				$sql['http_headers'] 	= $this->data_validation->escape_sql(apache_request_headers());

				$this->db->query('INSERT INTO system_log_page_error
									(ip_address, username, page, querystring, http_headers)
									VALUES
									(	' . $sql['ip_address'] . ',
										' . $sql['username'] . ',
										' . $sql['page'] . ',
										' . $sql['querystring'] . ',
										' . $sql['http_headers'] . ')', 'log_page_error');

				header('location:http://www.tfhsbruins.com');

		}

		function system_log_email($to_email, $from_email, $from_fullname, $headers, $subject, $message){

			//Initialize variables
				$sql['ip_address'] 		= $this->data_validation->escape_sql($_SERVER['REMOTE_ADDR']);
				$sql['to_email'] 		= $this->data_validation->escape_sql($to_email);
				$sql['from_email'] 		= $this->data_validation->escape_sql($from_email);
				$sql['from_fullname']	= $this->data_validation->escape_sql($from_fullname);
				$sql['headers'] 		= $this->data_validation->escape_sql($headers);
				$sql['subject'] 		= $this->data_validation->escape_sql($subject);
				$sql['message'] 		= $this->data_validation->escape_sql($message);

			//Insert email record into databaseu
				$this->db->query('INSERT INTO system_log_email
									(ip_address, email_to, email_from, fullname_from, headers, subject, message)
									VALUES
									(	' . $sql['ip_address'] . ',
										' . $sql['to_email'] . ',
										' . $sql['from_email'] . ',
										' . $sql['from_fullname'] . ',
										' . $sql['headers'] . ',
										' . $sql['subject'] . ',
										' . $sql['message'] . ')', 'log_email');

		}



	/*****************************************
	**		DEBUGGING FUNCTIONS				**
	*****************************************/

		function debug(){

			echo '<p><strong>APACHE REQUEST HEADERS</strong></p><blockquote>';
				$headers_array = apache_request_headers();
				foreach($headers_array as $name => $value){
					echo $name . ' = ' . $value . '<br/>';
				}
			echo '</blockquote><p><strong>GET Headers:</strong></p><blockquote>';
				$get_array = $_GET;
				foreach($get_array as $name => $value){
					echo $name . ' = ' . $value . '<br/>';
				}
			echo '</blockquote><p><strong>POST Headers:</strong></p><blockquote>';
				$post_array = $_POST;
				foreach($post_array as $name => $value){
					echo $name . ' = ' . $value . '<br/>';
				}
			echo '</blockquote>';

			die();

		}


	/*****************************************
	**			USER FUNCTIONS				**
	*****************************************/

		function system_send_mail($to, $subject, $message, $headers){

			//Send email
				$email = mail($to, $subject, $message, $headers);
				if(!$email){
					die('<p />The system is currently unable to send mail.  Please contact ' . EMAIL_ACCOUNT_SUPPORT . ' for assistance.');
				}
			//Log email
				$this->system_log_email($to, 'System', 'TFHSBruins.com', $headers, $subject, $message);
		}

	/*****************************************
	**			IMAGE FUNCTIONS				**
	*****************************************/

		function display_image($image_id, $directory, $filename, $alt, $height = ''){

			//Escape variables
				$html['image_id'] 	= $this->data_validation->escape_html($image_id);
				$html['directory'] 	= $this->data_validation->escape_html($directory);
				$html['filename'] 	= $this->data_validation->escape_html($filename);
				$html['alt'] 		= $this->data_validation->escape_html($alt);
				$html['height']		= $this->data_validation->escape_html($height);

				echo '<a href="' . $this->root_path . 'image_view.php?image_id=' . $html['image_id'] . '" title="View fullsize image">
						<img src="' . $this->root_path . 'files/images/' . $html['directory'] . '/' . $html['filename'] . '" border="0" alt="' . $html['alt'] . '" height="' . $html['height'] . '" />
					  </a>';

		}


	}
?>
