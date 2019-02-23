<?php

/**
 * Helper for files
 */
class HValid extends HBase{


	public $hname = 'valid';


	private $valid = TRUE;


	private $errFields = array();


	/**
	 * test if a description with html for example is set
	 * @return
	 * @param object $form_vars
	 */
	public function filled_out_html($form_vars, $fromPost = true){
		if(is_array($form_vars)){
			foreach ($form_vars as $field){
				$this->filled_out_html($field, $fromPost);
			}
		}else{
			$value = $fromPost ? $this->getPost($form_vars) : $form_vars;
			echo $value."::";
			$value = str_replace("&nbsp;", "", $value);
			$value = str_replace(" ", "", $value);
			$value = trim($value);
			$value = strip_tags($value);
			$value = trim($value);
			if (strlen($value)<1 || ($value == '')){
				$this->errFields[] = $form_vars;
				$this->valid = FALSE;
			}
		}
	}


	/**
	 * Form fill
	 *
	 * @param Array $form_vars
	 * @return Bool
	 */
	public function filled_out($form_vars, $fromPost = true){
		if(is_array($form_vars)){
			foreach ($form_vars as $field){
				$this->filled_out($field, $fromPost);
			}
		}else{
			$value = $fromPost ? $this->getPost($form_vars) : $form_vars;
			if (strlen(trim($value))<1 || ($value == '')){
				$this->errFields[] = $form_vars;
				$this->valid = FALSE;
			}
		}
	}

	public function setErrField($fld){
		$this->errFields[] = $fld;
		$this->valid = FALSE;
	}



	/**
	 * Validate email address
	 *
	 * @param String $address
	 * @return bool
	 */
	public function valid_email($address, $frompost = true){
		if($frompost === true){
			if(is_array($address)){
				foreach($address as $add){
					$this->valid_email($add);
				}
			}else{
				$this->valid = (preg_match(
						'/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
						'(([a-z0-9-])*([a-z0-9]))+' .
						'(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i',
						$this->getPost($address)));
				if(!$this->valid){
					$this->errFields[] = $address;
				}
			}
		}else{
			if(is_array($address)){
				foreach($address as $add){
					$this->valid_email($add);
				}
			}else{
				$this->valid = preg_match(
					'/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' .
					'(([a-z0-9-])*([a-z0-9]))+' .
					'(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i',
					$address);
			}
		}
	}


	/**
	 * Validate number
	 *
	 * @param String/Array $address
	 * @return bool
	 */
	public function valid_number($numbers){
		if(is_array($numbers)){
			foreach($numbers as $number){
				$this->valid_number($number);
			}
		}else{
			if(!is_numeric($this->getPost($numbers))){
				$this->valid = FALSE;
				$this->errFields[] = $numbers;
			}
		}
	}


	/**
	 * Validate First Name or Last Name
	 * check for a digit , and minim 3 letters
	 */
	public function valid_name($name)
	{
		if(is_array($name)){
			foreach($name as $n){
				$this->valid_name($n);
			}
		}
		else{
			if(1 === preg_match('~[0-9]~', $this->getPost($name)) || strlen($this->getPost($name)) < 3){
				$this->valid = FALSE;
				$this->errFields[] = $name;
			}
		}
	}

	public function valid_password($pwd) {

  	 	if (strlen($pwd) < 6) {
  	      	$this->valid = FALSE;
			$this->errFields[] = $pwd;
  		}

	}


	/**
	 *
	 * @param instance of a helper that have the validateFields method $helperObj
	 */
	public function validateRequiredFields($helperObj){
		$return = $helperObj->validateFields(true);
		if(count($return) > 0){
			$this->valid = false;
			$this->errFields = array_unique(array_merge($this->errFields, $return));
		}
	}

	/**
	 * Compare 2 fields, and return false if are differnces
	 * @return
	 * @param string $str1
	 * @param string $str2
	 */
	public function duplicateFields($str1, $str2){
		if(!($this->getPost($str1) === $this->getPost($str2))){
			$this->errFields[] = $str1;
			$this->errFields[] = $str2;
			$this->valid = false;
		}
	}

// 	public function


	/**
	 * CNP validation
	 * PIN: Personal Identification Number
	 * @param array/string $fields
	 */
	public function valid_cnp($fields){
		if(is_array($fields)){
			foreach($fields as $field){
				$this->valid_cnp($field);
			}
		}else{
			$cnp = $this->getPost($fields);
			if (!is_numeric($cnp) || strlen($cnp)!=13){
				$this->valid = FALSE;
				$this->errFields[] = $fields;
				return;
			}

			$year = $cnp[1].$cnp[2];
			$mounth = $cnp[3].$cnp[4];
			$day = $cnp[5].$cnp[6];
			if (!checkdate($mounth, $day, $year)){
				$this->valid = FALSE;
				$this->errFields[] = $fields;
				return;
			}

			$sum=$cnp[0]*2 + $cnp[1]*7 + $cnp[2]*9 + $cnp[3]*1 + $cnp[4]*4 + $cnp[5]*6 + $cnp[6]*3 + $cnp[7]*5 + $cnp[8]*8 + $cnp[9]*2 + $cnp[10]*7 + $cnp[11]*9;
			$rest=fmod($sum, 11);
			if ( $rest<10 && $rest==$cnp[12] ){

			}elseif ( $cnp[12]==1 ){

			}else{
				$this->valid = FALSE;
				$this->errFields[] = $fields;
				return;
			}
		}
	}

