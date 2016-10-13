<?php

namespace Petkopara\CrudGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PetkoparaCrudGeneratorBundle:Default:index.html.twig');
    }
}
