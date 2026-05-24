<?php
	function upload_image($idField)
	{
		if(isset($_FILES[$idField]))
		{
			if (file_exists('../public/uploads/' . $_FILES[$idField]['name'])) {
				unlink('../public/uploads/' . $_FILES[$idField]['name']);
			}
			$extension = explode('.', $_FILES[$idField]['name']);
			$new_name = rand() . '.' . $extension[1];
			$destination = '../public/uploads/' . $new_name;
			move_uploaded_file($_FILES[$idField]['tmp_name'], $destination);
			return $new_name;
		}
	}
?>