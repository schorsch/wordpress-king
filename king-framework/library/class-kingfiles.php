<?
/**
* King File + Directory handler Class
* Original Version 1.1 by by Johan De Klerk (johan@wisi.co.za) jFileDir
* Freely Distributable
* 
* Log:
* 2003/09/15: function setCurrentDir added by Ulrich Zdebel
*             Modified populate function
*
*ToDo
* - implement recursive copying can be taken from class_dirtool-2005-12-29
* -mabye need for chmodding can be taken from filemanager_class
*/

class KingFiles {
	var $current_dir;
	var $current_dir_handle;
	var $current_files = array();
	var $current_dirs = array();
	
	function kingFiles() {
		$this->current_dir = getcwd();

		$this->current_dir_handle = @opendir($this->current_dir);
		
		$this->populate();
	}
	//public methods
	function createDir($dirname,$where='') {
		if (strcmp($where,'')) {
			$where = $this->current_dir;
		}

		if (!is_dir($dirname)) {
			mkdir($dirname) or die('ERROR: Could not create directory!');
		}
		else {
			die('ERROR: '.$dirname.' already exists!');
		}
	}
	function removeDir($dirname) {
		if (is_dir($dirname)) {
			rmdir($dirname) or die ('ERROR: Could not remove directory, make sure it is empty!');
		}
		else {
			die('ERROR: No such directory exists!');
		}
	}
	function emptyDir($Dir) {
		if ($handle = @opendir($Dir)) {
			while (($file = readdir($handle)) !== false) {

				if ($file == "." || $file == "..") {
					continue;
				}

				if (is_dir($Dir.$file)){
					$this->emptyDir($Dir.$file."/");
					#chmod($Dir.$file,0777) or die('ERROR: Could not change permissions!');
					rmdir($Dir.$file) or die('ERROR: Could not remove directory '.$Dir);
				}else {
					#chmod($Dir.$file,0777) or die('ERROR: Could not change permissions!');
					unlink($Dir.$file) or die('ERROR: Could not delete file!');
				}
			}
		}
	@closedir($handle);
	}
	function copyFile($filename,$to='',$as='') {
		if (!strcmp($as,'')) {
			$as = $filename;
		}

		if (!strcmp($to,'')) {
			$dest = $as;
		}
		else {
			$dest = $to.'/'.$as;
		}
		copy($filename,$dest) or die('ERROR: Could not copy file!');
	}

	function deleteFile($filename) {
		if (file_exists($filename)) {
			unlink($filename) or die('ERROR: Could not delete file!');
		}
	}

	//accessor methods - GET
	function getCurrentDir() {
		return $this->current_dir;
	}

	function getCurrentDirHandle() {
		return $this->current_dir_handle;
	}

	function getFiles() {
		return $this->current_files;
	}
	
	function getDirectories() {
		return $this->current_dirs;
	}
	
	//accessor methods - SET - uz
	function setCurrentDir($dir) {
		if( @chdir($dir) ) {			
			$this->kingFiles();
			return true;
		} else
		return false;
	}
		
	//private methods
	function populate() {
		$i = 0;
		$j = 0;
		$this->current_dirs = array();
		$this->current_files = array();

		// read the contents of the current directory
		while ($contents = readdir($this->current_dir_handle)) {
					// collect all files in current directory
			if (is_dir($contents)) {
				if ($contents != '.' && $contents != '..') {
					$this->current_dirs[$j] = $contents;  
					$j++;
				}
			}
			// collect all directories in current directory
			elseif (is_file( $contents )) {
				$this->current_files[$i] = $contents;
				$i++;
			}
		}
		
		closedir($this->current_dir_handle);
	}
}
?>