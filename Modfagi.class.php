<?php

namespace FreePBX\modules;

use TelNowEdge\FreePBX\Base\Module\Module;
use TelNowEdge\Module\modfagi\Controller\AjaxController;
use TelNowEdge\Module\modfagi\Controller\DialplanController;
use TelNowEdge\Module\modfagi\Controller\FagiController;
use TelNowEdge\Module\modfagi\Controller\FunctionController;
use TelNowEdge\Module\modfagi\Controller\PageController;
use TelNowEdge\Module\modfagi\Resources\Migrations\FagiMigration;

class Modfagi extends Module implements \BMO
{
    public function install()
    {
        $this
            ->get(FagiMigration::class)
            ->migrate()
            ;
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

    public static function myDialplanHooks()
    {
        return true;
    }

    public function doDialplanHook(&$ext, $engine, $priority)
    {
        $this
            ->get(DialplanController::class)
            ->run($ext, $engine, $priority)
            ;
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
            return $this->get(AjaxController::class)->getFagiGrid();
        case 'deleteFagi':
            return $this->get(AjaxController::class)->deleteFagi();
        }
    }

    public function pageInit()
    {
        $request = $this->get('request');

        if ('fagi' !== $request->query->get('display')) {
            return;
        }

        if ('true' === $request->request->get('duplicate')) {
            return $this
                ->get(FagiController::class)
                ->duplicateAction()
                ;
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
                'value' => _('Reset'),
            ),
            'submit' => array(
                'name' => 'submit',
                'id' => 'submit',
                'value' => _('Submit'),
            ),
        );

        if (true === $request->query->has('id')) {
            $buttons['delete'] = array(
                'name' => 'delete',
                'id' => 'delete',
                'value' => _('Delete'),
            );
            $buttons['duplicate'] = array(
                'name' => 'duplicate',
                'id' => 'duplicate',
                'value' => _('Duplicate'),
            );
        }

        return $buttons;
    }

    public function getDestinations()
    {
        return $this->get(FunctionController::class)->getDestinations();
    }

    public function getDestinationInfo($dest)
    {
        return $this->get(FunctionController::class)->getDestinationInfo($dest);
    }

    public function checkDestinations($dest)
    {
        return $this->get(FunctionController::class)->checkDestinations($dest);
    }
}
