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
use TelNowEdge\Module\modfagi\Repository\FagiRepository;

class FunctionController extends AbstractController
{
    public function getDestinations()
    {
        try {
            $fagis = $this
                ->get(FagiRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return null;
        }

        return array_map(function ($x) {
            return array(
                'destination' => sprintf('fagi,%d,1', $x->getId()),
                'description' => $x->getDisplayName(),
            );
        }, $fagis->toArray());
    }

    public function getDestinationInfo($dest)
    {
        if (0 === preg_match('/^fagi,([^,]+),/', $dest, $match)) {
            return false;
        }

        try {
            $fagi = $this
                ->get(FagiRepository::class)
                ->getById($match[1])
                ;
        } catch (NoResultException $e) {
            return false;
        }

        return array(
            'description' => sprintf(_('FastAgi %s : %s'), $fagi->getId(), $fagi->getDescription()),
            'edit_url' => sprintf('config.php?display=fagi&id=%d', $fagi->getId()),
        );
    }

    public function checkDestinations($dest)
    {
        if (true === is_array($dest) && true === empty($dest)) {
            return array();
        }

        try {
            if (true === $dest) {
                $fagis = $this
                    ->get(FagiRepository::class)
                    ->getCollection()
                    ;
            } else {
                $fagis = $this
                    ->get(FagiRepository::class)
                    ->getByBothGoto(implode(',', $dest))
                    ;
            }
        } catch (NoResultException $e) {
            return array();
        }

        $output = array();

        foreach ($fagis as $fagi) {
            if (true !== $dest && $fagi->getTrueGoto() !== $dest && $fagi->getFalseGoto() !== $dest) {
                continue;
            }

            $destination = $fagi->getTrueGoto() === $dest
                ? $fagi->getTrueGoto()
                : $fagi->getFalseGoto()
                ;

            array_push($output, array(
                'dest' => $destination->getDestination(),
                'description' => $fagi->getDescription(),
                'edit_url' => sprintf('config.php?display=fagi&id=%d', $fagi->getId()),
            ));
        }

        return $output;
    }

    public static function getViewsDir()
    {
    }

    public static function getViewsNamespace()
    {
    }
}
