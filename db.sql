-- CREATE TABLE
CREATE TABLE `k992302t_board`.`board` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `identifier` VARCHAR(50) NOT NULL ,
    `score` INT NOT NULL ,
    `game` VARCHAR(20) NOT NULL ,
    `meta` TEXT NULL ,
    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
        PRIMARY KEY (`id`)
) ENGINE = InnoDB;
