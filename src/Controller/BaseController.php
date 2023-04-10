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

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(){
        return $this->render('home.html.twig');
    }
    protected function addSuccessFlash($message = 'Votre opération a bien été effectuée !')
    {
        $this->addFlash('success', '<i class="icon-check"></i> '.$message);
    }

    protected function addWarningFlash($message)
    {
        $this->addFlash('warning', '<i class="icon-warning"></i> '.$message);
    }

    protected function addErrorFlash($message)
    {
        $this->addFlash('danger', '<i class="icon-x"></i> '.$message);
    }

    protected function addInfoFlash($message)
    {
        $this->addFlash('info', '<i class="icon-info3"></i> '.$message);
    }
}
