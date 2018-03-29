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
        ,f.fallback fallback__destination
        ,fr.id fr__id
        ,fr.match fr__match
        ,fr.goto destination__destination
    FROM
        fagi f LEFT JOIN fagi_result fr
            ON (
            fr.fagi_id = f.id
        )
';

    public function getCollection()
    {
        $stmt = $this->connection->prepare(self::SQL);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this
            ->collection($res)
            ;
    }

    public function getById($id)
    {
        $sql = sprintf('%s WHERE f.id = :id', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $id);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this
            ->collection($res)
            ->first()
            ;
    }

    public function getByFallback($gotos)
    {
        $params = array();
        foreach ($gotos as $k => $goto) {
            array_push($params, sprintf(':p_%d', $k));
        }

        $sql = sprintf('%s WHERE f.fallback IN (%s)', self::SQL, implode(',', $params));

        $stmt = $this->connection->prepare($sql);

        foreach ($gotos as $k => $goto) {
            $stmt->bindValue(sprintf('p_%d', $k), $goto);
        }

        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this
            ->collection($res)
            ;
    }

    public function getByGotos($gotos)
    {
        $params = array();
        foreach ($gotos as $k => $goto) {
            array_push($params, sprintf(':p_%d', $k));
        }

        $sql = sprintf('%s WHERE fr.goto IN (%s)', self::SQL, implode(',', $params));

        $stmt = $this->connection->prepare($sql);

        foreach ($gotos as $k => $goto) {
            $stmt->bindValue(sprintf('p_%d', $k), $goto);
        }

        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this
            ->collection($res)
            ;
    }

    public function getByDisplayName($displayName)
    {
        $sql = sprintf('%s WHERE f.displayname = :displayName', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('displayName', $displayName);
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this
            ->collection($res)
            ->getValues()
            ;
    }

    public function getByDisplayNameLike($displayName)
    {
        $sql = sprintf('%s WHERE f.displayname LIKE :displayName', self::SQL);

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('displayName', sprintf('%s%%', $displayName));
        $stmt->execute();

        $res = $this->fetchAll($stmt);

        return $this
            ->collection($res)
            ;
    }

    private function collection(array $res)
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($res as $child) {
            $object = $this->mapModel($this->sqlToArray($child));
            if ($fagiResult = $collection->get($object->getId())) {
                $fagiResult->addFagiResult($object->getFagiResults()->first());
                continue;
            }

            if (null === $object->getFagiResults()->first()->getId()) {
                $object->getFagiResults()->clear();
            }

            $collection->set($object->getId(), $object);
        }

        // Prevent that serialize was not an array but an object due to index
        return new \Doctrine\Common\Collections\ArrayCollection(
            $collection->getValues()
        );
    }

    private function mapModel(array $res)
    {
        $fagi = $this->objectFromArray(Fagi::class, $res['f']);
        $fagiResult = $this->objectFromArray(FagiResult::class, $res['fr']);
        $goto = $this->objectFromArray(Destination::class, $res['destination']);
        $fallback = $this->objectFromArray(Destination::class, $res['fallback']);

        $fagiResult->setGoto($goto);

        return $fagi
            ->setFallback($fallback)
            ->setFagiResults(array($fagiResult))
            ;
    }
}
