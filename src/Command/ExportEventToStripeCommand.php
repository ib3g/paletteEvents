<?php

namespace App\Command;

use App\Entity\Event;
use App\Entity\Prix;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\Product;
use Stripe\Stripe;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExportEventToStripeCommand extends Command
{
    protected static $defaultName = 'app:export-events-stripe';
    protected static $defaultDescription = 'Add a short description for your command';

    protected ContainerInterface $container;
    private EntityManagerInterface $entityManager;
    protected ?string $stripe_secret;


    public function __construct(ContainerInterface $container,EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->stripe_secret = $container->getParameter('stripe_secret');
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Stripe::setApiKey($this->stripe_secret);
        $io = new SymfonyStyle($input, $output);
        $total = 0;
        $arrayCategories=[];
        $categories=[];
        /** @var Event[] $events */
        $events = $this->entityManager->getRepository(Event::class)->findAll();
//        $events = \array_slice($events, 0, 6);
        $stripe = new \Stripe\StripeClient($this->stripe_secret);
        foreach ($events as $i=>$event) {
            $product = $event->getStripeEventId() == null ? null : \Stripe\Product::retrieve($event->getStripeEventId());
            if(!$product) {
                $categories = $event->getCategories()->getValues();
                foreach ($categories as $category) {
                    $arrayCategories[] = $category->getName();
                }
                try {
                    $product = \Stripe\Product::create([
                        'name' => $event->getTitle(),
                        'description' => $event->getShortDescription(),
                        'metadata' => ['categories' => $arrayCategories[0]],
                    ]);
                    $productId = $product->id;
                    if (!$event->getStripeEventId()) {
                        $event->setStripeEventId($productId);
                        $this->entityManager->persist($event);
                        $this->entityManager->flush();
                    }
                    $total++;
                } catch (ApiErrorException $e) {
                    return $e->getMessage();
                }
            }
                    $io->success("Create prices to Event".$event->getTitle());
                    /** @var Prix $p */
                    foreach ($event->getPrix() as $p) {
                        $price=$p->getStripePriceId() == null ? null : \Stripe\Price::retrieve($p->getStripePriceId());
                      if(!$price) {
                          $price = \Stripe\Price::create([
                              'unit_amount' => $p->getSomme() * 100,
                              "currency" => 'usd',
                              'product' => $product->id,
                              "metadata" => ['type'=> $p->getType()]
                          ]);
                      }
                        $priceId = $price->id;
                        if(!$p->getStripePriceId()){
                            $p->setStripePriceId($priceId);
                            $this->entityManager->persist($p);
                            $this->entityManager->flush();
                        }
                    }


        }
        $io->success("You have added $total events to Stripe.");

        return 0;
    }
}