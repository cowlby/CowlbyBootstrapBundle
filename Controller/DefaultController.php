<?php

namespace Cowlby\Bundle\BootstrapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CowlbyBootstrapBundle:Default:index.html.twig', array('name' => $name));
    }
}
