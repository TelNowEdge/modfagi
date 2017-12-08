<?php

namespace TelNowEdge\Module\modfagi\Resources\Migrations;

use TelNowEdge\FreePBX\Base\Resources\Migrations\AbstractMigration;

class FagiMigration extends AbstractMigration
{
    public function migration2017120702()
    {
        return '
ALTER TABLE
    `fagi` CHANGE COLUMN `truegoto` `truegoto` VARCHAR (255) NOT NULL
    ,CHANGE COLUMN `falsegoto` `falsegoto` VARCHAR (255) NOT NULL
';
    }

    public function migration2017120701()
    {
        return '
CREATE
    TABLE
        IF NOT EXISTS fagi (
            id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
            ,displayname VARCHAR (32) NOT NULL
            ,description VARCHAR (50) NOT NULL
            ,host VARCHAR (30) DEFAULT NULL
            ,port VARCHAR (30) DEFAULT NULL
            ,path VARCHAR (100) DEFAULT NULL
            ,query VARCHAR (100) DEFAULT NULL
            ,truegoto VARCHAR (50) DEFAULT NULL
            ,falsegoto VARCHAR (50) DEFAULT NULL
        )
';
    }
}
