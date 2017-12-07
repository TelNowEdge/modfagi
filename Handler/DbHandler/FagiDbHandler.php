<?php

namespace TelNowEdge\Module\modfagi\Handler\DbHandler;

use TelNowEdge\FreePBX\Base\Handler\AbstractDbHandler;
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
            ,`truegoto`
            ,`falsegoto`
        )
    VALUES (
        :displayName
        ,:description
        ,:host
        ,:port
        ,:path
        ,:query
        ,:trueGoto
        ,:falseGoto
    )
';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('displayName', $fagi->getDisplayName());
        $stmt->bindParam('description', $fagi->getDescription());
        $stmt->bindParam('host', $fagi->getHost());
        $stmt->bindParam('port', $fagi->getPort());
        $stmt->bindParam('path', $fagi->getPath());
        $stmt->bindParam('query', $fagi->getQuery());
        $stmt->bindParam('trueGoto', $fagi->getTrueGoto()->getDestination());
        $stmt->bindParam('falseGoto', $fagi->getFalseGoto()->getDestination());

        $stmt->execute();

        $fagi->setId($this->connection->lastInsertId());

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
        ,truegoto = :trueGoto
        ,falsegoto = :falseGoto
    WHERE
        id = :id
';
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $fagi->getId());
        $stmt->bindParam('displayName', $fagi->getDisplayName());
        $stmt->bindParam('description', $fagi->getDescription());
        $stmt->bindParam('host', $fagi->getHost());
        $stmt->bindParam('port', $fagi->getPort());
        $stmt->bindParam('path', $fagi->getPath());
        $stmt->bindParam('query', $fagi->getQuery());
        $stmt->bindParam('trueGoto', $fagi->getTrueGoto()->getDestination());
        $stmt->bindParam('falseGoto', $fagi->getFalseGoto()->getDestination());

        $stmt->execute();

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

        $id = $fagi->getId();

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam('id', $id);

        $stmt->execute();

        return true;
    }
}
