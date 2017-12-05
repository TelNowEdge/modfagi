<?php

namespace TelNowEdge\Module\modfagi\Controller;

use TelNowEdge\FreePBX\Base\Controller\AbstractController;

class PageController extends AbstractController
{
    public function showAction()
    {
        return $this->render('page.html.twig');
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
