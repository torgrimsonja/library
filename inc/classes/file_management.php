<?php
/*****************************************************************
 *	inc/classes/file_management.php
 *	------------------------
 *  Created			: September 27, 2007
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2007 Twin Falls High School.
 *	Description		: This class includes all methods used to upload
 					  and validate files to the file system.
 ****************************************************************/


/************************************************
 *	Initialize form class
************************************************/

	$upload = new file_management;

/*****************************
**		Begin Class			**
*****************************/

	class file_management{

		//Valid file types
			var $valid_image_types 		= 	array(	'jpg', 'gif', 'png', 'jpeg', 'nef');
			var $valid_video_types 		= 	array(	'wmv', 'mov', 'mpg', 'asf');
			var $valid_document_types 	= 	array(	'doc', 'rft', 'pdf', 'txt', 'docx', 'pub');
			var $valid_audio_types 		= 	array(	'mp3', 'wma', 'asf');

		//Max file sizes
			var $max_image_size 	= 5120000;
			var $max_video_size 	= 20971520000;
			var $max_document_size 	= 2048000;
			var $max_audio_size 	= 10485760000;

		//Max width
			var $max_image_width	= 1024;

		//Max height
			var $max_image_height = 768;

		//Default file directory
			var $location = ROOT_FILES_DIRECTORY;


		private function create_directory($directory){

			if($this->check_directory_exists($directory)){
				return TRUE;
			}else{

				if(mkdir($directory)){
					return TRUE;
				}else{
					return FALSE;
				}
			}

		}


		private function check_directory_exists($directory){

				$absolute_dir = preg_replace("/(.*)(\/)$/","\\1", $directory);

				if (!is_dir($absolute_dir)) {

					if(	mkdir($absolute_dir, 0777) &&
						chmod($absolute_dir, 0777)){

						return $absolute_dir;

					}else{

						return FALSE;

					}
				}else{
					return $absolute_dir;
				}

		}


		private function check_file_exists($directory, $filename){

			if($this->check_directory_exists($directory)){

				$basefilename 	= preg_replace("/(.*)\.([^.]+)$/","\\1", $filename);
				$extension 		= preg_replace("/.*\.([^.]+)$/","\\1", $filename);

				//Counting variable for changing name
					$i = 1;

				while(file_exists($directory . '/' . $basefilename . $i . '.' . $extension)){

					$i++;

				}

				$new_filename = $basefilename . $i . '.' . $extension;

				return $new_filename;

			}else{

				die('Error: The directory in which your are trying to upload this file does not exist.  Please try again.');

			}

		}

		public function delete_file($source_directory, $filename){

			//Remove file if it exists
				if (file_exists($source_directory . '/' . $filename)) {

					if (unlink($source_directory . '/' . $filename)) {
						return TRUE;
					}else{
						return FALSE;
					}

				}else{
					die('Error: The system can not find the specified file and thus can not delete it.  Please try again.');
				}
			}

		private function check_gd_version(){

			$gd_content = get_extension_funcs('gd'); // Grab function list
			if (!$gd_content) {
				$this->message_err('Upload class: GD libarary is not installed!', '', '', E_USER_ERROR);

				return false;
			} else {
				ob_start();
				phpinfo(8);
				$buffer = ob_get_contents();
				ob_end_clean();

				if (strpos($buffer, '2.0')) {
					return 'gd2';
				} else {
					return 'gd';
				}
			}
		}


		public function upload_file($form_field_name, $target_directory = ROOT_FILES_DIRECTORY){

			//Validate that directory exists
				$directory = $this->check_directory_exists($target_directory);

				if (!empty($_FILES[$form_field_name]['name'])) {

					if (is_uploaded_file($_FILES[$form_field_name]['tmp_name'])) {

						$filename 		= strtolower(str_replace(' ', '', $_FILES[$form_field_name]['name']));
						$filename 		= strtolower(str_replace('%20', '', $filename));
						$temp_name		= $_FILES[$form_field_name]['tmp_name'];
						$basefilename 	= preg_replace("/(.*)\.([^.]+)$/","\\1", $filename);
						$extension 		= preg_replace("/.*\.([^.]+)$/","\\1", $filename);
						$filename 		= $basefilename . '.' . $extension;

						if (	!in_array($extension, $this->valid_image_types) &&
								!in_array($extension, $this->valid_document_types) &&
								!in_array($extension, $this->valid_video_types)&&
								!in_array($extension, $this->valid_audio_types)) {

							die('Error: The system does not accept that file type.  Please try again with another.');

						} else {

	########################################## HANDLE IMAGE UPLOAD##########################################

							if(in_array($extension, $this->valid_image_types)){

								//Validate that file is not larger than the maximum file size allowed
									if ($_FILES[$form_field_name]['size'] > $this->max_image_size) {

										die('Error: The file exceeds the maximum filesize of ' . (string)$this->max_image_size . ' bytes.  Please compress your file and try again..');

									}

								//Move uploaded image to the fullsize folder
								//The permission value for this object is failing at the file system level
									//Need to address this on the server
									if (!move_uploaded_file($temp_name, $target_directory . 'images/fullsize/' . $filename)) {

										die('Error: The system was unable to upload your image file.  Please try again. (Move attempt failed.)');

									}

								//Encode filename
									$new_filename = $this->check_file_exists($this->location . 'images/fullsize', $filename);

								//Rename file to new encoded filename
									if(!rename($target_directory . 'images/fullsize/' . $filename, $target_directory . 'images/fullsize/' . $new_filename)) {

										//delete uploaded image
											unlink($target_directory . 'images/fullsize/' . $filename);

										die('Error: The system was unable to upload your image file.  Please try again. (Renaming attempt failed.)');

									}

								//Copy and resize image to the small folder
										$this->copy_and_resize_image($target_directory . 'images/fullsize', $target_directory . 'images/small', $new_filename, '320');

								//Copy and resize image to the thumbs folder
										$this->copy_and_resize_image($target_directory . 'images/fullsize', $target_directory . 'images/thumbs', $new_filename,  '100');

	########################################## HANDLE DOCUMENT UPLOAD ##########################################
							}else if(in_array($extension, $this->valid_document_types)){

								//Encode filename
									$new_filename = $this->check_file_exists($this->location . 'documents', $filename);

								//Validate that file is not larger than the maximum file size allowed
									if ($_FILES[$form_field_name]['size'] > $this->max_document_size) {

										die('Error: The file exceedes the maximum filesize of ' . $this->max_document_size . ' bytes.  Please compress your file and try again.');

									}

								//Move uploaded file to the files/document folder
									if (!move_uploaded_file($_FILES[$form_field_name]['tmp_name'], $target_directory . 'documents/'.$filename)) {
										die('Error: The system was unable to upload your document file.  Please try again.');
									}

								//Rename file to new encoded filename
									if(!rename($target_directory . 'documents/' . $filename, $target_directory . 'documents/' . $new_filename)) {

										//delete uploaded image
											unlink($target_directory . 'documents/' . $filename);

										die('Error: The system was unable to upload your image file.  Please try again. (Renaming attempt failed.)');

									}

	########################################## HANDLE VIDEO UPLOAD ##########################################

							}else if(in_array($extension, $this->valid_video_types)){

								//Encode filename
									$new_filename = $this->check_file_exists($this->location . 'video/', $filename);

								//Validate that file is not larger than the maximum file size allowed
									if ($_FILES[$form_field_name]['size'] > $this->max_video_size) {

										die('Error: The file exceeds the maximum filesize of ' . (string)$this->max_video_size . ' bytes.  Please compress your file and try again..');

									}

								//Move uploaded file to the files/video folder
									if (!move_uploaded_file($_FILES[$form_field_name]['tmp_name'], $target_directory . 'video/'.$filename)) {
										die('Error: The system was unable to upload your video file.  Please try again.');
									}

								//Rename file to new encoded filename
									if(!rename($target_directory . 'video/' . $filename, $target_directory . 'video/' . $new_filename)) {

										//delete uploaded image
											unlink($target_directory . 'video/' . $filename);

										die('Error: The system was unable to upload your image file.  Please try again. (Renaming attempt failed.)');

									}

	########################################## HANDLE AUDIO UPLOAD ##########################################

							}else if(in_array($extension, $this->valid_audio_types)){

								//Encode filename
									$new_filename = $this->check_file_exists($this->location . 'audio', $filename);

								//Validate that file is not larger than the maximum file size allowed
									if ($_FILES[$form_field_name]['size'] > $this->max_audio_size) {

										die('Error: The file exceeds the maximum filesize of ' . (string)$this->max_audio_size . ' bytes.  Please compress your file and try again.');

									}

							//Move uploaded file to the files/video folder
									if (!move_uploaded_file($_FILES[$form_field_name]['tmp_name'], $target_directory . 'audio/'.$filename)) {
										die('Error: The system was unable to upload your video file.  Please try again.');
									}

									if(!rename($target_directory . 'audio/' . $filename, $target_directory . 'audio/' . $new_filename)) {

										//delete uploaded image
											unlink($target_directory . 'audio/' . $filename);

										die('Error: The system was unable to upload your image file.  Please try again. (Renaming attempt failed.)');

									}

							}

							//Return value
								return $new_filename;
						}

					} else {
						die('Error: The system was unable to upload your file.');
					}
				}else{
					return '';
				}

		}


		public function copy_file($filename, $current_directory, $copy_to_directory){

			$current_path 	= $current_directory . '/' . $filename;
			$copy_to_path 	= $copy_to_directory . '/' . $filename;

			//Check that file exists
				if($this->check_file_exists($directory, $filename)){

					copy($current_path, $copy_to_path);
					return TRUE;

				}else{

					return FALSE;

				}

			//Copy file to new location

		}


		private function copy_and_resize_image($source_directory, $destination_directory, $filename, $destination_width){

			//Copy file to the new location
				copy($source_directory . '/' . $filename, $destination_directory . '/' . $filename);

			//$source_dir = $this->checkDir($source_dir);
				$full_path 		= $destination_directory . '/' . $filename;
				$basefilename 	= preg_replace("/(.*)\.([^.]+)$/","\\1", $filename);
				$extension 		= preg_replace("/.*\.([^.]+)$/","\\1", $filename);

				switch ($extension) {
				case 'png':
					$image = imagecreatefrompng($full_path);
					break;

				case 'jpg':
					$image = imagecreatefromjpeg($full_path);
					break;

				case 'jpeg':
					$image = imagecreatefromjpeg($full_path);
					break;

				case 'gif':
					$image = imagecreatefromgif($full_path);
					break;

				default:
					die('Error: the ' . $extension . ' format is not allowed in your GD version');
					break;
				}

				$image_width = imagesx($image);
				$image_height = imagesy($image);


				#####THIS IS CUTTING OFF PART OF THE PICTURE IF IT IS OVER THE MAX IMAGE WIDTH#####
						// resize image pro rata
							//if($image_width > $this->max_image_width){
								//$image_width = $this->max_image_width;
							//}
				#####THIS IS CUTTING OFF PART OF THE PICTURE IF IT IS OVER THE MAX IMAGE WIDTH#####

				$coefficient = ($image_width > $destination_width) ? (real)($destination_width / $image_width) : 1;
				$dest_width = (int)($image_width * $coefficient);
				$dest_height = (int)($image_height * $coefficient);

				if ('gd2' == $this->check_gd_version()) {
					$img_id = imagecreatetruecolor($dest_width, $dest_height);
					imagecopyresampled($img_id, $image, 0, 0, 0, 0, $dest_width + 1, $dest_height + 1, $image_width, $image_height);

				} else {
					$img_id = imagecreate($dest_width, $dest_height);
					imagecopyresized($img_id, $image, 0, 0, 0, 0, $dest_width + 1, $dest_height + 1, $image_width, $image_height);
				}

				switch ($extension) {
				case 'png':
					imagepng($img_id, $destination_directory . '/' . $filename);
					break;

				case 'jpg':
					imagejpeg($img_id, $destination_directory . '/' . $filename);
					break;

				case 'jpeg':
					imagejpeg($img_id, $destination_directory . '/' . $filename);
					break;
				}

				imagedestroy($img_id);
		}



	}


?>