	/**
	 * Check captcha code
	 * @return
	 * @param object $key
	 */
	public function checkCaptcha($field){
		if(!isset($_SESSION['captcha_id']) || ($_SESSION['captcha_id']!=$this->getPost($field))){
			$this->errFields[] = $field;
			$this->valid = FALSE;
		}
	}

	/**
	 * Check is is a corect day
	 * @return
	 * @param string $field
	 */
	public function valid_day($field){
		$date = $this->getPost($field);
		$result = ereg("^[0-9]+$",$date,$trashed);
		if(!($result)){
			$this->errFields[] = $field;
			$this->valid = FALSE;
		}else{
			if(($date<=0)OR($date>31)){
				$this->errFields[] = $field;
				$this->valid = FALSE;
			}
		}
	}

	/**
	 * Check is is a corect month
	 * @return
	 * @param string $field
	 */
	public function valid_month($field){
		$date = $this->getPost($field);
		$result = ereg("^[0-9]+$",$date,$trashed);
		if(!($result)){
			$this->errFields[] = $field;
			$this->valid = FALSE;
		}else{
			if(($date<=0)OR($date>12)){
				$this->errFields[] = $field;
				$this->valid = FALSE;
			}
		}
	}


	/**
	 * Check if is a valid year
	 * @return
	 * @param string $field
	 * @param int $diff[optional]
	 */
	public function valid_year($field, $diff = 0){
		$date = $this->getPost($field);
		$result = ereg("^[0-9]+$",$date,$trashed);
		if(!($result)){
			$this->errFields[] = $field;
			$this->valid = FALSE;
		}else{
			$thisYear = intval(date("Y"));
			if(($date<=$thisYear-100)OR($date>$thisYear-$diff)){
				$this->errFields[] = $field;
				$this->valid = FALSE;
			}
		}
	}


	/**
	 * Compare 2 dates
	 * @return
	 * @param object $strdate
	 * @param object $nextday[optional]
	 */
	function dateComparation($strdate, $nextday = NULL){
		//echo $strdate;

		$show_echo = true;
		if($nextday==NULL){
			$nextday = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
			$nextdate = date("d/m/Y", $nextday);
		}else{
			$nextdate =$nextday;
		}
	//	echo "<br />---- compare: ".$strdate." with: ".$nextdate." ----------<br />";
	//	Check the length of the entered date value
		if((strlen($strdate)<8) || (strlen($strdate)>10)){
			if($show_echo){
				trigger_error("Lenght it's not good !");
			}
			$this->valid = FALSE;
		}else{
	//	The entered value is checked for proper date format
			if((substr_count($strdate,"/"))<>2){
				if($show_echo){
					trigger_error("Date is not valid... must have two / !");
				}
				$this->valid = FALSE;
			}else{
				$pos=strpos($strdate,"/");
				$date=substr($strdate,0,($pos));
				$date_a=substr($nextdate,0,($pos));
				$month=substr($strdate,($pos+1),($pos));
				$month_a=substr($nextdate,($pos+1),($pos));
				if(($date<=0)OR($date>31)){
					if($show_echo){
						trigger_error("Day must be from 0 to 31 !");
					}
					$this->valid = FALSE;
				}
				if(($date<$date_a) && $month<=$month_a){
					if($show_echo){
						trigger_error("Day must be greater than ".$date_a);
					}
					$this->valid = FALSE;
				}
			}
			$month=intval(substr($strdate,($pos+1),($pos)));
			$month_a= substr($nextdate,($pos+1),($pos));
			if(($month<=0)OR($month>12)){
				if($show_echo){
					trigger_error("Month must be from 0 to 12 $month");
				}
				$this->valid = FALSE;
			}
			if(($month<=0)OR($month>12)OR($month<$month_a)){
				if($show_echo){
					trigger_error("Month must be greater or equals with ".$month_a);
				}
				$this->valid = FALSE;
			}
			$year=substr($strdate,($pos+4),strlen($strdate));
			if(($year<date("Y"))OR($year>2200)){
				$this->valid = FALSE;
			}
		}
	}


	/**
	 * Getter for test validation of form
	 *
	 * @return boolean
	 */
	public function isValid(){
		return $this->valid;
	}


	/**
	 * Retrive error fields
	 * @return
	 */
	public function getErrFields(){
		$f = array_unique($this->errFields);
		return array_unique(array_values($f));
	}

	//	must be EOF from here

	function valid_select($form_vars, $val){
		foreach ($form_vars as $key => $value){
			if ($value == 0){
				$this->errFields[] = $key;
				$this->valid = FALSE;
			}
		}
	}

}