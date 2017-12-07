<?php

namespace TelNowEdge\Module\modfagi\Resources\Migrations;

use TelNowEdge\FreePBX\Base\Resources\Migrations\AbstractMigration;

class FagiMigration extends AbstractMigration
{
    public function migration2017120701()
    {
        return '
ALTER TABLE
    `fagi` CHANGE COLUMN `truegoto` `truegoto` VARCHAR (255) DEFAULT NOT NULL
    ,CHANGE COLUMN `falsegoto` `falsegoto` VARCHAR (255) DEFAULT NOT NULL
';
    }
}
