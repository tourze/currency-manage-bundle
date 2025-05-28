<?php

namespace Tourze\CurrencyManageBundle\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;

class CurrencyRateHistoryRepositoryTest extends TestCase
{
    public function test_instantiation_createsRepository(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertInstanceOf(CurrencyRateHistoryRepository::class, $repository);
    }

    public function test_inheritance_extendsServiceEntityRepository(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertInstanceOf(\Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository::class, $repository);
    }

    public function test_save_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'save'));
    }

    public function test_remove_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'remove'));
    }

    public function test_findByCurrencyAndDate_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'findByCurrencyAndDate'));
    }

    public function test_findByCurrencyCode_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'findByCurrencyCode'));
    }

    public function test_findByDateRange_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'findByDateRange'));
    }

    public function test_findLatestByCurrency_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'findLatestByCurrency'));
    }

    public function test_findAllByDate_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'findAllByDate'));
    }

    public function test_deleteBeforeDate_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'deleteBeforeDate'));
    }

    public function test_getStatistics_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'getStatistics'));
    }

    public function test_flush_methodExists(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $this->assertTrue(method_exists($repository, 'flush'));
    }

    public function test_save_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('save');
        $parameters = $method->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertSame('history', $parameters[0]->getName());
        $this->assertSame('flush', $parameters[1]->getName());
        $this->assertTrue($parameters[1]->isDefaultValueAvailable());
        $this->assertFalse($parameters[1]->getDefaultValue());
    }

    public function test_remove_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('remove');
        $parameters = $method->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertSame('history', $parameters[0]->getName());
        $this->assertSame('flush', $parameters[1]->getName());
        $this->assertTrue($parameters[1]->isDefaultValueAvailable());
        $this->assertFalse($parameters[1]->getDefaultValue());
    }

    public function test_findByCurrencyAndDate_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('findByCurrencyAndDate');
        $parameters = $method->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertSame('currencyCode', $parameters[0]->getName());
        $this->assertSame('date', $parameters[1]->getName());
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('?Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory', $returnType->__toString());
    }

    public function test_findByCurrencyCode_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('findByCurrencyCode');
        $parameters = $method->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertSame('currencyCode', $parameters[0]->getName());
        $this->assertSame('limit', $parameters[1]->getName());
        $this->assertTrue($parameters[1]->isDefaultValueAvailable());
        $this->assertNull($parameters[1]->getDefaultValue());
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('array', $returnType->__toString());
    }

    public function test_findByDateRange_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('findByDateRange');
        $parameters = $method->getParameters();
        
        $this->assertCount(3, $parameters);
        $this->assertSame('startDate', $parameters[0]->getName());
        $this->assertSame('endDate', $parameters[1]->getName());
        $this->assertSame('currencyCode', $parameters[2]->getName());
        $this->assertTrue($parameters[2]->isDefaultValueAvailable());
        $this->assertNull($parameters[2]->getDefaultValue());
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('array', $returnType->__toString());
    }

    public function test_findLatestByCurrency_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('findLatestByCurrency');
        $parameters = $method->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('currencyCode', $parameters[0]->getName());
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('?Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory', $returnType->__toString());
    }

    public function test_getStatistics_withValidSignature(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new CurrencyRateHistoryRepository($registry);
        
        $reflection = new \ReflectionClass($repository);
        $method = $reflection->getMethod('getStatistics');
        $parameters = $method->getParameters();
        
        $this->assertCount(0, $parameters);
        
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('array', $returnType->__toString());
    }
} 