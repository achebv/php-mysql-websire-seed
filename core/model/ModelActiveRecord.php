<?php

class ModelActiveRecord extends ModelDbDriver implements ModelRecordTemplate{


	public $rownum;


	private $emptyRecord = false;


	public function __contruct(){
	//	print_r ($this);
		unset($this->F3);
		parent::__contruct();
	}


	public static $fields = '*';


	private $strictFields = false;


	public static $strictFieldsStatic = false;


	private function findDinamic(){
		$this->__contruct();	//	because of the static
	//	set base sql phrase
		$this->initSql('find');
	//	parse and create where args
		$this->setupWhere($this->setWhereArgs(func_get_args()));
		return $this->dataToObject(parent::doSql());
	}


	public function isStrictFields(){
		return $this->strictFields;
	}


	private function dataToObject($data){
		$c = get_class($this);
		if(isset($data[0])){
			$r = array();
			foreach($data as $d){
				if($this->isStrictFields()){
					$t = new StdClass();
				}else{
					$t = new $c();
				}
				foreach($d as $k=>$v){
					$t->$k = $v;
				}
				$r[] = $t;
			}
			return $r;
		}else{
			$t = new StdClass();
			if(!empty($data)){
				if($this->isStrictFields()){
					$t = new StdClass();
				}else{
					$t = new $c();
				}
				foreach($data as $k=>$v){
					$t->$k = $v;
				}
			}else{
				return NULL;
			}
			return $t;
		}
	}


	public function delete(){
		$this->__contruct();	//	because of the static
		$this->initSql('delete');
		$oArr = array();
		foreach($this as $k=>$v){
			$oArr[$k] = $v;
		}
		$this->setPKeyName($oArr['pk']);
		$this->setPKeyValue((strlen(trim($oArr[$this->getPKeyName()]))>0)?$oArr[$this->getPKeyName()]: 0);
		$this->setupWhere(" {$this->getPKeyName()} = {$this->getPKeyValue()} ");
		$this->doSql();
	}


	private function setWhereArgs($args){
		$x = $args[0];
		if(DB_DRIVER=='PostgreSQL'){
			$x = array();
			foreach($args[0] as $a){
				$x[] = 'public."'.$this->getTName().'"."'.$a.'"';
			}
		}
		return vsprintf(implode("='%s' AND ", $x) . " = '%s'", $args[1]);
	}


	private function bindFields($oArr){
		if($this->getPKeyValue()>0){
			foreach($oArr as $k=>$v){
				$this->appendSql(" $k = '$v', ");
			}
			$this->setSql(substr($this->getSql(), 0, strlen($this->getSql())-2));
			$this->setupWhere(" {$this->getPKeyName()} = {$this->getPKeyValue()} ");
		}else{	//	insert
			$fields = array_keys($oArr);
			$vals = array_values($oArr);
			$s = '';
			$i=0;
			foreach($vals as $v1){
				if(strlen(trim($v1))>0){
					$s .= "'" . $v1 . "', ";
				}else{
					unset($fields[$i]);
				}
				$i++;
			}
			$this->appendSql(implode(', ', $fields) . ') VALUES (' . $s);
			$this->setSql(substr($this->getSql(), 0, strlen($this->getSql())-2) . ')');
		}
	}


	public function save(){
		$this->__contruct();	//	because of the static
		$oArr = array();
		foreach($this as $k=>$v){
			$oArr[$k] = $v;
		}
		$this->setPKeyName($oArr['pk']);
		unset($oArr['emptyRecord']);
		unset($oArr['link']);
		unset($oArr['pk']);
		unset($oArr['rownum']);
		unset($oArr['strictFields']);
		$this->setPKeyValue((strlen(trim($oArr[$this->getPKeyName()]))>0)?$oArr[$this->getPKeyName()]: 0);
		unset($oArr[$this->getPKeyName()]);
		if($this->getPKeyValue()>0){
			$this->initSql('update');
		}else{
			$this->initSql('insert');
		}
		$this->bindFields($oArr);
		parent::doSql();
		if($this->getPKeyValue()<1){
			$this->{$this->pk} = $this->getPkVal();
		}
	}


	public static function createComponent($fields, $type){
		if(($pos=strrpos($type,'.'))!==false){
			$type=substr($type,$pos+1);
		}
		if(($n=func_num_args())>1){
			$args=func_get_args();
			$s='$args[1]';
			$component = '';
			for($i=2;$i<$n;++$i){
				$s.=",\$args[$i]";
			}
			eval("\$component=new $type($s);");
			$component->setStrictFields(self::$strictFieldsStatic);
			self::$fields = $fields;
			return $component;
		}else{
			return new $type;
		}
	}


