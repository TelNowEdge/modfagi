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

namespace TelNowEdge\Module\modfagi\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\modfagi\Event\FagiEvent;
use TelNowEdge\Module\modfagi\Handler\DbHandler\FagiResultDbHandler;
use TelNowEdge\Module\modfagi\Model\FagiResult;
use TelNowEdge\Module\modfagi\Repository\FagiResultRepository;

class FagiSubscriber implements EventSubscriberInterface, ContainerAwareInterface
{
    /**
     * Symfony\Component\DependencyInjection\ContainerInterface.
     */
    private $container;

    public static function getSubscribedEvents()
    {
        return array(
            FagiEvent::CREATE_POST_SAVE => array(
                array('childrenProcess', 1000),
            ),
            FagiEvent::UPDATE_POST_SAVE => array(
                array('childrenProcess', 1000),
            ),
            FagiEvent::DELETE_PRE_SAVE => array(
                array('deleteChildren', 1000),
            ),
        );
    }

    public function childrenProcess(FagiEvent $event)
    {
        $fagi = $event->getFagi();

        try {
            $actualFagiResults = $this->container
                ->get(FagiResultRepository::class)
                ->getByFagi($fagi)
                ;
        } catch (NoResultException $e) {
            $actualFagiResults = new \Doctrine\Common\Collections\ArrayCollection();
        }

        $added = array_udiff(
            $fagi->getFagiResults()->getValues(),
            $actualFagiResults->getValues(),
            array($this, 'compare')
        );

        $removed = array_udiff(
            $actualFagiResults->getValues(),
            $fagi->getFagiResults()->getValues(),
            array($this, 'compare')
        );

        $changed = array_udiff(
            array_uintersect(
                $fagi->getFagiResults()->getValues(),
                $actualFagiResults->getValues(),
                array($this, 'compare')
            ),
            $added,
            $removed,
            array($this, 'compare')
        );

        foreach ($added as $x) {
            $x->setFagi($fagi);

            $this->container
                ->get(FagiResultDbHandler::class)
                ->create($x)
                ;
        }

        foreach ($removed as $x) {
            $this->container
                ->get(FagiResultDbHandler::class)
                ->delete($x)
                ;
        }

        foreach ($changed as $x) {
            $this->container
                ->get(FagiResultDbHandler::class)
                ->update($x)
                ;
        }
    }

    public function deleteChildren(FagiEvent $event)
    {
        $fagi = $event->getFagi();

        foreach ($fagi->getFagiResults() as $fagiResult) {
            $this->container
                ->get(FagiResultDbHandler::class)
                ->delete($fagiResult)
                ;
        }
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function compare(FagiResult $a, FagiResult $b)
    {
        if ($a->getId() < $b->getId()) {
            return -1;
        } elseif ($a->getId() === $b->getId()) {
            return 0;
        }

        return 1;
    }
}
