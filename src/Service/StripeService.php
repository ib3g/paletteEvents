<?php

namespace App\Service;

use App\Entity\Prix;
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
    public function createCheckout(User $user, $price_id, $mode = 'payment'){
        $this->initStripe();

        $price = $this->getByStripeId($price_id);
        try {
            $line_items_data = [
                'price' => $price_id,
                'quantity' => 1,
            ];
            $data = [
                'customer' => $this->checkCustomer($user),
                'payment_method_types' => ['card'],
                'line_items' => [$line_items_data],
                'mode' => $mode,
                'success_url' => $this->container->getParameter('app_url').'/event/stripe-payment-succedeed/'.$price_id.'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->container->getParameter('app_url').'/events',
                'locale' => 'fr',
                'allow_promotion_codes' => true,
//                "phone_number_collection"=>[
//                    'enabled' => true,
//                ]
            ];
            if ('payment' === $mode) {
                $data['billing_address_collection'] = 'required';
                $data['payment_intent_data'] = [
                    'metadata' => [
                        'price_id' => $price_id,
                    ],
                ];
            }
            $session = \Stripe\Checkout\Session::create($data);
        }catch (ApiErrorException $e){
            return $e->getMessage();
        }
        return $session;
    }
    public function getByStripeId($stripe_id)
    {
        return $this->manager->getRepository(Prix::class)->findOneBy(['stripe_price_id' => $stripe_id]);
    }
    public function getSession($session_id)
    {
        $this->initStripe();

        try {
            $session = \Stripe\Checkout\Session::retrieve($session_id);
        } catch (ApiErrorException $e) {
            return null;
        }

        return $session;
    }

}