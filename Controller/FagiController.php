<?php

namespace TelNowEdge\Module\modfagi\Controller;

use TelNowEdge\FreePBX\Base\Controller\AbstractController;
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

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $this->get(FagiDbHandler::class)
                 ->create($form->getData())
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
        } catch(NoResultException $e) {
            return;
        }

        $form = $this->createForm(
            FagiType::class,
            $fagi
        );

        $form->handleRequest($request);

        if (true === $form->isSubmitted() && true === $form->isValid()) {
            $this->get(FagiDbHandler::class)
                 ->update($fagi)
                ;
        }

        return $this->processForm($form, $id);
    }

    public function deleteAction($id)
    {
        $request = $this->get('request');

        try {
            $fagi = $this
                ->get(FagiRepository::class)
                ->getById($id)
                ;
        } catch(NoResultException $e) {
            return;
        }

        $this->get(FagiDbHandler::class)
             ->delete($fagi)
            ;

        redirect('config.php?display=fagi');
    }

    public static function getViewsDir()
    {
        return sprintf('%s/../views', __DIR__);
    }

    public static function getViewsNamespace()
    {
        return 'fagi';
    }

    private function processForm(\Symfony\Component\Form\Form $form, $id = null)
    {
        return $this->render('fagi.html.twig', array(
            'form' => $form->createView(),
            'id' => $id,
        ));
    }
}
