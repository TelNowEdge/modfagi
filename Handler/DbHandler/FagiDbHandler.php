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
use TelNowEdge\Module\modfagi\Event\FagiEvent;
use TelNowEdge\Module\modfagi\Model\Fagi;

class FagiDbHandler extends AbstractDbHandler
{
    public function create(Fagi $fagi)
    {
        $sql = '
INSERT
    INTO
        fagi (
            `displayname`
            ,`description`
            ,`host`
            ,`port`
            ,`path`
            ,`query`
            ,`fallback`
        )
    VALUES (
        :displayName
        ,:description
        ,:host
        ,:port
        ,:path
        ,:query
        ,:fallback
    )
';
        $this->eventDispatcher->dispatch(
            FagiEvent::CREATE_PRE_BIND,
            new FagiEvent($fagi)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('displayName', $fagi->getDisplayName());
        $stmt->bindParam('description', $fagi->getDescription());
        $stmt->bindParam('host', $fagi->getHost());
        $stmt->bindParam('port', $fagi->getPort());
        $stmt->bindParam('path', $fagi->getPath());
        $stmt->bindParam('query', $fagi->getQuery());
        $stmt->bindParam('fallback', $fagi->getFallback()->getDestination());

        $this->eventDispatcher->dispatch(
            FagiEvent::CREATE_PRE_SAVE,
            new FagiEvent($fagi)
        );

        $stmt->execute();

        $fagi->setId($this->connection->lastInsertId());

        $this->eventDispatcher->dispatch(
            FagiEvent::CREATE_POST_SAVE,
            new FagiEvent($fagi)
        );

        return true;
    }

    public function update(Fagi $fagi)
    {
        $sql = '
UPDATE
        fagi
    SET
        displayname = :displayName
        ,description = :description
        ,host = :host
        ,port = :port
        ,path = :path
        ,query = :query
        ,fallback = :fallback
    WHERE
        id = :id
';

        $this->eventDispatcher->dispatch(
            FagiEvent::UPDATE_PRE_BIND,
            new FagiEvent($fagi)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $fagi->getId());
        $stmt->bindParam('displayName', $fagi->getDisplayName());
        $stmt->bindParam('description', $fagi->getDescription());
        $stmt->bindParam('host', $fagi->getHost());
        $stmt->bindParam('port', $fagi->getPort());
        $stmt->bindParam('path', $fagi->getPath());
        $stmt->bindParam('query', $fagi->getQuery());
        $stmt->bindParam('fallback', $fagi->getFallback()->getDestination());

        $this->eventDispatcher->dispatch(
            FagiEvent::UPDATE_PRE_SAVE,
            new FagiEvent($fagi)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            FagiEvent::UPDATE_POST_SAVE,
            new FagiEvent($fagi)
        );

        return true;
    }

    public function delete(Fagi $fagi)
    {
        $sql = '
DELETE
    FROM
        fagi
    WHERE
        id = :id
';

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $fagi->getId());

        $this->eventDispatcher->dispatch(
            FagiEvent::DELETE_PRE_SAVE,
            new FagiEvent($fagi)
        );

        $stmt->execute();

        $this->eventDispatcher->dispatch(
            FagiEvent::DELETE_POST_SAVE,
            new FagiEvent($fagi)
        );

        return true;
    }
}
