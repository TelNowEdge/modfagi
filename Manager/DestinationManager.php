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

namespace TelNowEdge\Module\modfagi\Manager;

use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\modfagi\Repository\FagiRepository;

class DestinationManager
{
    /**
     * TelNowEdge\Module\modfagi\Repository\FagiRepository.
     */
    private $fagiRepository;

    public function __construct(FagiRepository $fagiRepository)
    {
        $this->fagiRepository = $fagiRepository;
    }

    public function getAll()
    {
        $output = array();

        try {
            $fagis = $this->fagiRepository
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return array();
        }

        foreach ($fagis as $fagi) {
            foreach ($fagi->getFagiResults() as $fagiResult) {
                array_push($output, array(
                    'dest' => $fagiResult->getGoto()->getDestination(),
                    'description' => sprintf(
                        '[%s] %s',
                        $fagiResult->getMatch(),
                        $fagi->getDisplayName()
                    ),
                    'edit_url' => sprintf('config.php?display=fagi&id=%d', $fagi->getId()),
                ));
            }

            array_push($output, array(
                'dest' => $fagi->getFallback()->getDestination(),
                'description' => sprintf(
                    '[%s] %s',
                    'Fallback',
                    $fagi->getDisplayName()
                ),
                'edit_url' => sprintf('config.php?display=fagi&id=%d', $fagi->getId()),
            ));
        }

        return $output;
    }

    public function getByDestination(array $destination)
    {
        $fagis = array();
        $fallbacks = array();
        $output = array();

        try {
            $fagis = $this->fagiRepository
                ->getByGotos($destination)
                ;
        } catch (NoResultException $e) {
            // Do nothing
        }

        try {
            $fallbacks = $this->fagiRepository
                ->getByFallBack($destination)
                ;
        } catch (NoResultException $e) {
            // Do nothing
        }

        foreach ($fagis as $fagi) {
            foreach ($fagi->getFagiResults() as $fagiResult) {
                foreach ($destination as $x) {
                    if ($fagiResult->getGoto()->getDestination() !== $x) {
                        continue;
                    }

                    array_push($output, array(
                        'dest' => $fagiResult->getGoto()->getDestination(),
                        'description' => sprintf(
                            '[%s] %s',
                            $fagiResult->getMatch(),
                            $fagi->getDisplayName()
                        ),
                        'edit_url' => sprintf('config.php?display=fagi&id=%d', $fagi->getId()),
                    ));
                }
            }
        }

        foreach ($fallbacks as $fagi) {
            foreach ($destination as $x) {
                if ($fagi->getFallback()->getDestination() !== $x) {
                    continue;
                }

                array_push($output, array(
                    'dest' => $fagi->getFallback()->getDestination(),
                    'description' => sprintf(
                        '[%s] %s',
                        'Fallback',
                        $fagi->getDisplayName()
                    ),
                    'edit_url' => sprintf('config.php?display=fagi&id=%d', $fagi->getId()),
                ));
            }
        }

        return $output;
    }
}
