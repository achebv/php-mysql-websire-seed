drop table if exists list_value;
CREATE TABLE IF NOT EXISTS `list_value` (
  `ListValueID` INT(11) NOT NULL AUTO_INCREMENT,
  `ListKey` VARCHAR(45) NULL DEFAULT NULL,
  `ListValue` VARCHAR(145) NULL DEFAULT NULL,
  `ListGroup` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`ListValueID`))
ENGINE = InnoDB;
INSERT INTO `list_value` (`ListValueID`, `ListKey`, `ListValue`, `ListGroup`) VALUES (1, 'gender', 'M', NULL);
INSERT INTO `list_value` (`ListValueID`, `ListKey`, `ListValue`, `ListGroup`) VALUES (2, 'gender', 'F', NULL);