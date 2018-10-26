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
        try {
            $fagis = $this
                ->get(FagiRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return array();
        }

        foreach ($fagis as $fagi) {
            array_push($results, array(
                'text' => sprintf('[FAGI] %s', $fagi->getDisplayName()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI][%s] %s', $fagi->getDisplayName(), $fagi->getDescription()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI][%s] %s', $fagi->getDisplayName(), $fagi->getHost()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI][%s] %s', $fagi->getDisplayName(), $fagi->getPath()),
                'type' => 'get',
                'dest' => sprintf('?display=fagi&id=%d', $fagi->getId()),
            ));

            array_push($results, array(
                'text' => sprintf('[FAGI][%s] %s', $fagi->getDisplayName(), $fagi->getQuery()),
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
