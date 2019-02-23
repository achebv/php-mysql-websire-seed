<?php 

/**
 * Helper for files
 */
class HFile extends HBase{
	
	
	public $hname = 'file';
	

	public function init(){
		
	}


	/**
	 * read files from folder and return as array
	 * @param string $dir
	 */
	public function getFilesFromFolder($dir = ''){
		$files = array();
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if(!in_array($file, array('.', '..'))){
						$files[] = $file;
					}
				}
				closedir($dh);
			}
		}
		return $files;
	}
	
	
	/**
	 * 	Remove a file or folder with children
	 */
	public function remove($str){
		if(is_file($str)){
			return @unlink($str);
		}
		elseif(is_dir($str)){
			$scan = glob(rtrim($str,'/').'/*');
			foreach($scan as $index=>$path){
				$this->remove($path);
			}/*
			return @rmdir($str);
			if(!$removeDir){
				@mkdir($str);
			}*/
		}
	}
	
}