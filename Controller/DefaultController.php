<?php

namespace Bundle\SpecBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SpecBundle:Default:index.twig.html');
    }
}
