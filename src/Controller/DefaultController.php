<?php

/*
 * This file is part of the Chellal project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Developed by Monarkit
 *
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(){
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(){
        return $this->render('home.html.twig');
    }
}
