<?php

namespace Tourze\CurrencyManageBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\CurrencyManageBundle\Repository\CurrencyRateHistoryRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(CurrencyRateHistoryRepository::class)]
#[RunTestsInSeparateProcesses]
final class CurrencyRateHistoryRepositoryTest extends AbstractRepositoryTestCase
{
    private CurrencyRateHistoryRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(CurrencyRateHistoryRepository::class);
    }

    public function testFindWithEmptyStringIdShouldReturnNull(): void
    {
        $result = $this->repository->find('');
        $this->assertNull($result);
    }

    public function testSaveWithoutFlushShouldNotPersistImmediately(): void
    {
        $rateHistory = new CurrencyRateHistory();
        $rateHistory->setCurrencyCode('GBP');
        $rateHistory->setCurrencyName('British Pound');
        $rateHistory->setCurrencySymbol('£');
        $rateHistory->setRateDate(new \DateTimeImmutable('2024-01-03'));
        $rateHistory->setRateToCny(9.1000);

        $this->repository->save($rateHistory, false);
        $this->assertNull($rateHistory->getId());

        $this->repository->flush();
        $this->assertNotNull($rateHistory->getId());
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $rateHistory = new CurrencyRateHistory();
        $rateHistory->setCurrencyCode('JPY');
        $rateHistory->setCurrencyName('Japanese Yen');
        $rateHistory->setCurrencySymbol('¥');
        $rateHistory->setRateDate(new \DateTimeImmutable('2024-01-04'));
        $rateHistory->setRateToCny(0.0480);

        $this->repository->save($rateHistory);
        $id = $rateHistory->getId();
        $this->assertNotNull($id);

        $this->repository->remove($rateHistory);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByCurrencyAndDateShouldReturnEntity(): void
    {
        $date = new \DateTimeImmutable('2024-01-05');
        $rateHistory = new CurrencyRateHistory();
        $rateHistory->setCurrencyCode('CAD');
        $rateHistory->setCurrencyName('Canadian Dollar');
        $rateHistory->setCurrencySymbol('C$');
        $rateHistory->setRateDate($date);
        $rateHistory->setRateToCny(5.3000);

        $this->repository->save($rateHistory);

        $found = $this->repository->findByCurrencyAndDate('CAD', $date);
        $this->assertInstanceOf(CurrencyRateHistory::class, $found);
        $this->assertSame('CAD', $found->getCurrencyCode());
        $this->assertEquals($date, $found->getRateDate());
    }

    public function testFindByCurrencyAndDateWithNonExistentShouldReturnNull(): void
    {
        $result = $this->repository->findByCurrencyAndDate('XXX', new \DateTimeImmutable('2024-01-01'));
        $this->assertNull($result);
    }

    public function testFindByCurrencyCodeShouldReturnArray(): void
    {
        $rateHistory1 = new CurrencyRateHistory();
        $rateHistory1->setCurrencyCode('AUD');
        $rateHistory1->setCurrencyName('Australian Dollar');
        $rateHistory1->setCurrencySymbol('A$');
        $rateHistory1->setRateDate(new \DateTimeImmutable('2024-01-06'));
        $rateHistory1->setRateToCny(4.7000);

        $rateHistory2 = new CurrencyRateHistory();
        $rateHistory2->setCurrencyCode('AUD');
        $rateHistory2->setCurrencyName('Australian Dollar');
        $rateHistory2->setCurrencySymbol('A$');
        $rateHistory2->setRateDate(new \DateTimeImmutable('2024-01-07'));
        $rateHistory2->setRateToCny(4.8000);

        $this->repository->save($rateHistory1);
        $this->repository->save($rateHistory2);

        $results = $this->repository->findByCurrencyCode('AUD');
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(2, count($results));

        foreach ($results as $history) {
            $this->assertInstanceOf(CurrencyRateHistory::class, $history);
            $this->assertSame('AUD', $history->getCurrencyCode());
        }
    }

    public function testFindByCurrencyCodeWithLimitShouldRespectLimit(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $rateHistory = new CurrencyRateHistory();
            $rateHistory->setCurrencyCode('CHF');
            $rateHistory->setCurrencyName('Swiss Franc');
            $rateHistory->setCurrencySymbol('CHF');
            $rateHistory->setRateDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
            $rateHistory->setRateToCny(7.9000 + $i * 0.01);

            $this->repository->save($rateHistory);
        }

        $results = $this->repository->findByCurrencyCode('CHF', 3);
        $this->assertIsArray($results);
        $this->assertCount(3, $results);
    }

    public function testFindByDateRangeShouldReturnEntitiesInRange(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-10');
        $endDate = new \DateTimeImmutable('2024-01-12');

        $rateHistory1 = new CurrencyRateHistory();
        $rateHistory1->setCurrencyCode('SEK');
        $rateHistory1->setCurrencyName('Swedish Krona');
        $rateHistory1->setCurrencySymbol('kr');
        $rateHistory1->setRateDate(new \DateTimeImmutable('2024-01-09'));  // Before range
        $rateHistory1->setRateToCny(0.6800);

        $rateHistory2 = new CurrencyRateHistory();
        $rateHistory2->setCurrencyCode('SEK');
        $rateHistory2->setCurrencyName('Swedish Krona');
        $rateHistory2->setCurrencySymbol('kr');
        $rateHistory2->setRateDate(new \DateTimeImmutable('2024-01-11'));  // In range
        $rateHistory2->setRateToCny(0.6900);

        $rateHistory3 = new CurrencyRateHistory();
        $rateHistory3->setCurrencyCode('SEK');
        $rateHistory3->setCurrencyName('Swedish Krona');
        $rateHistory3->setCurrencySymbol('kr');
        $rateHistory3->setRateDate(new \DateTimeImmutable('2024-01-13'));  // After range
        $rateHistory3->setRateToCny(0.7000);

        $this->repository->save($rateHistory1);
        $this->repository->save($rateHistory2);
        $this->repository->save($rateHistory3);

        $results = $this->repository->findByDateRange($startDate, $endDate);
        $this->assertIsArray($results);

        foreach ($results as $history) {
            $this->assertGreaterThanOrEqual($startDate, $history->getRateDate());
            $this->assertLessThanOrEqual($endDate, $history->getRateDate());
        }
    }

    public function testFindByDateRangeWithCurrencyCodeShouldFilterByCurrency(): void
    {
        $startDate = new \DateTimeImmutable('2024-01-15');
        $endDate = new \DateTimeImmutable('2024-01-17');

        $rateHistory1 = new CurrencyRateHistory();
        $rateHistory1->setCurrencyCode('NOK');
        $rateHistory1->setCurrencyName('Norwegian Krone');
        $rateHistory1->setCurrencySymbol('kr');
        $rateHistory1->setRateDate(new \DateTimeImmutable('2024-01-16'));
        $rateHistory1->setRateToCny(0.6700);

        $rateHistory2 = new CurrencyRateHistory();
        $rateHistory2->setCurrencyCode('DKK');
        $rateHistory2->setCurrencyName('Danish Krone');
        $rateHistory2->setCurrencySymbol('kr');
        $rateHistory2->setRateDate(new \DateTimeImmutable('2024-01-16'));
        $rateHistory2->setRateToCny(1.0200);

        $this->repository->save($rateHistory1);
        $this->repository->save($rateHistory2);

        $results = $this->repository->findByDateRange($startDate, $endDate, 'NOK');
        $this->assertIsArray($results);

        foreach ($results as $history) {
            $this->assertSame('NOK', $history->getCurrencyCode());
        }
    }

    public function testFindLatestByCurrencyShouldReturnMostRecent(): void
    {
        $rateHistory = new CurrencyRateHistory();
        $rateHistory->setCurrencyCode('PLN');
        $rateHistory->setCurrencyName('Polish Zloty');
        $rateHistory->setCurrencySymbol('zł');
        $rateHistory->setRateDate(new \DateTimeImmutable('2024-01-20'));
        $rateHistory->setRateToCny(1.6800);

        $this->repository->save($rateHistory);

        $latest = $this->repository->findLatestByCurrency('PLN');
        $this->assertInstanceOf(CurrencyRateHistory::class, $latest);
        $this->assertSame('PLN', $latest->getCurrencyCode());
        $this->assertNotNull($latest->getId());
    }

    public function testFindLatestByCurrencyWithNonExistentShouldReturnNull(): void
    {
        $result = $this->repository->findLatestByCurrency('XXX');
        $this->assertNull($result);
    }

    public function testFindAllByDateShouldReturnEntitiesForDate(): void
    {
        $date = new \DateTimeImmutable('2024-01-25');

        $rateHistory1 = new CurrencyRateHistory();
        $rateHistory1->setCurrencyCode('HUF');
        $rateHistory1->setCurrencyName('Hungarian Forint');
        $rateHistory1->setCurrencySymbol('Ft');
        $rateHistory1->setRateDate($date);
        $rateHistory1->setRateToCny(0.0200);

        $rateHistory2 = new CurrencyRateHistory();
        $rateHistory2->setCurrencyCode('CZK');
        $rateHistory2->setCurrencyName('Czech Koruna');
        $rateHistory2->setCurrencySymbol('Kč');
        $rateHistory2->setRateDate($date);
        $rateHistory2->setRateToCny(0.3200);

        $this->repository->save($rateHistory1);
        $this->repository->save($rateHistory2);

        $results = $this->repository->findAllByDate($date);
        $this->assertIsArray($results);

        foreach ($results as $history) {
            $this->assertEquals($date, $history->getRateDate());
        }
    }

    public function testDeleteBeforeDateShouldRemoveOldRecords(): void
    {
        $cutoffDate = new \DateTimeImmutable('2024-01-30');

        $oldRecord = new CurrencyRateHistory();
        $oldRecord->setCurrencyCode('RON');
        $oldRecord->setCurrencyName('Romanian Leu');
        $oldRecord->setCurrencySymbol('lei');
        $oldRecord->setRateDate(new \DateTimeImmutable('2024-01-29'));  // Before cutoff
        $oldRecord->setRateToCny(1.5600);

        $newRecord = new CurrencyRateHistory();
        $newRecord->setCurrencyCode('RON');
        $newRecord->setCurrencyName('Romanian Leu');
        $newRecord->setCurrencySymbol('lei');
        $newRecord->setRateDate(new \DateTimeImmutable('2024-01-31'));  // After cutoff
        $newRecord->setRateToCny(1.5500);

        $this->repository->save($oldRecord);
        $this->repository->save($newRecord);

        $deletedCount = $this->repository->deleteBeforeDate($cutoffDate);
        $this->assertIsInt($deletedCount);
        $this->assertGreaterThanOrEqual(0, $deletedCount);

        // Clear entity manager to force fresh queries
        self::getEntityManager()->clear();

        // Verify new record still exists
        $this->assertNotNull($this->repository->find($newRecord->getId()));
    }

    public function testGetStatisticsShouldReturnValidData(): void
    {
        $statistics = $this->repository->getStatistics();

        $this->assertIsArray($statistics);
        $this->assertArrayHasKey('totalRecords', $statistics);
        $this->assertArrayHasKey('totalCurrencies', $statistics);
        $this->assertArrayHasKey('earliestDate', $statistics);
        $this->assertArrayHasKey('latestDate', $statistics);

        $this->assertIsInt($statistics['totalRecords']);
        $this->assertIsInt($statistics['totalCurrencies']);
    }

    public function testFlushShouldPersistPendingChanges(): void
    {
        $rateHistory = new CurrencyRateHistory();
        $rateHistory->setCurrencyCode('BGN');
        $rateHistory->setCurrencyName('Bulgarian Lev');
        $rateHistory->setCurrencySymbol('лв');
        $rateHistory->setRateDate(new \DateTimeImmutable('2024-02-01'));
        $rateHistory->setRateToCny(4.0000);

        $this->repository->save($rateHistory, false);
        $this->assertNull($rateHistory->getId());

        $this->repository->flush();
        $this->assertNotNull($rateHistory->getId());
    }

    public function testFindByWithNullFlag(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建有国旗代码的记录
        $history1 = new CurrencyRateHistory();
        $history1->setCurrencyCode('FLAG1');
        $history1->setCurrencyName('Currency with Flag');
        $history1->setCurrencySymbol('F1');
        $history1->setRateDate(new \DateTimeImmutable('2024-01-01'));
        $history1->setRateToCny(7.1000);
        $history1->setFlag('us');
        $this->repository->save($history1, false);

        // 创建没有国旗代码的记录
        $history2 = new CurrencyRateHistory();
        $history2->setCurrencyCode('FLAG2');
        $history2->setCurrencyName('Currency without Flag');
        $history2->setCurrencySymbol('F2');
        $history2->setRateDate(new \DateTimeImmutable('2024-01-02'));
        $history2->setRateToCny(7.2000);
        $history2->setFlag(null);
        $this->repository->save($history2, true);

        // 使用查询构建器查找没有国旗代码的记录
        $qb = $this->repository->createQueryBuilder('h')
            ->where('h.currencyCode = :code AND h.flag IS NULL')
            ->setParameter('code', 'FLAG2')
        ;
        $noFlagHistories = $qb->getQuery()->getResult();

        $this->assertIsArray($noFlagHistories);
        $this->assertCount(1, $noFlagHistories);
        $this->assertInstanceOf(CurrencyRateHistory::class, $noFlagHistories[0]);
        $this->assertSame('Currency without Flag', $noFlagHistories[0]->getCurrencyName());
        $this->assertNull($noFlagHistories[0]->getFlag());
    }

    public function testCountWithNullFlag(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建没有国旗代码的记录
        $history = new CurrencyRateHistory();
        $history->setCurrencyCode('NFLAG');
        $history->setCurrencyName('No Flag Currency');
        $history->setCurrencySymbol('NF');
        $history->setRateDate(new \DateTimeImmutable('2024-01-01'));
        $history->setRateToCny(7.0000);
        $history->setFlag(null);
        $this->repository->save($history, true);

        // 使用查询构建器计算没有国旗代码的记录数量
        $qb = $this->repository->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.currencyCode = :code AND h.flag IS NULL')
            ->setParameter('code', 'NFLAG')
        ;
        $noFlagCount = (int) $qb->getQuery()->getSingleScalarResult();
        $this->assertSame(1, $noFlagCount);
    }

    public function testFindOneByWithOrderBy(): void
    {
        // 清理数据库
        self::cleanDatabase();

        // 创建多个相同货币的记录
        $history1 = new CurrencyRateHistory();
        $history1->setCurrencyCode('ORDER');
        $history1->setCurrencyName('Order Currency A');
        $history1->setCurrencySymbol('OA');
        $history1->setRateDate(new \DateTimeImmutable('2024-01-01'));
        $history1->setRateToCny(7.1000);
        $this->repository->save($history1, false);

        $history2 = new CurrencyRateHistory();
        $history2->setCurrencyCode('ORDER');
        $history2->setCurrencyName('Order Currency Z');
        $history2->setCurrencySymbol('OZ');
        $history2->setRateDate(new \DateTimeImmutable('2024-01-02'));
        $history2->setRateToCny(7.2000);
        $this->repository->save($history2, true);

        // 按名称正序查找第一个
        $firstHistory = $this->repository->findOneBy(['currencyCode' => 'ORDER'], ['currencyName' => 'ASC']);
        $this->assertSame('Order Currency A', $firstHistory?->getCurrencyName());

        // 按名称倒序查找第一个
        $lastHistory = $this->repository->findOneBy(['currencyCode' => 'ORDER'], ['currencyName' => 'DESC']);
        $this->assertSame('Order Currency Z', $lastHistory?->getCurrencyName());
    }

    /**
     * @return ServiceEntityRepository<CurrencyRateHistory>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $rateHistory = new CurrencyRateHistory();
        $rateHistory->setCurrencyCode('TEST');
        $rateHistory->setCurrencyName('Test Currency');
        $rateHistory->setCurrencySymbol('TC');
        $rateHistory->setRateDate(new \DateTimeImmutable('2024-01-01'));
        $rateHistory->setRateToCny(7.0000);

        return $rateHistory;
    }
}
