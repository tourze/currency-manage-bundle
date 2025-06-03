<?php

namespace Tourze\CurrencyManageBundle;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class CurrencyManageBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
            \Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle::class => ['all' => true],
            \Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle::class => ['all' => true],
        ];
    }

    public function boot(): void
    {
        parent::boot();
        assert(class_exists(HttpClient::class));
    }
}
