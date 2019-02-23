<?php


class ACategory extends ABase{


	/**
	 * get outcome list
	 * @param $LoginID
	 * @param $ParentID
	 * @return mixed
	 */
	public function outcome($LoginID, $ParentID){
		$bo = $this->storeObject('BOutcome', 'bOutcome');
		return $bo->getAll($LoginID, $ParentID);
	}


	public function importCat($FamilyID){
		$bo = $this->storeObject('BCategory', 'bCategory');
		return $bo->importCat($FamilyID);
	}

	public function load($CategoryID){
		$bo = $this->storeObject('BCategory', 'bCategory');
		return $bo->load($CategoryID);
	}

	public function delete($LoginID, $CategoryID){
		$bo = $this->storeObject('BCategory', 'bCategory');
		return $bo->delete($LoginID, $CategoryID);
	}

	public function save($LoginID, $CategoryID, $ParentID, $Name){
		$bo = $this->storeObject('BCategory', 'bCategory');
		return $bo->save($LoginID, $CategoryID, $ParentID, $Name);
	}

}