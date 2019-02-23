<?php

/**
 * Business Logic object
 * @author achebv
 */
class BCategory extends BBase {


    public $bname = 'category';


    /**
     * add categories to family tree :)
     * @param $FamilyID
     * @return array
     */
    public function importCat($FamilyID){
        DboCategory::strictFields(true);
        DboCategory::finder()->findAllBySql("delete from category where FamilyID = $FamilyID");
        $processed = 0;
        $data = DboCategory::finder()->findAllBySql("select * from category where FamilyID is null AND ParentID in (1, 2)");
        $assoc = array();
        foreach($data as $d){
            $categMaster = new DaoCategory();
            $categMaster->Name = $d->Name;
            $categMaster->FamilyID = $FamilyID;
            $categMaster->ParentID = $d->ParentID;
            $categMaster->save();
            $assoc[$d->CategoryID] = $categMaster->CategoryID;
            $processed++;
        }
        $old_parents = array_keys($assoc);
        $data = DboCategory::finder()->findAllBySql("select * from category where ParentID in (".implode(',', $old_parents).")");
        foreach($data as $d){
            $categMaster = new DaoCategory();
            $categMaster->Name = $d->Name;
            $categMaster->FamilyID = $FamilyID;
            $categMaster->ParentID = $assoc[$d->ParentID];
            $categMaster->save();
            $processed++;
        }
        return array('processed' => $processed);
    }

    public function load($CategoryID){
        DaoCategory::strictFields(true);
        $data = DaoCategory::finder()->findByCategoryID($CategoryID);
        return array('data' => $data);
    }


    /**
     * delete a non used category only
     * @param $LoginID
     * @param $CategoryID
     * @return array
     */
    public function delete($LoginID, $CategoryID){
        $result = array('error' => true, 'msg' => 'Categoria a fost stearsa.');
        $data = DaoItem::finder()->findByCategoryID($CategoryID);
        if($data->ItemID > 0){
            $result['msg'] = "Categoria este folosita si nu poate fi stearsa.";
            return $result;
        }
        $FamilyID = $this->getFamilyIDByLoginID($LoginID);
        $data = DaoCategory::finder()->findByCategoryID($CategoryID);
        if($data->FamilyID != $FamilyID){
            $result['msg'] = "Nu aveti permisiunea de a sterge aceasta categorie.";
            return $result;
        }
        $data->delete();
        $result['error'] = false;
        return $result;
    }


    /**
     * Save a category in database
     * @param $LoginID
     * @param $CategoryID
     * @param $ParentID
     * @param $Name
     * @return array
     */
    public function save($LoginID, $CategoryID, $ParentID, $Name){
        $result = array('error' => false, 'msg' => 'Date salvate cu success.');
        $FamilyID = $this->getFamilyIDByLoginID($LoginID);
        $category = new DaoCategory();
        if($CategoryID > 0){
            $category = DaoCategory::finder()->findByCategoryID($CategoryID);
        }
        // check unique name
        $categoryCheck = DaoCategory::finder()->findByNameAndParentIDAndFamilyID($Name, $ParentID, $FamilyID);
        t::p($categoryCheck);
        if($categoryCheck->CategoryID > 0 && $categoryCheck->CategoryID != $CategoryID){
            $result['error'] = true;
            $result['msg'] = 'Exista deja o categorie cu acest nume. ' . $categoryCheck->CategoryID . ":" . $CategoryID;
            return $result;
        }

        $category->ParentID = $ParentID;
        $category->Name = $Name;
        $category->FamilyID = $FamilyID;
        $category->save();
        $result['newId'] = $category->CategoryID;
        return $result;
    }

}