<?php

/**
 * Business Logic object
 * @author achebv
 */
class BItem extends BBase {


    public $bname = 'item';


    /**
     * @param $LoginID
     * @param $CategoryID
     * @param $Price
     * @param $Desc
     * @param $ItemID
     * @return array
     */
    public function save($LoginID, $CategoryID, $Price, $Desc, $ItemID, $Data){
        $result = array('error' => false, 'msg' => 'Date salvate cu success.');
        $userId = $this->getUserIDByLoginID($LoginID);
        $item = new DaoItem();
        if($ItemID > 0){
            $item = DaoItem::finder()->findByItemID($ItemID);
        }
        $item->Price = $Price;
        $item->Description = $Desc;
        $item->UserID = $userId;
        $item->CategoryID = $CategoryID;
        $item->DateCreated = date('Y-m-d', strtotime($Data));
        $item->save();
        return $result;
    }

    public function getAll($LoginID, $ParentID){
        $data = array();
        $FamilyID = $this->getFamilyIDByLoginID($LoginID);

        $usersInFamily = $this->getUsersInFamily($FamilyID);
        $categories = $this->getAllCategoriesByLevel($FamilyID, $ParentID);

        $sql = "SELECT FORMAT(i.Price, 0) as Price, i.Description, i.ItemID, i.CategoryID,
                  CONCAT(MONTHNAME(i.DateCreated), ', ', DAYOFMONTH(i.DateCreated), ' ', HOUR(i.DateCreated), ':', MINUTE(i.DateCreated)) as DateCreated,
                  CONCAT(u.Name) as UserName, c.Name as CategoryName
                FROM item i
                    LEFT JOIN category c on c.CategoryID = i.CategoryID
                    LEFT JOIN user u on u.UserID = i.UserID
                  WHERE i.UserID in (".implode(',', $usersInFamily).") AND
                        i.CategoryID in (".implode(',', $categories).")
                ORDER BY i.ItemID DESC";

        DboItem::strictFields(true);
        $data = DboItem::finder()->findAllBySql($sql);
        return array('Items' => $data, 'Total' => $data ? count($data) : 0);
    }


    public function load($ItemID){
        DaoItem::strictFields(true);
        $data = DaoItem::finder()->findByItemID($ItemID);
        return array('data' => $data);
    }

    public function delete($LoginID, $ItemID){
        $result = array('error' => true, 'msg' => 'Inregistrarea a fost stearsa.');
        $userID = $this->getUserIDByLoginID($LoginID);
        $data = DaoItem::finder()->findByItemID($ItemID);
        if($data->UserID != $userID){
            $result['msg'] = "Nu aveti permisiunea de a sterge aceasta inregistrare.";
            return $result;
        }
        $data->delete();
        $result['error'] = false;
        return $result;
    }

}