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

namespace TelNowEdge\Module\modfagi\Handler\DbHandler;

use TelNowEdge\FreePBX\Base\Handler\AbstractDbHandler;
use TelNowEdge\Module\modfagi\Event\FagiResultEvent;
use TelNowEdge\Module\modfagi\Model\FagiResult;

class FagiResultDbHandler extends AbstractDbHandler
{
    public function create(FagiResult $fagiResult)
    {
        $sql = '
INSERT
    INTO
        fagi_result (
            `match`
            ,`goto`
            ,`fagi_id`
        )
    VALUES (
        :match
        ,:goto
        ,:fagi
    )
';

        $this->eventDispatcher->dispatch(
            FagiResultEvent::CREATE_PRE_BIND,
            new FagiResultEvent($fagiResult)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('match', $fagiResult->getMatch());
        $stmt->bindParam('goto', $fagiResult->getGoto());
        $stmt->bindParam('fagi', $fagiResult->getFagi()->getId());

        $this->eventDispatcher->dispatch(
            FagiResultEvent::CREATE_PRE_SAVE,
            new FagiResultEvent($fagiResult)
        );

        $stmt->execute();

        $fagiResult->setId($this->connection->lastInsertId());

        $this->eventDispatcher->dispatch(
            FagiResultEvent::CREATE_POST_SAVE,
            new FagiResultEvent($fagiResult)
        );

        return true;
    }

    public function update(FagiResult $fagiResult)
    {
        $sql = '
UPDATE
        fagi_result
    SET
        `match` = :match
        ,`goto` = :goto
    WHERE
        `id` = :id
';

        $this->eventDispatcher->dispatch(
            FagiResultEvent::UPDATE_PRE_BIND,
            new FagiResultEvent($fagiResult)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $fagiResult->getId());
        $stmt->bindParam('match', $fagiResult->getMatch());
        $stmt->bindParam('goto', $fagiResult->getGoto());

        $this->eventDispatcher->dispatch(
            FagiResultEvent::UPDATE_PRE_SAVE,
            new FagiResultEvent($fagiResult)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            FagiResultEvent::UPDATE_POST_SAVE,
            new FagiResultEvent($fagiResult)
        );

        return true;
    }

    public function delete(FagiResult $fagiResult)
    {
        $sql = '
DELETE
    FROM
        fagi_result
    WHERE
        id = :id
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $fagiResult->getId());

        $this->eventDispatcher->dispatch(
            FagiResultEvent::DELETE_PRE_SAVE,
            new FagiResultEvent($fagiResult)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            FagiResultEvent::DELETE_POST_SAVE,
            new FagiResultEvent($fagiResult)
        );

        return true;
    }
}
