SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `city`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `city` ;

CREATE TABLE IF NOT EXISTS `city` (
  `CityID` INT(11) NOT NULL AUTO_INCREMENT,
  `CountyID` INT(11) NULL DEFAULT NULL,
  `Long` VARCHAR(20) NULL DEFAULT NULL,
  `Lat` VARCHAR(20) NULL DEFAULT NULL,
  `Name` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`CityID`))
ENGINE = InnoDB
AUTO_INCREMENT = 13750
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `county`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `county` ;

CREATE TABLE IF NOT EXISTS `county` (
  `CountyID` INT(11) NOT NULL AUTO_INCREMENT,
  `Code` VARCHAR(2) NULL DEFAULT NULL,
  `Name` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`CountyID`))
ENGINE = InnoDB
AUTO_INCREMENT = 43
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `list_value`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `list_value` ;

CREATE TABLE IF NOT EXISTS `list_value` (
  `ListValueID` INT(11) NOT NULL AUTO_INCREMENT,
  `ListKey` VARCHAR(45) NULL DEFAULT NULL,
  `ListValue` VARCHAR(145) NULL DEFAULT NULL,
  `ListGroup` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`ListValueID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `super`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `super` ;

CREATE TABLE IF NOT EXISTS `super` (
  `UserID` INT(11) NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(128) NOT NULL,
  `Password` VARCHAR(40) NOT NULL,
  PRIMARY KEY (`UserID`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;

CREATE TABLE IF NOT EXISTS `user` (
  `UserID` INT NOT NULL AUTO_INCREMENT,
  `Email` VARCHAR(145) NULL,
  `Password` VARCHAR(145) NULL,
  `FirstName` VARCHAR(145) NULL,
  `LastName` VARCHAR(145) NULL,
  `DateCreated` DATETIME NULL,
  `Dob` DATETIME NULL,
  `PhoneNo` VARCHAR(15) NULL,
  `Gender` VARCHAR(1) NULL,
  `City` VARCHAR(500) NULL,
  `County` VARCHAR(500) NULL,
  `Country` VARCHAR(500) NULL,
  PRIMARY KEY (`UserID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `login`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `login` ;

CREATE TABLE IF NOT EXISTS `login` (
  `LoginID` INT NOT NULL AUTO_INCREMENT,
  `UserID` INT NULL,
  `Lang` VARCHAR(5) NULL,
  `SessionID` VARCHAR(145) NULL,
  `StartTime` DATETIME NULL,
  `EndTime` DATETIME NULL,
  `DeviceID` VARCHAR(145) NULL,
  PRIMARY KEY (`LoginID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `country`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `country` ;

CREATE TABLE IF NOT EXISTS `country` (
  `CountryID` INT NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(145) NULL,
  PRIMARY KEY (`CountryID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `user_input`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_input` ;

CREATE TABLE IF NOT EXISTS `user_input` (
  `UserInputID` INT NOT NULL AUTO_INCREMENT,
  `Key` VARCHAR(145) NULL,
  `Value` TEXT NULL,
  PRIMARY KEY (`UserInputID`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `list_value`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `list_value` (`ListValueID`, `ListKey`, `ListValue`, `ListGroup`) VALUES (1, 'gender', 'M', NULL);
INSERT INTO `list_value` (`ListValueID`, `ListKey`, `ListValue`, `ListGroup`) VALUES (2, 'gender', 'F', NULL);

COMMIT;

