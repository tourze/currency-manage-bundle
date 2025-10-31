<?php

namespace Tourze\CurrencyManageBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Entity\Currency;
use Tourze\CurrencyManageBundle\Repository\CountryRepository;
use Tourze\CurrencyManageBundle\Repository\CurrencyRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CurrencyRepository::class)]
#[RunTestsInSeparateProcesses]
final class CurrencyRepositoryTest extends AbstractRepositoryTestCase
{
    private CurrencyRepository $repository;

    private CountryRepository $countryRepository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(CurrencyRepository::class);
        $this->countryRepository = self::getService(CountryRepository::class);
    }

    public function testFindWithEmptyStringIdShouldReturnNull(): void
    {
        $result = $this->repository->find('');
        $this->assertNull($result);
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $currency = new Currency();
        $currency->setCode('TC3');
        $currency->setName('Test Currency 3');
        $currency->setSymbol('T3');

        $this->repository->save($currency, false);
        $this->assertNull($currency->getId());

        $this->repository->flush();
        $this->assertNotNull($currency->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $currency = new Currency();
        $currency->setCode('TC4');
        $currency->setName('Test Currency 4');
        $currency->setSymbol('T4');

        $this->repository->save($currency);
        $id = $currency->getId();
        $this->assertNotNull($id);

        $this->repository->remove($currency);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByCode(): void
    {
        $currency = new Currency();
        $currency->setCode('TC5');
        $currency->setName('Test Currency 5');
        $currency->setSymbol('T5');

        $this->repository->save($currency);

        $found = $this->repository->findByCode('TC5');
        $this->assertInstanceOf(Currency::class, $found);
        $this->assertSame('TC5', $found->getCode());
        $this->assertSame('Test Currency 5', $found->getName());
    }

    public function testFindByCodeWithNonExistentCode(): void
    {
        $result = $this->repository->findByCode('XXX');
        $this->assertNull($result);
    }

    public function testFlush(): void
    {
        $currency = new Currency();
        $currency->setCode('TC6');
        $currency->setName('Test Currency 6');
        $currency->setSymbol('T6');

        $this->repository->save($currency, false);
        $this->assertNull($currency->getId());

        $this->repository->flush();
        $this->assertNotNull($currency->getId());
    }

    public function testEntityRelationshipWithCountry(): void
    {
        $country = new Country();
        $country->setCode('XC');
        $country->setName('Test Country For Currency');
        $country->setValid(true);

        $this->countryRepository->save($country);

        $currency = new Currency();
        $currency->setCode('TC7');
        $currency->setName('Test Currency 7');
        $currency->setSymbol('T7');
        $currency->setCountry($country);

        $this->repository->save($currency);

        $found = $this->repository->find($currency->getId());
        $this->assertInstanceOf(Currency::class, $found);
        $this->assertInstanceOf(Country::class, $found->getCountry());
        $this->assertSame('XC', $found->getCountry()->getCode());
    }

    public function testFindByWithCountryRelation(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建国家
        $country = new Country();
        $country->setCode('FBR');
        $country->setName('Find By Relation Country');
        $country->setValid(true);
        $this->countryRepository->save($country, true);

        // 创建有国家关联的货币
        $currency1 = new Currency();
        $currency1->setCode('REL1');
        $currency1->setName('Related Currency');
        $currency1->setSymbol('R1');
        $currency1->setCountry($country);
        $this->repository->save($currency1, false);

        // 创建没有国家关联的货币
        $currency2 = new Currency();
        $currency2->setCode('REL2');
        $currency2->setName('Unrelated Currency');
        $currency2->setSymbol('R2');
        $currency2->setCountry(null);
        $this->repository->save($currency2, true);

        // 根据国家查找货币
        $countryCurrencies = $this->repository->findBy(['country' => $country]);
        $this->assertCount(1, $countryCurrencies);
        $this->assertSame('Related Currency', $countryCurrencies[0]->getName());
        $this->assertSame($country, $countryCurrencies[0]->getCountry());
    }

    public function testFindByWithNullCountry(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建没有国家关联的货币
        $currency = new Currency();
        $currency->setCode('NULL1');
        $currency->setName('No Country Currency');
        $currency->setSymbol('NC');
        $currency->setCountry(null);
        $this->repository->save($currency, true);

        // 使用查询构建器查找没有国家关联的货币
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.code = :code AND c.country IS NULL')
            ->setParameter('code', 'NULL1')
        ;
        $noCountryCurrencies = $qb->getQuery()->getResult();

        $this->assertIsArray($noCountryCurrencies);
        $this->assertCount(1, $noCountryCurrencies);
        $this->assertInstanceOf(Currency::class, $noCountryCurrencies[0]);
        $this->assertSame('No Country Currency', $noCountryCurrencies[0]->getName());
        $this->assertNull($noCountryCurrencies[0]->getCountry());
    }

    public function testCountWithNullCountry(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建没有国家关联的货币
        $currency = new Currency();
        $currency->setCode('NCC');
        $currency->setName('No Country Count Currency');
        $currency->setSymbol('NCC');
        $currency->setCountry(null);
        $this->repository->save($currency, true);

        // 使用查询构建器计算没有国家关联的货币数量
        $qb = $this->repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.code = :code AND c.country IS NULL')
            ->setParameter('code', 'NCC')
        ;
        $noCountryCount = (int) $qb->getQuery()->getSingleScalarResult();
        $this->assertSame(1, $noCountryCount);
    }

    public function testFindByWithNullRateToCny(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建有汇率的货币
        $currency1 = new Currency();
        $currency1->setCode('RATE1');
        $currency1->setName('Currency with Rate');
        $currency1->setSymbol('R1');
        $currency1->setRateToCny(7.2);
        $this->repository->save($currency1, false);

        // 创建没有汇率的货币
        $currency2 = new Currency();
        $currency2->setCode('RATE2');
        $currency2->setName('Currency without Rate');
        $currency2->setSymbol('R2');
        $currency2->setRateToCny(null);
        $this->repository->save($currency2, true);

        // 使用查询构建器查找没有汇率的货币
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.code = :code AND c.rateToCny IS NULL')
            ->setParameter('code', 'RATE2')
        ;
        $noRateCurrencies = $qb->getQuery()->getResult();

        $this->assertIsArray($noRateCurrencies);
        $this->assertCount(1, $noRateCurrencies);
        $this->assertInstanceOf(Currency::class, $noRateCurrencies[0]);
        $this->assertSame('Currency without Rate', $noRateCurrencies[0]->getName());
        $this->assertNull($noRateCurrencies[0]->getRateToCny());
    }

    public function testCountWithNullRateToCny(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建没有汇率的货币
        $currency = new Currency();
        $currency->setCode('NRC');
        $currency->setName('No Rate Currency');
        $currency->setSymbol('NRC');
        $currency->setRateToCny(null);
        $this->repository->save($currency, true);

        // 使用查询构建器计算没有汇率的货币数量
        $qb = $this->repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.code = :code AND c.rateToCny IS NULL')
            ->setParameter('code', 'NRC')
        ;
        $noRateCount = (int) $qb->getQuery()->getSingleScalarResult();
        $this->assertSame(1, $noRateCount);
    }

    public function testFindByWithNullRateUpdateDate(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建没有汇率更新时间的货币
        $currency = new Currency();
        $currency->setCode('NRU');
        $currency->setName('No Rate Update Currency');
        $currency->setSymbol('NRU');
        $this->repository->save($currency, false);

        // 手动设置为null，避免自动填充
        $currency->setRateUpdateDate(null);
        self::getEntityManager()->flush();

        // 使用查询构建器查找没有汇率更新时间的货币
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.code = :code AND c.rateUpdateDate IS NULL')
            ->setParameter('code', 'NRU')
        ;
        $noUpdateDateCurrencies = $qb->getQuery()->getResult();

        $this->assertIsArray($noUpdateDateCurrencies);
        $this->assertCount(1, $noUpdateDateCurrencies);
        $this->assertInstanceOf(Currency::class, $noUpdateDateCurrencies[0]);
        $this->assertSame('No Rate Update Currency', $noUpdateDateCurrencies[0]->getName());
        $this->assertNull($noUpdateDateCurrencies[0]->getRateUpdateDate());
    }

    public function testCountWithNullRateUpdateDate(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建没有汇率更新时间的货币
        $currency = new Currency();
        $currency->setCode('NRUC');
        $currency->setName('No Rate Update Count Currency');
        $currency->setSymbol('NRUC');
        $this->repository->save($currency, false);

        // 手动设置为null，避免自动填充
        $currency->setRateUpdateDate(null);
        self::getEntityManager()->flush();

        // 使用查询构建器计算没有汇率更新时间的货币数量
        $qb = $this->repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.code = :code AND c.rateUpdateDate IS NULL')
            ->setParameter('code', 'NRUC')
        ;
        $noUpdateDateCount = (int) $qb->getQuery()->getSingleScalarResult();
        $this->assertSame(1, $noUpdateDateCount);
    }

    public function testFindOneByWithOrderBy(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建多个相同类型的货币，使用特定前缀
        $currency1 = new Currency();
        $currency1->setCode('FOB1');
        $currency1->setName('FindOneBy Currency A');
        $currency1->setSymbol('A');
        $this->repository->save($currency1, false);

        $currency2 = new Currency();
        $currency2->setCode('FOB2');
        $currency2->setName('FindOneBy Currency Z');
        $currency2->setSymbol('Z');
        $this->repository->save($currency2, true);

        // 按名称正序查找第一个
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.code LIKE :code')
            ->setParameter('code', 'FOB%')
            ->orderBy('c.name', 'ASC')
            ->setMaxResults(1)
        ;
        $firstCurrency = $qb->getQuery()->getOneOrNullResult();
        $this->assertInstanceOf(Currency::class, $firstCurrency);
        $this->assertSame('FindOneBy Currency A', $firstCurrency->getName());

        // 按名称倒序查找第一个
        $qb = $this->repository->createQueryBuilder('c')
            ->where('c.code LIKE :code')
            ->setParameter('code', 'FOB%')
            ->orderBy('c.name', 'DESC')
            ->setMaxResults(1)
        ;
        $lastCurrency = $qb->getQuery()->getOneOrNullResult();
        $this->assertInstanceOf(Currency::class, $lastCurrency);
        $this->assertSame('FindOneBy Currency Z', $lastCurrency->getName());
    }

    public function testCountWithCountryRelation(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建国家
        $country = new Country();
        $country->setCode('CTB');
        $country->setName('Count By Association Country');
        $country->setValid(true);
        $this->countryRepository->save($country, true);

        // 创建有国家关联的货币
        $currency = new Currency();
        $currency->setCode('CREL');
        $currency->setName('Country Related Currency');
        $currency->setSymbol('CR');
        $currency->setCountry($country);
        $this->repository->save($currency, true);

        // 计算有国家关联的货币数量
        $countryRelatedCount = $this->repository->count(['country' => $country]);
        $this->assertSame(1, $countryRelatedCount);
    }

    public function testFindOneByAssociationCountryShouldReturnMatchingEntity(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建国家
        $country = new Country();
        $country->setCode('ASC');
        $country->setName('Association Country');
        $country->setValid(true);
        $this->countryRepository->save($country, true);

        // 创建有国家关联的货币
        $currency = new Currency();
        $currency->setCode('ASSOC');
        $currency->setName('Association Currency');
        $currency->setSymbol('AC');
        $currency->setCountry($country);
        $this->repository->save($currency, true);

        // 使用 findOneBy 通过关联查找货币
        $foundCurrency = $this->repository->findOneBy(['country' => $country]);

        $this->assertInstanceOf(Currency::class, $foundCurrency);
        $this->assertSame('Association Currency', $foundCurrency->getName());
        $this->assertSame($country, $foundCurrency->getCountry());
    }

    public function testCountByAssociationCountryShouldReturnCorrectNumber(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建国家
        $country = new Country();
        $country->setCode('AC');
        $country->setName('Association Count Country');
        $country->setValid(true);
        $this->countryRepository->save($country, true);

        // 创建 4 个属于该国家的货币
        for ($i = 1; $i <= 4; ++$i) {
            $currency = new Currency();
            $currency->setCode(sprintf('AC%d', $i));
            $currency->setName(sprintf('Country Currency %d', $i));
            $currency->setSymbol(sprintf('AC%d', $i));
            $currency->setCountry($country);
            $this->repository->save($currency, false);
        }

        // 创建 2 个属于其他国家的货币
        $otherCountry = new Country();
        $otherCountry->setCode('OT');
        $otherCountry->setName('Other Country');
        $otherCountry->setValid(true);
        $this->countryRepository->save($otherCountry, false);

        for ($i = 1; $i <= 2; ++$i) {
            $currency = new Currency();
            $currency->setCode(sprintf('OT%d', $i));
            $currency->setName(sprintf('Other Currency %d', $i));
            $currency->setSymbol(sprintf('OT%d', $i));
            $currency->setCountry($otherCountry);
            $this->repository->save($currency, false);
        }
        $this->repository->flush();

        // 计算属于指定国家的货币数量
        $count = $this->repository->count(['country' => $country]);
        $this->assertSame(4, $count);
    }

    /**
     * @return ServiceEntityRepository<Currency>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $currency = new Currency();
        $currency->setCode('TEST');
        $currency->setName('Test Currency');
        $currency->setSymbol('TC');

        return $currency;
    }
}
