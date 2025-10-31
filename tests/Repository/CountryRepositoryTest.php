<?php

namespace Tourze\CurrencyManageBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;
use Tourze\GBT2659\Alpha2Code;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CountryRepository::class)]
#[RunTestsInSeparateProcesses]
final class CountryRepositoryTest extends AbstractRepositoryTestCase
{
    private CountryRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(CountryRepository::class);
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $country = new Country();
        $country->setCode('T3');
        $country->setName('Test Country 3');
        $country->setValid(true);

        $this->repository->save($country, false);
        $this->assertNull($country->getId());

        $this->repository->flush();
        $this->assertNotNull($country->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $country = new Country();
        $country->setCode('T4');
        $country->setName('Test Country 4');
        $country->setValid(true);

        $this->repository->save($country);
        $id = $country->getId();
        $this->assertNotNull($id);

        $this->repository->remove($country);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByCodeWithExistingCodeShouldReturnEntity(): void
    {
        $country = new Country();
        $country->setCode('T5');
        $country->setName('Test Country 5');
        $country->setValid(true);

        $this->repository->save($country);

        $found = $this->repository->findByCode('T5');
        $this->assertInstanceOf(Country::class, $found);
        $this->assertSame('T5', $found->getCode());
        $this->assertSame('Test Country 5', $found->getName());
    }

    public function testFindByCodeWithNonExistentCodeShouldReturnNull(): void
    {
        $result = $this->repository->findByCode('XX');
        $this->assertNull($result);
    }

    public function testFindByAlpha2CodeWithValidEnum(): void
    {
        // Fixtures 已经加载了所有国家，包括 US
        $found = $this->repository->findByAlpha2Code(Alpha2Code::US);
        $this->assertInstanceOf(Country::class, $found);
        $this->assertSame('US', $found->getCode());
        $this->assertNotNull($found->getName());
    }

    public function testFindByAlpha2CodeWithNonExistentEnumShouldReturnNull(): void
    {
        // Fixtures 已经加载了所有国家，所以 ZW 应该存在
        // 我们使用一个不存在的枚举值来测试返回 null 的情况
        // 由于 Alpha2Code 是枚举，我们无法创建不存在的值，所以这个测试需要调整

        // 先删除 ZW 记录来测试不存在的情况
        $existing = $this->repository->findByCode('ZW');
        if (null !== $existing) {
            $this->repository->remove($existing, true);
        }

        // 现在搜索应该返回 null
        $result = $this->repository->findByAlpha2Code(Alpha2Code::ZW);
        $this->assertNull($result);
    }

    public function testFindAllValidShouldReturnOnlyValidCountries(): void
    {
        $validCountry = new Country();
        $validCountry->setCode('T6');
        $validCountry->setName('Valid Test Country');
        $validCountry->setValid(true);

        $invalidCountry = new Country();
        $invalidCountry->setCode('T7');
        $invalidCountry->setName('Invalid Test Country');
        $invalidCountry->setValid(false);

        $this->repository->save($validCountry);
        $this->repository->save($invalidCountry);

        $results = $this->repository->findAllValid();

        $this->assertIsArray($results);
        $validCodes = array_map(fn (Country $c) => $c->getCode(), $results);
        $this->assertContains('T6', $validCodes);
        $this->assertNotContains('T7', $validCodes);

        foreach ($results as $country) {
            $this->assertTrue($country->isValid());
        }
    }

    public function testSearchByNameShouldReturnMatchingCountries(): void
    {
        $country1 = new Country();
        $country1->setCode('T8');
        $country1->setName('Australia Test');
        $country1->setValid(true);

        $country2 = new Country();
        $country2->setCode('T9');
        $country2->setName('Austria Test');
        $country2->setValid(true);

        $country3 = new Country();
        $country3->setCode('U1');
        $country3->setName('Brazil Test');
        $country3->setValid(true);

        $this->repository->save($country1);
        $this->repository->save($country2);
        $this->repository->save($country3);

        $results = $this->repository->searchByName('Aust');

        $this->assertIsArray($results);
        $this->assertCount(2, $results);

        $names = array_map(fn (Country $c) => $c->getName(), $results);
        $this->assertContains('Australia Test', $names);
        $this->assertContains('Austria Test', $names);
        $this->assertNotContains('Brazil Test', $names);
    }

    public function testSearchByNameWithNonMatchingShouldReturnEmptyArray(): void
    {
        $results = $this->repository->searchByName('NonExistentCountry');
        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function testFindCountriesWithCurrenciesShouldReturnArray(): void
    {
        $results = $this->repository->findCountriesWithCurrencies();
        $this->assertIsArray($results);
    }

    public function testFlushShouldPersistPendingChanges(): void
    {
        $country = new Country();
        $country->setCode('U2');
        $country->setName('Test Flush Country');
        $country->setValid(true);

        $this->repository->save($country, false);
        $this->assertNull($country->getId());

        $this->repository->flush();
        $this->assertNotNull($country->getId());
    }

    public function testFindByWithNullFlagCode(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建有国旗代码的国家
        $country1 = new Country();
        $country1->setCode('NF1');
        $country1->setName('Country with Flag');
        $country1->setFlagCode('flag1');
        $country1->setValid(true);
        $this->repository->save($country1, false);

        // 创建没有国旗代码的国家
        $country2 = new Country();
        $country2->setCode('NF2');
        $country2->setName('Country without Flag');
        $country2->setFlagCode(null);
        $country2->setValid(true);
        $this->repository->save($country2, true);

        // 查找没有国旗代码的国家
        $noFlagCountries = $this->repository->findBy(['flagCode' => null]);

        $this->assertCount(1, $noFlagCountries);
        $this->assertSame('Country without Flag', $noFlagCountries[0]->getName());
        $this->assertNull($noFlagCountries[0]->getFlagCode());
    }

    public function testCountWithNullFlagCode(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建没有国旗代码的国家
        $country = new Country();
        $country->setCode('NFC');
        $country->setName('No Flag Code Country');
        $country->setFlagCode(null);
        $country->setValid(true);
        $this->repository->save($country, true);

        // 计算没有国旗代码的国家数量
        $noFlagCount = $this->repository->count(['flagCode' => null]);
        $this->assertSame(1, $noFlagCount);
    }

    public function testFindByWithCurrenciesRelation(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建国家
        $country = new Country();
        $country->setCode('CR1');
        $country->setName('Country with Currencies Relation');
        $country->setValid(true);
        $this->repository->save($country, true);

        // 验证关联集合初始化
        $this->assertInstanceOf(Collection::class, $country->getCurrencies());
        $this->assertCount(0, $country->getCurrencies());
    }

    public function testFindOneByWithOrderBy(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建多个测试国家，使用特定前缀
        $country1 = new Country();
        $country1->setCode('FOB1');
        $country1->setName('FindOneBy Country A');
        $country1->setValid(true);
        $this->repository->save($country1, false);

        $country2 = new Country();
        $country2->setCode('FOB2');
        $country2->setName('FindOneBy Country Z');
        $country2->setValid(true);
        $this->repository->save($country2, true);

        // 使用查询构建器按名称正序查找第一个
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.code LIKE :code')
            ->setParameter('code', 'FOB%')
            ->orderBy('c.name', 'ASC')
            ->setMaxResults(1)
        ;
        $firstCountry = $qb->getQuery()->getOneOrNullResult();
        $this->assertInstanceOf(Country::class, $firstCountry);
        $this->assertSame('FindOneBy Country A', $firstCountry->getName());

        // 使用查询构建器按名称倒序查找第一个
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.code LIKE :code')
            ->setParameter('code', 'FOB%')
            ->orderBy('c.name', 'DESC')
            ->setMaxResults(1)
        ;
        $lastCountry = $qb->getQuery()->getOneOrNullResult();
        $this->assertInstanceOf(Country::class, $lastCountry);
        $this->assertSame('FindOneBy Country Z', $lastCountry->getName());
    }

    /**
     * @return ServiceEntityRepository<Country>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $country = new Country();
        // 使用随机代码避免与 fixtures 中的数据冲突
        $country->setCode('T' . uniqid());
        $country->setName('Test Country ' . uniqid());
        $country->setValid(true);

        return $country;
    }
}
