<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Photo extends CI_Controller {

	public $img_length 		= 580;

	public function index()
	{
	}

	public function uploadPreviewImage() {
		$error 				= '';
		$file_location 		= '';
		$file_folder		= config_item('upload_url');
		$base_url			= config_item('base_url');
		$file_name 			= 'uploadFile';
		$file_max_size		= config_item('file_max_size');
		$extension 			= end(explode(".", $_FILES[$file_name]["name"]));
		$allowed_exts		 = array("jpg", "jpeg", "gif", "png");

		if ((($_FILES[$file_name]["type"] == "image/gif")
		|| ($_FILES[$file_name]["type"] == "image/jpeg")
		|| ($_FILES[$file_name]["type"] == "image/png")
		|| ($_FILES[$file_name]["type"] == "image/pjpeg"))
		&& ($_FILES[$file_name]["size"] < $file_max_size)
		&& in_array($extension, $allowed_exts))
		{
			if(!empty($_FILES[$file_name]['error']))
			{
				switch($_FILES[$file_name]['error'])
				{
					case '1':
						$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2':
						$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3':
						$error = 'The uploaded file was only partially uploaded';
						break;
					case '4':
						$error = 'No file was uploaded.';
						break;
					case '6':
						$error = 'Missing a temporary folder';
						break;
					case '7':
						$error = 'Failed to write file to disk';
						break;
					case '8':
						$error = 'File upload stopped by extension';
						break;
					case '999':
					default:
						$error = 'No error code avaiable';
				}
			}

			elseif(empty($_FILES[$file_name]['tmp_name']) || $_FILES[$file_name]['tmp_name'] == 'none') {
				$error = 'No file was uploaded.';
			}

			else {				
				$tmp_name 	   = $_FILES[$file_name]['tmp_name'];
		        $name 	  	   = 'temp_' . time() . '.' . $extension;
		        $file_location = $file_folder . $name;
		        
		        //upload a file first
		        move_uploaded_file($tmp_name, $file_location);

				// Set a min height and width
				$w  = $this->img_length;
				$h = $this->img_length;

				// Get new dimensions
				list($o_w, $o_h) = getimagesize($file_location);

				$o_ratio = $o_w/$o_h;

				if ($w/$h < $o_ratio) {
				   $w = $h * $o_ratio;
				} else {
				   $h = $w / $o_ratio;
				}

				// Resample
				$canvas = imagecreatetruecolor($w, $h);
				$image = imagecreatefromjpeg($file_location);
				imagecopyresampled($canvas, $image, 0, 0, 0, 0, $w, $h, $o_w, $o_h);
				
				// Output
				ob_start (); 
				imagejpeg( $canvas, null, 70 );
				$output = ob_get_contents (); 
				ob_end_clean (); 
				file_put_contents( $file_location , $output );
			}
		}
		else {
			$error = "Please use an image in JPG, GIF and PNG format under " . ($file_max_size/1000000) ."mb.";
		}
		
		$return['error'] = $error;
		$return['file_name'] = $name;
	    $return['file_location'] = $file_location;
	   	echo json_encode( $return );
	}

	public function saveFile(){
		$data = $this->input->post();
		if(!empty($data)){
			$len = $this->img_length;
			$canvas = imagecreatetruecolor($len, $len);
			$image = imagecreatefromjpeg( $data['filePath'] );

			// Resample image
			$o_w = imagesx( $image );
			$o_h = imagesy( $image );
			imagecopyresampled($canvas, $image, $data['x'], $data['y'], 0,0, $o_w, $o_h, $o_w, $o_h);

			$result = $this->saveOutput($canvas, $data);

			//Delete temporary file
			unlink($data['filePath']);
			return $result;
		}
		else{
			echo 'Invaild access.';
		}
	}

	public function saveRawFile() {
		$data = $this->input->post();
		if(!empty($data)){
			$base64data = explode('base64,', $data['base64data'] );
			$canvas 	= imagecreatefromstring( base64_decode($base64data[1]) );
			$result 	= $this->saveOutput($canvas, $data);
			return $result;
		}
		else{
			echo 'Invaild access.';
		}
	}

	private function saveOutput($img, $data){

		$name 			= 'photo_' . time() .".jpg";
		$file_location 	= config_item('upload_url') . $name;
		$php_date 		= new DateTime();
		$time_date 		= $php_date->format('Y-m-d H:i:s');

		// Save image first
		ob_start (); 
		imagejpeg( $img, null, 70 );
		$output = ob_get_contents (); 
		ob_end_clean (); 
		file_put_contents( $file_location , $output );
		unset($img);

	    //Save to Database
		// save to Photo table of circleId and usersFbId is invalid. Save to Circle Photo table if valid.
		if(empty($data['circleId']) && empty($data['usersFbId']) ){
			$post = array(
				'date'			=> $time_date,
				'description'	=> $data['desc'],
				'filename'		=> $name,
				);
			$this->load->model('photos_model');
			$result = $this->photos_model->Add($post);
		}
		else{
			$post = array(
				'ref_circle_id'	=> $data['circleId'],
				'description'	=> $data['desc'],
				'filename'		=> $name,
				'users_fb_id'	=> $data['usersFbId']
				);
			$this->load->model('circle_photos_Model');
			$result = $this->circle_photos_Model->Add($post);
		}

		if ($result){
			$data['id'] = $result;
			$data['file_name'] = $name;
	    	$data['file_location'] = $file_location;
		}
		else{
			$data['error'] = 'Write failed';
		}
		echo json_encode($data);
	}

	public function fetchUploadedPhotoData()
	{
		$this->load->model('photos_model');
		$this->post = $this->input->post();
		if ( isset ( $this->post['photo_id'] )) {	

			$photo_id = $this->post['photo_id'];
			$data = array();
			$query = $this->db->query("SELECT * FROM photos WHERE id='$photo_id'"); 

			if ($query->num_rows() > 0) {
			  foreach($query->result() as $row) {
			    $data['photo_id'] = $row->id;
			    $data['filename'] = $row->filename;
			    $data['description'] = $row->description;
			  }

			  echo json_encode($data);
			}
			
		}
		else
			echo 'Invalid access';
	}

}
