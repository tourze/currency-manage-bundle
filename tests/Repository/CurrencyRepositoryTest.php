<?php

namespace Tourze\CurrencyManageBundle\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;

class CurrencyRepositoryTest extends TestCase
{
    public function test_instantiation_createsRepository(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRepository($registry);
        
        $this->assertInstanceOf(CurrencyRepository::class, $repository);
    }

    public function test_inheritance_extendsServiceEntityRepository(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRepository($registry);
        
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $repository);
    }

    // 直接测试方法签名和功能，而不是检查方法是否存在

    public function test_save_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('save');
        $parameters = $method->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertSame('currency', $parameters[0]->getName());
        $this->assertSame('flush', $parameters[1]->getName());
        $this->assertTrue($parameters[1]->isDefaultValueAvailable());
        $this->assertFalse($parameters[1]->getDefaultValue());
    }

    public function test_remove_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('remove');
        $parameters = $method->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertSame('currency', $parameters[0]->getName());
        $this->assertSame('flush', $parameters[1]->getName());
        $this->assertTrue($parameters[1]->isDefaultValueAvailable());
        $this->assertFalse($parameters[1]->getDefaultValue());
    }

    public function test_findByCode_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('findByCode');
        $parameters = $method->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('code', $parameters[0]->getName());
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('?Tourze\CurrencyManageBundle\Entity\Currency', $returnType->__toString());
    }
} 