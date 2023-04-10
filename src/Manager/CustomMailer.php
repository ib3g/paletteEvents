<?php

namespace App\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class CustomMailer
{
    /** @var ContainerInterface */
    private $container;
    private $twig;

    public function __construct(ContainerInterface $container, Environment $twig)
    {
        $this->container = $container;
        $this->twig = $twig;
    }


    public function send($sujet, $message, $to, $from = '') {

        $mailer_from = empty($from) ? $this->container->getParameter('app.mailer_from'): $from;
        $mailer_dns = $this->container->getParameter('app.mailer_dns');

        $templatedEmail = (new Email())
            ->from($mailer_from)
            ->to($to)
            ->subject($sujet)
            ->html($message);

        $transport = Transport::fromDsn($mailer_dns);

        $mailer = new Mailer($transport);

        $mailer->send($templatedEmail);
    }
}