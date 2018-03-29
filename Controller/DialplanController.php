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

            $ext->add($context, $id, '', new \ext_noop(sprintf('Fast Agi: %s', $fagi->getDescription())));
            $ext->add($context, $id, '', new \ext_execif('$[${EXISTS(${ITER})}]', 'Set', 'ITER=$[${ITER} + 1]', 'Set', 'ITER=0'));
            $ext->add($context, $id, '', new \ext_gotoif('$[${ITER} >= 20]', 'app-blackhole,hangup,1'));

            $ext->add($context, $id, '', new \ext_setvar('AGISTATUS', 'ERROR'));
            $ext->add($context, $id, '', new \ext_setvar('FAGIRUN', 'TNE_FAKEVALUE_ENT'));
            $ext->add($context, $id, '', new \ext_setvar('FAGICIDNAME', 'TNEnotsetENT'));
            $ext->add($context, $id, '', new \ext_agi($fagi->getUri()));
            $ext->add($context, $id, '', new \ext_execif('$["${FAGICIDNAME}" != "TNEnotsetENT"]', 'Set', 'CALLERID(name)=${FAGICIDNAME}'));
            $ext->add($context, $id, '', new \ext_gotoif('$["${AGISTATUS}" != "SUCCESS"]', 'fallback'));

            foreach ($fagi->getFagiResults() as $fagiResult) {
                $ext->add($context, $id, '', new \ext_gotoif(
                    sprintf('$["${FAGIRUN}" = "%s"]', $fagiResult->getMatch()),
                    $fagiResult->getGoto()->getDestination(),
                    false
                ));
            }

            $ext->add($context, $id, 'fallback', new \ext_goto($fagi->getFallback()->getDestination()));
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
