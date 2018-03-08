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

class FagiRepository extends AbstractRepository
{
    const SQL = '
SELECT
        f.id f__id
        ,f.displayname f__display_name
        ,f.description f__description
        ,f.host f__host
        ,f.port f__port
        ,f.path f__path
        ,f.query f__query
        ,f.truegoto destination_true__destination
        ,f.falsegoto destination_false__destination
    FROM
        fagi f
';

    public function getCollection()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

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
        $sql = sprintf('%s WHERE id = :id', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        return $this->mapModel(
            $this->sqlToArray($this->fetch($stmt))
        );
    }

    public function getByBothGoto($goto)
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

        $sql = sprintf('%s WHERE truegoto = :goto OR falsegoto = :goto', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('goto', $goto);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        foreach ($res as $child) {
            $x = $this->sqlToArray($child);
            $collection->add($this->mapModel($x));
        }

        return $collection;
    }

    public function getByDisplayName($displayName)
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

        $sql = sprintf('%s WHERE displayname = :displayName', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('displayName', $displayName);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        foreach ($res as $child) {
            $x = $this->sqlToArray($child);
            $collection->add($this->mapModel($x));
        }

        return $collection;
    }

    public function getByDisplayNameLike($displayName)
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

        $sql = sprintf('%s WHERE displayname LIKE :displayName', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('displayName', sprintf('%s%%', $displayName));
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
        $trueGoto = $this->objectFromArray(Destination::class, $res['destination_true']);
        $falseGoto = $this->objectFromArray(Destination::class, $res['destination_false']);
        $fagi = $this->objectFromArray(Fagi::class, $res['f']);

        $fagi
            ->setTrueGoto($trueGoto)
            ->setFalseGoto($falseGoto)
            ;

        return $fagi;
    }
}
