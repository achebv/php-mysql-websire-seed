create view v_outcome as (SELECT c1.Name as ParentName, c2.Name as Name, c2.CategoryID, c2.ParentID, c1.FamilyID from category c1
left join category c2 on c2.ParentID = c1.CategoryID
WHERE c1.ParentID = 1 order by ParentName, Name)