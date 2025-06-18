<?php

namespace Tourze\CurrencyManageBundle\Tests\Repository;

use PHPUnit\Framework\TestCase;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;

class CountryRepositoryTest extends TestCase
{
    // 测试方法签名而不是仅检查方法是否存在

    public function test_findByAlpha2Code_withValidEnum(): void
    {
        // 测试方法签名和参数类型
        $reflection = new \ReflectionMethod(CountryRepository::class, 'findByAlpha2Code');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('alpha2Code', $parameters[0]->getName());
        
        // 检查返回类型
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('?Tourze\CurrencyManageBundle\Entity\Country', $returnType->__toString());
    }

    public function test_searchByName_withValidString(): void
    {
        // 测试方法签名
        $reflection = new \ReflectionMethod(CountryRepository::class, 'searchByName');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(1, $parameters);
        $this->assertSame('name', $parameters[0]->getName());
        
        // 检查返回类型
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('array', $returnType->__toString());
    }

    public function test_save_methodSignature(): void
    {
        $reflection = new \ReflectionMethod(CountryRepository::class, 'save');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertSame('country', $parameters[0]->getName());
        $this->assertSame('flush', $parameters[1]->getName());
        
        // 检查默认值
        $this->assertFalse($parameters[1]->getDefaultValue());
    }

    public function test_remove_methodSignature(): void
    {
        $reflection = new \ReflectionMethod(CountryRepository::class, 'remove');
        $parameters = $reflection->getParameters();
        
        $this->assertCount(2, $parameters);
        $this->assertSame('country', $parameters[0]->getName());
        $this->assertSame('flush', $parameters[1]->getName());
        
        // 检查默认值
        $this->assertFalse($parameters[1]->getDefaultValue());
    }
} 