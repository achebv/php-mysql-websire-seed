<?php


class AItem extends ABase{


	/**
	 * @param $LoginID
	 * @param $CategoryID
	 * @param $Price
	 * @param $Desc
	 * @param $ItemID
	 * @return mixed
	 */
	public function save($LoginID, $CategoryID, $ItemID, $Price, $Desc, $picker, $Data){
		$bo = $this->storeObject('BItem', 'bItem');
		return $bo->save($LoginID, $CategoryID, $Price, $Desc, $ItemID, $Data);
	}

	public function getAll($LoginID, $ParentID){
		$bo = $this->storeObject('BItem', 'bItem');
		return $bo->getAll($LoginID, $ParentID);
	}

	public function load($ItemID){
		$bo = $this->storeObject('BItem', 'bItem');
		return $bo->load($ItemID);
	}

	public function delete($LoginID, $ItemID){
		$bo = $this->storeObject('BItem', 'bItem');
		return $bo->delete($LoginID, $ItemID);
	}
}