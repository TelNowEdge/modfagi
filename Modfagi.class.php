<?php

namespace FreePBX\modules;

use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\FreePBX\Base\Module\Module;
use TelNowEdge\Module\modfagi\Controller\FagiController;
use TelNowEdge\Module\modfagi\Controller\PageController;
use TelNowEdge\Module\modfagi\Handler\DbHandler\FagiDbHandler;
use TelNowEdge\Module\modfagi\Repository\FagiRepository;

class Modfagi extends Module implements \BMO
{
    public function install()
    {

    }

    public function uninstall()
    {
    }

    public function backup()
    {
    }

    public function restore($backup)
    {
    }

    public function doConfigPageInit($page)
    {
    }

    public function ajaxRequest($req, &$setting)
    {
        switch ($req) {
        case 'getFagiGrid':
        case 'deleteFagi':
            return true;
        }

        return false;
    }

    public function ajaxHandler()
    {
        $request = $this->get('request');
        $command = $request->query->get('command') ?: $request->request->get('command');

        switch ($command) {
        case 'getFagiGrid':
            try {
                $res = $this
                    ->get(FagiRepository::class)
                    ->getCollection()
                    ;
            } catch(NoResultException $e) {
                $res = array();
            }

            return $this->get('serializer')->normalize($res);

        case 'deleteFagi':
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
    }

    public function pageInit()
    {
        $request = $this->get('request');

        if ('fagi' !== $request->query->get('display')) {
            return;
        }

        if (0 < $id = $request->query->getInt('id', 0)) {
            if ('del' === $request->query->get('action')) {
                return $this
                    ->get(FagiController::class)
                    ->deleteAction($id)
                    ;
            }

            return $this
                ->get(FagiController::class)
                ->editAction($id)
                ;
        }

        if ('add' === $request->query->get('action')) {
            return $this
                ->get(FagiController::class)
                ->createAction()
                ;
        }

        return $this
            ->get(PageController::class)
            ->showAction()
            ;
    }

    public function getActionBar()
    {
        $request = $this->get('request');

        if ('fagi' !== $request->query->get('display')) {
            return array();
        }

        if (false === $request->query->has('action') && false === $request->query->has('id')) {
            return array();
        }

        $buttons = array(
            'reset' => array(
                'name' => 'reset',
                'id' => 'reset',
                'value' => _('Reset')
            ),
            'submit' => array(
                'name' => 'submit',
                'id' => 'submit',
                'value' => _('Submit')
            )
        );

        if (true === $request->query->has('id')) {
            $buttons['delete'] = array(
                'name' => 'delete',
                'id' => 'delete',
                'value' => _('Delete')
            );
        }

        return $buttons;
    }
}
