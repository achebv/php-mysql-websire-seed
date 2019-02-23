<?php


class AReport extends ABase{


	public function getAll($LoginID, $ParentID, $FromDate){
		$bo = $this->storeObject('BOutcome', 'bOutcome');
		return $bo->getAllForReports($LoginID, $ParentID, $FromDate);
	}

}