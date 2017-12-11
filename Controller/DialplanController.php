<?php

namespace TelNowEdge\Module\modfagi\Controller;

use TelNowEdge\FreePBX\Base\Controller\AbstractController;
use TelNowEdge\FreePBX\Base\Exception\NoResultException;
use TelNowEdge\Module\modfagi\Repository\FagiRepository;

class DialplanController extends AbstractController
{
    public function run(&$ext, $engine, $priority)
    {
        if ('asterisk' !== $engine) {
            return;
        }

        try {
            $fagis = $this
                ->get(FagiRepository::class)
                ->getCollection()
                ;
        } catch (NoResultException $e) {
            return;
        }

        $context = 'fagi';

        foreach ($fagis as $fagi) {
            $id = $fagi->getId();
            $trueGoto = $fagi->getTrueGotoAsArray();
            $falseGoto = $fagi->getFalseGotoAsArray();

            $ext->add($context, $id, '', new \ext_noop(sprintf('Fast Agi: %s', $fagi->getDescription())));
            $ext->add($context, $id, '', new \ext_setvar('FAGIRUN', 'NO'));
            $ext->add($context, $id, '', new \ext_setvar('FAGICIDNAME', 'TNEnotsetENT'));
            $ext->add($context, $id, '', new \ext_agi($fagi->getUri()));
            $ext->add($context, $id, '', new \ext_execif('$["${FAGICIDNAME}" !="TNEnotsetENT"]', 'Set', 'CALLERID(name)=${FAGICIDNAME}'));
            $ext->add($context, $id, '', new \ext_gotoif('$["${FAGIRUN}"="NO"]', 'notrun'));
            $ext->add($context, $id, '', new \ext_goto($trueGoto[2], $trueGoto[1], $trueGoto[0]));
            $ext->add($context, $id, 'notrun', new \ext_goto($falseGoto[2], $falseGoto[1], $falseGoto[0]));
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
