<?php

namespace TelNowEdge\Module\modfagi\Controller;

use TelNowEdge\FreePBX\Base\Controller\AbstractController;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\modfagi\Handler\DbHandler\FagiDbHandler;
use TelNowEdge\Module\modfagi\Repository\FagiRepository;

class AjaxController extends AbstractController
{
    public function getFagiGrid()
    {
        try {
            $res = $this
                ->get(FagiRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            $res = array();
        }

        return $this->get('serializer')->normalize($res);
    }

    public function deleteFagi()
    {
        $request = $this->get('request');

        $id = $request->request->getInt('id', 0);

        if (0 >= $id) {
            return false;
        }

        try {
            $fagi = $this
                ->get(FagiRepository::class)
                ->getById($id)
                ;
        } catch (NoResultException $e) {
            return $this->get('serializer')->normalize(array('success' => false));
        }

        $this->get(FagiDbHandler::class)
             ->delete($fagi)
            ;

        return $this->get('serializer')->normalize(array('success' => true));
    }

    public function search($query, &$results)
    {
        $fagis = $this->get(FagiRepository::class)
                      ->getCollection()
            ;

        foreach ($fagis as $fagi) {
            array_push($results, array(
                'text' => sprintf('[FAGI] %s', $fagi->getId()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI] %s', $fagi->getDisplayName()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI] %s', $fagi->getDescription()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI] %s', $fagi->getHost()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI] %s', $fagi->getPath()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI] %s', $fagi->getQuery()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));
        }
    }

    public static function getViewsDir()
    {
        return sprintf('%s/../views', __DIR__);
    }

    public static function getViewsNamespace()
    {
        return 'fagi';
    }
}
