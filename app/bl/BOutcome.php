<?php

/**
 * Business Logic object
 * @author achebv
 */
class BOutcome extends BBase
{


    public $bname = 'outcome';

    public function getAll($LoginID, $ParentID)
    {

        $FamilyID = $this->getFamilyIDByLoginID($LoginID);

        DboCategory::strictFields(true);
        $criteria = new ModelActiveRecordCriteria();
        $criteria->OrdersBy = array('Name' => 'ASC');
        $criteria->Condition = " FamilyID = {$FamilyID} AND ParentID = " . ($ParentID < 0 ? OUTCOME_ID : $ParentID);
        $data = DboCategory::finder()->findAll($criteria);
        return array('Items' => $data, 'Total' => count($data));
    }

    public function getAllForReports($LoginID, $ParentID, $FromDate = '')
    {
        $FamilyID = $this->getFamilyIDByLoginID($LoginID);
        $whereDate = '';
        if($FromDate!=''){
            $whereDate = " and i.DateCreated > '".$FromDate."' ";
        }
        $sql = "select sum(i.Price) as x, FORMAT(sum(i.Price), 0) as Total,
 	                            c.ParentID as CategoryID, c1.Name as Name, c1.ParentID
                              from item i
	                            left join category c on c.CategoryID = i.CategoryID
	                            left join category c1 on c1.CategoryID = c.ParentID
                              where c1.ParentID = {$ParentID} and c1.FamilyID = {$FamilyID} {$whereDate}
                              group by c1.CategoryID, c1.Name
                              order by x DESC";



        if (OUTCOME_ID != $ParentID) {
            DboCategory::strictFields(true);
            $hasCateg = DaoCategory::finder()->findByParentID($ParentID);
            DboCategory::strictFields(false);
            if ($hasCateg && $hasCateg->CategoryID > 0) {
                $sql = "select sum(i.Price) as x, FORMAT(sum(i.Price), 0) as Total,
 	                    i.CategoryID, c.Name, c.ParentID
                    from item i
                        left join category c on c.CategoryID = i.CategoryID
                    where c.ParentID = {$ParentID} and c.FamilyID = {$FamilyID} {$whereDate}
                    group by c.CategoryID, c.Name
                    order by x desc";
            }else{
                $sql = "select FORMAT(i.Price, 0) as Total,
 	                   i.CategoryID, CONCAT(CONCAT(MONTHNAME(i.DateCreated), ', ', DAYOFMONTH(i.DateCreated)), '<br /><span style=\"color: #555;font-size: 14px;\">' , i.Description, '</span>') as Name, i.CategoryID as ParentID
                    from item i
                    where i.CategoryID = {$ParentID} {$whereDate}
                    order by i.Price desc";
            }
        }
        //die($sql);
        DboCategory::strictFields(true);
        $data = DboCategory::finder()->findAllBySql($sql);
        return array('Items' => $data, 'Total' => $data ? count($data) : 0);
    }

}