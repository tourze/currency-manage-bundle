<?php

namespace Tourze\CurrencyManageBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\CurrencyManageBundle\CurrencyManageBundle;

class CurrencyManageBundleTest extends TestCase
{
    private CurrencyManageBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new CurrencyManageBundle();
    }

    public function test_instantiation_createsBundle(): void
    {
        $this->assertInstanceOf(CurrencyManageBundle::class, $this->bundle);
    }

    public function test_inheritance_extendsBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function test_getName_returnsCorrectName(): void
    {
        $expectedName = 'CurrencyManageBundle';
        
        $result = $this->bundle->getName();
        
        $this->assertSame($expectedName, $result);
    }

    public function test_getNamespace_returnsCorrectNamespace(): void
    {
        $expectedNamespace = 'Tourze\CurrencyManageBundle';
        
        $result = $this->bundle->getNamespace();
        
        $this->assertSame($expectedNamespace, $result);
    }

    public function test_getPath_returnsCorrectPath(): void
    {
        $result = $this->bundle->getPath();
        
        $this->assertStringContainsString('currency-manage-bundle', $result);
        $this->assertStringEndsWith('src', $result);
    }
} 