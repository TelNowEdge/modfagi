<?php

namespace TelNowEdge\Module\modfagi\Repository;

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
        ,f.truegoto f__true_goto
        ,f.falsegoto f__false_goto
    FROM
        fagi f
';

    public function getCollection()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection;

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

    private function mapModel(array $res)
    {
        return $this->objectFromArray(Fagi::class, $res['f']);
    }
}
