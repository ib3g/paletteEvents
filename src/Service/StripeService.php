<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StripeService
{
    protected ContainerInterface $container;
    protected EntityManagerInterface $manager;
    protected $stripe_secret;

    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $manager
    ){
        $this->container = $container;
        $this->manager = $manager;
        $this->stripe_secret =  $this->container->getParameter('stripe_secret');
    }

    public function initStripe()
    {
        Stripe::setApiKey($this->stripe_secret);
    }
    public function getCustomer(User $user)
    {
        $stripe = new StripeClient($this->stripe_secret);

        try {
            $customer = $stripe->customers->retrieve($this->checkCustomer($user));

        } catch (ApiErrorException $e) {
            return $e->getMessage();
        }
        return $customer;
    }
    public function checkCustomer(User $user)
    {
        $this->initStripe();

        $em = $this->manager;

        if (empty($user->getStripeCustomerId())) {
            try {
                $customer = \Stripe\Customer::create([
                    'description' => 'Client pour '.$user->getEmail(),
                    'email' => $user->getEmail(),
                    'name' => $user->getFullName(),
                    'metadata' => ['Nom' => $user->getFullName()],
                ]);

                $customerId = $customer['id'];
                $user->setStripeCustomerId($customerId);
                $em->persist($user);
                $em->flush();
            } catch (ApiErrorException $e) {
                return $e->getMessage();
            }
        }

        return $user->getStripeCustomerId();
    }
}