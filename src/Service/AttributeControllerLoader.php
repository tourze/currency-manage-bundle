<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\RouteCollection;
use Tourze\CurrencyManageBundle\Controller\Api\Flag1x1Controller;
use Tourze\CurrencyManageBundle\Controller\Api\FlagController;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;

#[Autoconfigure(public: true)]
#[AutoconfigureTag(name: 'routing.loader')]
class AttributeControllerLoader extends Loader implements RoutingAutoLoaderInterface
{
    public function __construct(
        #[Autowire(service: 'routing.loader.attribute')]
        private AttributeRouteControllerLoader $controllerLoader,
    ) {
        parent::__construct();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->autoload();
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return false;
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->addCollection($this->controllerLoader->load(FlagController::class));
        $collection->addCollection($this->controllerLoader->load(Flag1x1Controller::class));

        return $collection;
    }
}
