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

namespace TelNowEdge\Module\modfagi\Repository;

use TelNowEdge\FreePBX\Base\Form\Model\Destination;
use TelNowEdge\FreePBX\Base\Repository\AbstractRepository;
use TelNowEdge\Module\modfagi\Model\Fagi;
use TelNowEdge\Module\modfagi\Model\FagiResult;

class FagiResultRepository extends AbstractRepository
{
    const SQL = '
SELECT
        fr.id fr__id
        ,fr.match fr__match
        ,fr.goto destination__destination
        ,f.id f__id
        ,f.displayname f__display_name
        ,f.description f__description
        ,f.host f__host
        ,f.port f__port
        ,f.path f__path
        ,f.query f__query
    FROM
        fagi_result fr INNER JOIN fagi f
            ON (
            f.id = fr.fagi_id
        )
';

    public function getCollection()
    {
        $stmt = $this->connection->prepare(self::SQL);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        foreach ($res as $child) {
            $x = $this->sqlToArray($child);
            $collection->add($this->mapModel($x));
        }

        return $collection;
    }

    public function getById($id)
    {
        $sql = sprintf('%s WHERE fr.id = ?', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        $res = $this->fetch($stmt);

        return $this->mapModel($this->sqlToArray($res));
    }

    public function getByFagi(Fagi $fagi)
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

        $sql = sprintf('%s WHERE f.id = ?', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $fagi->getId());

        $stmt->execute();

        $res = $this->fetchAll($stmt);

        foreach ($res as $child) {
            $x = $this->sqlToArray($child);
            $collection->add($this->mapModel($x));
        }

        return $collection;
    }

    private function mapModel(array $res)
    {
        $fagiResult = $this->objectFromArray(FagiResult::class, $res['fr']);
        $fagi = $this->objectFromArray(Fagi::class, $res['f']);
        $goto = $this->objectFromArray(Destination::class, $res['destination']);

        return $fagiResult
            ->setFagi($fagi)
            ->setGoto($goto)
            ;
    }
}
