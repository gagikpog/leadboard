-- CREATE TABLE
CREATE TABLE `k992302t_board`.`games` (
    `id` VARCHAR(20) NOT NULL ,
    `name` VARCHAR(100) NOT NULL ,
        PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `k992302t_board`.`board` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `identifier` VARCHAR(50) NOT NULL ,
    `score` INT NOT NULL ,
    `game` VARCHAR(20) NOT NULL ,
    `mode` VARCHAR(50) NOT NULL DEFAULT 'default' ,
    `meta` TEXT NULL ,
    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
        PRIMARY KEY (`id`),
        CONSTRAINT `fk_board_game` FOREIGN KEY (`game`)
            REFERENCES `k992302t_board`.`games` (`id`)
) ENGINE = InnoDB;
