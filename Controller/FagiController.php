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
use TelNowEdge\Module\modfagi\Form\FagiType;
use TelNowEdge\Module\modfagi\Handler\DbHandler\FagiDbHandler;
use TelNowEdge\Module\modfagi\Repository\FagiRepository;

class FagiController extends AbstractController
{
    public function createAction()
    {
        $request = $this->get('request');

        $form = $this->createForm(
            FagiType::class
        );

        $form->handleRequest($request);

        if (true === $form->isValid()) {
            needreload();

            $this->get(FagiDbHandler::class)
                 ->create($form->getData())
                ;

            redirect(
                sprintf('config.php?display=fagi&id=%d', $form->getData()->getId())
            );
        }

        return $this->processForm($form);
    }

    public function duplicateAction()
    {
        $request = $this->get('request');

        $form = $this->createForm(
            FagiType::class
        );

        $form->handleRequest($request);

        if (true === $form->isValid()) {
            needreload();

            $fagi = $form->getData();

            try {
                $storedFagis = $this
                    ->get(FagiRepository::class)
                    ->getByDisplayNameLike($fagi->getDisplayName())
                    ;

                $names = array();
                foreach ($storedFagis as $storedFagi) {
                    array_push($names, $storedFagi->getDisplayName());
                }

                rsort($names);

                $name = reset($names);

                if (0 !== preg_match('/(.+)_(.+)$/', $name, $match)) {
                    $fagi->setDisplayName(sprintf('%s_%d', $match[1], (int) $match[2] + 1));
                } else {
                    $fagi->setDisplayName(sprintf('%s_1', $name));
                }
            } catch (NoResultException $e) {
            }

            $this->get(FagiDbHandler::class)
                 ->create($fagi)
                ;

            redirect(
                sprintf('config.php?display=fagi&id=%d', $form->getData()->getId())
            );
        }

        return $this->processForm($form);
    }

    public function editAction($id)
    {
        $request = $this->get('request');

        try {
            $fagi = $this
                ->get(FagiRepository::class)
                ->getById($id)
                ;
        } catch (NoResultException $e) {
            return;
        }

        $form = $this->createForm(
            FagiType::class,
            $fagi
        );

        $form->handleRequest($request);

        if (true === $form->isValid()) {
            needreload();

            $this->get(FagiDbHandler::class)
                 ->update($fagi)
                ;
        }

        $usedBy = framework_check_destination_usage(sprintf('fagi,%d,1', $fagi->getId()));

        return $this->processForm($form, $id, $usedBy);
    }

    public function deleteAction($id)
    {
        try {
            $fagi = $this
                ->get(FagiRepository::class)
                ->getById($id)
                ;
        } catch (NoResultException $e) {
            return;
        }

        $this->get(FagiDbHandler::class)
             ->delete($fagi)
            ;

        needreload();
        redirect('config.php?display=fagi');
    }

    public function getRightNav()
    {
        return $this->render('right-nav.html.twig');
    }

    public static function getViewsDir()
    {
        return sprintf('%s/../views', __DIR__);
    }

    public static function getViewsNamespace()
    {
        return 'fagi';
    }

    private function processForm(\Symfony\Component\Form\Form $form, $id = null, $usedBy = null)
    {
        return $this->render('fagi.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
            'usedBy' => $usedBy,
        ));
    }
}