	public static function finder($fields='*', $className=__CLASS__){
		static $finders = array();
		if(!isset($finders[$className])){
			$f = self::createComponent($fields, $className);
			$finders[$className] = $f;
		}
		$d = $finders[$className];
		$d->setFields($fields);
		return $finders[$className];
	}


	public static function strictFields( $strict ){
		self::$strictFieldsStatic = $strict;
	}



	public function setStrictFields($strict){
		$this->strictFields = $strict;
	}


	/**
	 * process all finders.
	 * 		combinantions: find, findAll, findByFieldAndField, findAllByFieldAndField, findBySql, findAllBySql
	 * @param string $method
	 * @return
	 */
	private function doFind($method, $args){
		$this->__contruct();				//	because of the static
		$this->initSql('findAll');

		if(isset($args[0]) && ($args[0] instanceof ModelActiveRecordCriteria)){
			$cr = $args[0];
			$args[0] = $cr->__toString();
			if($cr->isComplex()){
				$this->initSql('findComplex');
				$this->setSingleData(strlen(trim($method))==4);
				$sql = $cr->bindComplexSql(parent::getSql());
				$this->setSql($sql);
				return $this->dataToObject(parent::doSql());
			}
		}

		if(strlen(trim($method))==4){			//	find
			$this->setSingleData(true);
		//	parse and create where args
			$this->setupWhere($this->bindArgsInWhere($args));
			return $this->dataToObject(parent::doSql());
		}else if(strlen(trim($method))==7){		//	findAll
			$this->setSingleData(false);
			if(!empty($args)){
				$this->setupWhere($this->bindArgsInWhere($args));
			}
			return $this->dataToObject(parent::doSql());
		}else if(strlen(trim($method))>7){
			if(substr($method, 0, 6)=='findBy'){
				$this->setSingleData(true);
				if(strtolower(substr($method, 0, 9))=='findbysql'){
					$this->setSql($args[0]);
					return $this->dataToObject(parent::doSql());
				}else{
					$this->__contruct();	//	because of the static
					$ar = substr($method, 6);
        			$ar = explode('And', $ar);
        			return $this->findDinamic($ar, $args);
				}
			}else{
				$m = substr($method, 0, 7);			//	findAllBy...
				if($m=='findAll'){
					$this->setSingleData(false);
					if(strtolower(substr($method, 0, 12))=='findallbysql'){
						$this->setSql($args[0]);
						return $this->dataToObject(parent::doSql());
					}else{
						$this->__contruct();	//	because of the static
						$ar = substr($method, 9);
        				$ar = explode('And', $ar);
        				return $this->findDinamic($ar, $args);
					}
				}
			}
			throw new Exception("We are unable to execute the find method '$method'. Please fix it!");
		}
	}


	private function bindArgsInWhere($args){
		if(count($args)>1){
			$w = $args[0];
			$w = str_replace('%', '^', $w);
			$w = str_replace('?', '%s', $w);
			$vals = array();
			for($i=1; $i<count($args); $i++){
				$v = $args[$i];
				if(gettype($args[$i])=='boolean'){
					if($args[$i]===false){
						$v = 0;
					}else{
						$v = 1;
					}
				}
				$vals[] = $v;
			}
			$x = vsprintf($w, $vals);
			$x = str_replace('^', '%', $x);
		}else{
			$x = isset($args[0]) ? $args[0] : '';
		}
		return $x;
	}


	private function doDelete($method){
		echo "ToDo: Implement delete...";
	}


 	public function __call($method, $args){
 		if (!method_exists($this, $method)) {
        	if(substr($method, 0, 4)=='find'){
        		return $this->doFind($method, $args);
        	}else if(substr($method, 0, 6)=='delete'){
        		$this->doDelete($method, $args);
        	}
        	die('no method: ' . $method);
        return;
        	echo "- " . $method ."<br />";
        	$m = substr($method, 0, 7);
        	$isAll = false;
        	if($m=='findAll'){
        		$isAll = true;
	        	$ar = substr($method, 9);
        	}else if(substr($method, 0, 6)=='findBy'){
        		$ar = substr($method, 6);
        		$this->setLimit(1);
        		$ar = explode('And', $ar);
        		$o = $this->findDinamic($ar, $args);
        		$this->clearLimit();
        		return $o;
        	}else{
        		$ar = substr($method, 7);
        	}
        	$ar = explode('And', $ar);
        	$this->findDinamic($ar, $args);
        	return;
        }
        return call_user_func_array(
            array($this, $method),
            $args
        );
    }


    public function __set($a, $b){
    	throw new Exception("Unable to find $a in " . get_class($this) . ". Please fix it!");
    }


    public function __get($a){
    	throw new Exception("Unable to get $a in " . get_class($this) . ". Please fix it!");
    }



	public function setEmptyRecord($r){
		$this->emptyRecord = $r;
	}

	public function isEmptyRecord(){
		return $this->emptyRecord;
	}


}



