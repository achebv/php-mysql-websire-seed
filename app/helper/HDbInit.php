<?php 

class HDbInit extends HBase{
	
	
	public function runTask(){
		echo "start init db";
		$operator = $this->initMedic();
	}
	

	private function initMedic(){
		$m = new DboMedic();
		$m->Title = 'dr.';
		$m->FirstName = 'Brumaru';
		$m->LastName = '';
		$m->save();
		$m = new DboMedic();
		$m->Title = 'dr.';
		$m->FirstName = 'Andreescu';
		$m->LastName = '';
		$m->save();
		$m = new DboMedic();
		$m->Title = 'dr.';
		$m->FirstName = 'Visarion';
		$m->LastName = '';
		$m->save();
	}
	
}