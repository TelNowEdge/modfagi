<?php

/*
 * Copyright [2016] [TelNowEdge]
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace TelNowEdge\Module\modfagi\Resources\Migrations;

use TelNowEdge\FreePBX\Base\Model\Annotation\Migration;
use TelNowEdge\FreePBX\Base\Resources\Migrations\AbstractSqlMigration;

class FagiMigration extends AbstractSqlMigration
{
    /**
     * @Migration()
     */
    public function migration2018032801()
    {
        return '
ALTER TABLE
    `fagi` ADD COLUMN `fallback` VARCHAR (255) NOT NULL
';
    }

    /**
     * @Migration()
     */
    public function migration2018032702()
    {
        return '
ALTER TABLE
    `fagi` DROP
        COLUMN `truegoto`
        ,DROP
            COLUMN `falsegoto`
';
    }

    /**
     * @Migration()
     */
    public function migration2018032701()
    {
        return '
CREATE
    TABLE
        `fagi_result` (
            `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT
            ,`match` VARCHAR (255) NOT NULL
            ,`goto` VARCHAR (255) NOT NULL
            ,`fagi_id` INT NOT NULL
            ,FOREIGN KEY (`fagi_id`) REFERENCES `fagi` (`id`)
        )
';
    }

    /**
     * @Migration()
     */
    public function migration2017120702()
    {
        return '
ALTER TABLE
    `fagi` CHANGE COLUMN `truegoto` `truegoto` VARCHAR (255) NOT NULL
    ,CHANGE COLUMN `falsegoto` `falsegoto` VARCHAR (255) NOT NULL
';
    }

    /**
     * @Migration()
     */
    public function migration2017120701()
    {
        return '
CREATE
    TABLE
        IF NOT EXISTS `fagi` (
            `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT
            ,`displayname` VARCHAR (32) NOT NULL
            ,`description` VARCHAR (50) NOT NULL
            ,`host` VARCHAR (30) DEFAULT NULL
            ,`port` VARCHAR (30) DEFAULT NULL
            ,`path` VARCHAR (100) DEFAULT NULL
            ,`query` VARCHAR (100) DEFAULT NULL
            ,`truegoto` VARCHAR (50) DEFAULT NULL
            ,`falsegoto` VARCHAR (50) DEFAULT NULL
        )
';
    }
}
