<?php

namespace App\Menu;

use App\Entity\Role;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var FactoryInterface */
    private $factory;

    /** @var RequestStack */
    private $requestStack;

    /**
     * Builder constructor.
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authorizationChecker, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');
        // Disable label translation
        $menu->setExtra('translation_domain', false);
        $menu->setChildrenAttribute('class', 'nav nav-sidebar');

        $menuList = [
            'utilisateur' => [
                'route' => 'app_user_index',
                'label' => 'Utilisateurs',
                'icon' => 'icon-users fa-1rem',
                'roles' => [
                    Role::ROLE_ADMIN,
                ]
            ],
            'Events' => [
                'route' => 'admin_events_index',
                'label' => 'Events',
                'icon' => 'icon-folder icon-1rem',
                'roles' => [
                    Role::ROLE_ORGANISATEUR,
                    Role::ROLE_ADMIN,
                ]
            ],
            'Demandes' => [
                'route' => 'app_demande_index',
                'label' => 'Demandes',
                'icon' => 'icon-folder icon-1rem',
                'roles' => [
                    Role::ROLE_ORGANISATEUR,
                ]
            ],
            'Tags' => [
                'route' => 'app_tag_index',
                'label' => 'Tags',
                'icon' => 'icon-folder icon-1rem',
                'roles' => [
                    Role::ROLE_ADMIN
                ]
            ],
            'Category' => [
                'route' => 'app_category_index',
                'label' => 'Catégories',
                'icon' => 'icon-folder icon-1rem',
                'roles' => [
                    Role::ROLE_ADMIN
                ]
            ],
        ];

        $requestUri = $this->requestStack->getCurrentRequest()->getRequestUri();

        foreach ($menuList as $name => $item) {
            $roles = $item['roles'];
            $granted = false;
            foreach ($roles as $role) {
                if ($this->authorizationChecker->isGranted($role)) {
                    $granted = true;
                }
            }

            if ($granted) {

                $routeNames = match ($name) {
                    'utilisateur' => [$name, 'user'],
                    'Events' => [$name, 'event'],
                    'Demandes' => [$name, 'demande'],
                    'Tags' => [$name, 'tag'],
                    'Category' => [$name, 'category'],
                    default => [$name],
                };

                $color = '';
                foreach ($routeNames as $routeName) {
                    if (str_contains($requestUri, $routeName) && !empty($routeName)) {
                        $color = 'text-paletteEventLight';
                    }
                }

                $menu->addChild($name, [
                    'route' => $item['route'],
                    'label' => '<i class="'.$color.' '.$item["icon"].'"> </i> <span class="menu-label font-weight-bold '.$color.'">'. $item['label'] .'</span>',
                    'extras' => ['safe_label' => true],
                ])
                    ->setAttribute('class', 'nav-item')
                    ->setLinkAttribute('class', 'nav-link');


            }
        }


        return $menu;
    }
}
