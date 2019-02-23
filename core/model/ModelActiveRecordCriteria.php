<?php 


class ModelActiveRecordCriteria{
	
	
	public $Condition;
	
	
	public $Parameters = array();
	
	
	public $OrdersBy = array();
	
	
	public $Limit = 0;
	
	
	public $Offset = 0;
	
	
	public function __toString(){
		$c = $this->Condition;
		foreach($this->Parameters as $k=>$v){
			$c = str_replace($k, "'$v'", $c);
		}
		return $c;
	}
	
	
	public function isComplex(){
		return !(empty($this->OrdersBy))||!empty($this->Limit);
	}
	
	
	public function bindComplexSql($sql){
		$o = '';
		foreach($this->OrdersBy as $k=>$v){
			$o .= " $k $v, "; 
		}
		$o = substr($o, 0, strlen(trim($o)));
		if(!empty($o)){
			$sql = str_replace('[Order]', ' ORDER BY ' . $o, $sql);
		}else{
			$sql = str_replace('[Order]', '', $sql);
		}
		if(!empty($this->Condition)){
			$sql = str_replace('[WHERE]', ' WHERE '.$this->__toString(), $sql);
		}else{
			$sql = str_replace('[WHERE]', '', $sql);
		}
		if($this->Limit>0){
			if($this->Offset>0){
				$sql = str_replace('[Limit]', ' LIMIT ' . $this->Offset . ', ' . $this->Limit, $sql);
			}else{
				$sql = str_replace('[Limit]', ' LIMIT ' . $this->Limit, $sql);
			}
			$sql = str_replace('[Offset]', '', $sql);
		}else{
			$sql = str_replace('[Limit]', '', $sql);
			$sql = str_replace('[Offset]', '', $sql);
		}
		return $sql;
	}
	
	
}

