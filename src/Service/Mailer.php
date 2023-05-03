<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    protected \Swift_Mailer $mailer;
    protected TranslatorInterface $translator;
    protected ContainerInterface $container;

    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->container = $container;
    }


    public function sendEmail($mailer,$subject, $html,$from, $to, $sender){
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->cc($sender)
            ->replyTo($from)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->html($html);

        $mailer->send($email);
    }

    /**
     * @param array          $options
     * @param UploadedFile[] $files
     *
     * @return int
     */

    public function sendHTML($subject, $html, $to, $options = [], $files = [], $sender = [])
    {
        $adresse=$this->container->getParameter('mailer_from_address');
        $name=$this->container->getParameter('mailer_from_name');

        $message = (new \Swift_Message($subject))

            ->setFrom(
                !empty($sender['email']) ? $sender['email'] : $adresse,
                !empty($sender['name']) ? $sender['name'] : $name
            )
            ->setTo([$to])
            ->setBody($html, 'text/html')
        ;
        if (isset($options['cc'])) {
            $message->setCc($options['cc']);
        }

        if (isset($options['cci'])) {
            $message->setBcc($options['cci']);
        }

        if (isset($options['reply_to'])) {
            $message->setReplyTo($options['reply_to']);
        }

        if (isset($options['sender'])) {
            if (\is_array($options['sender'])) {
                $message->setSender($options['sender'][0], $options['sender'][1]);
            } else {
                $message->setSender($options['sender']);
            }
        }

        if (isset($options['subject'])) {
            $message->setSubject($options['subject']);
        }

        foreach ($files as $file) {
            $attachment = \Swift_Attachment::fromPath($file->getPathname(), $file->getClientMimeType());
            $attachment->setFilename($file->getClientOriginalName());
            $message->attach($attachment);
        }

        return $this->mailer->send($message);
    }

    public function send($slug, $to, $params, $subject_params = [], $options = [], $files = [])
    {
        $subject = $this->translator->trans("$slug.subject", $subject_params, 'mails');

        $slug = str_replace('.', '/', $slug);

        $html = $this->container->get('twig')->render(
            "mails/$slug.html.twig",
            $params
        );

        return $this->sendHTML($subject, $html, $to, $options, $files);
    }

    public function sendValidateEmail($slug, $to, $params, $from, $mailer, $subject_params = [])
    {
        if( $slug == "StripeUsers.newStripeUser"){
            $subject = $this->translator->trans("Activate your account and reset your password", $subject_params, 'mails');
        }else{
            $subject = $this->translator->trans("$slug.subject", $subject_params, 'mails');

        }

        $slug = str_replace('.', '/', $slug);

        $html = $this->container->get('twig')->render(
            "mails/$slug.html.twig",
            $params
        );

        return $this->sendEmail($mailer,$subject, $html,$from, $to, $from);
    }
}
