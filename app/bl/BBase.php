<?php

class BBase extends BMain{

    /**
     * Controller name
     */
    private $bname = 'base';

    private $_df = 'Y-m-d H:i:s';

    public function init(){ }

    /**
     * @param $LoginID
     * @return mixed
     */
    function getFamilyIDByLoginID($LoginID){
        $UserID = $this->getUserIDByLoginID($LoginID);
        $userDb = DaoUser::finder()->findByUserID($UserID);
        return $userDb->FamilyID;
    }

    /**
     * get user id by a login id
     * @param $LoginID
     * @return mixed
     */
    function getUserIDByLoginID($LoginID){
        $loginData = $this->isLoginValid($LoginID);
        $loginData = $loginData['data'];
        return $loginData->UserID;
    }

    /**
     * Check if login id is still valid
     * @param $LoginID
     * @return array
     */
    public function isLoginValid($LoginID){
        $result = array('error' => true);
        $daoLogin = DaoLogin::finder()->findByLoginID($LoginID);
        $result['error'] = !($daoLogin instanceof DboLogin);
        if(!$result['error']){
            $result['error'] = (strtotime($daoLogin->EndTime) < strtotime(date($this->_df)));
        }
        if(!$result['error']){
            unset($daoLogin->rownum);
            $result['data'] = $daoLogin;
        }
        return $result;
    }

    public function getUsersInFamily($FamilyID){
        $users = array();
        DaoUser::strictFields(true);
        $usersData = DaoUser::finder()->findAllByFamilyID($FamilyID);
        foreach($usersData as $user){
            $users[] = $user->UserID;
        }
        return $users;
    }

    public function getAllCategoriesByLevel($FamilyID, $level){
        DboCategory::strictFields(true);
        $sql = "SELECT c.CategoryID from category c";
        if($level == INCOME_ID){
            $sql .= " WHERE c.ParentID = " . INCOME_ID . " AND c.FamilyID = {$FamilyID}";
        }
        if($level == OUTCOME_ID){
            $sql = "SELECT t2.CategoryID from category c";
            $sql .= " LEFT JOIN category t2 ON t2.ParentID = c.CategoryID ";
            $sql .= " WHERE c.ParentID = " . OUTCOME_ID . " AND c.FamilyID = {$FamilyID}";
        }
        $data = DboCategory::finder(array('CategoryID'))->findAllbySql($sql);
        $df = array();
        foreach($data as $d){
            $df[] = $d->CategoryID;
        }
        return $df;
    }

}