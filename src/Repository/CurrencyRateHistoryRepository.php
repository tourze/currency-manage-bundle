<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<CurrencyRateHistory>
 */
#[AsRepository(entityClass: CurrencyRateHistory::class)]
class CurrencyRateHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRateHistory::class);
    }

    /**
     * 根据货币代码和日期查找历史汇率
     */
    public function findByCurrencyAndDate(string $currencyCode, \DateTimeInterface $date): ?CurrencyRateHistory
    {
        return $this->findOneBy([
            'currencyCode' => $currencyCode,
            'rateDate' => $date,
        ]);
    }

    /**
     * 根据货币代码查找历史汇率（按日期倒序）
     *
     * @return list<CurrencyRateHistory>
     */
    public function findByCurrencyCode(string $currencyCode, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('h')
            ->where('h.currencyCode = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->orderBy('h.rateDate', 'DESC')
        ;

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        /** @var list<CurrencyRateHistory> */
        return $qb->getQuery()->getResult();
    }

    /**
     * 根据日期范围查找历史汇率
     *
     * @return list<CurrencyRateHistory>
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?string $currencyCode = null): array
    {
        $qb = $this->createQueryBuilder('h')
            ->where('h.rateDate >= :startDate')
            ->andWhere('h.rateDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('h.rateDate', 'DESC')
            ->addOrderBy('h.currencyCode', 'ASC')
        ;

        if (null !== $currencyCode) {
            $qb->andWhere('h.currencyCode = :currencyCode')
                ->setParameter('currencyCode', $currencyCode)
            ;
        }

        /** @var list<CurrencyRateHistory> */
        return $qb->getQuery()->getResult();
    }

    /**
     * 获取指定货币的最新历史汇率
     */
    public function findLatestByCurrency(string $currencyCode): ?CurrencyRateHistory
    {
        $result = $this->createQueryBuilder('h')
            ->where('h.currencyCode = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->orderBy('h.rateDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result instanceof CurrencyRateHistory ? $result : null;
    }

    /**
     * 获取所有货币在指定日期的汇率
     *
     * @return CurrencyRateHistory[]
     */
    public function findAllByDate(\DateTimeInterface $date): array
    {
        return $this->findBy(['rateDate' => $date], ['currencyCode' => 'ASC']);
    }

    /**
     * 删除指定日期之前的历史记录（用于数据清理）
     */
    public function deleteBeforeDate(\DateTimeInterface $date): int
    {
        $result = $this->createQueryBuilder('h')
            ->delete()
            ->where('h.rateDate < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute()
        ;

        return \is_int($result) ? $result : 0;
    }

    /**
     * 获取历史汇率统计信息
     *
     * @return array{totalRecords: int, totalCurrencies: int, earliestDate: \DateTimeInterface|null, latestDate: \DateTimeInterface|null}
     */
    public function getStatistics(): array
    {
        $qb = $this->createQueryBuilder('h');

        /** @var array{totalRecords: string|int, totalCurrencies: string|int, earliestDate: \DateTimeInterface|null, latestDate: \DateTimeInterface|null} $result */
        $result = $qb->select([
            'COUNT(h.id) as totalRecords',
            'COUNT(DISTINCT h.currencyCode) as totalCurrencies',
            'MIN(h.rateDate) as earliestDate',
            'MAX(h.rateDate) as latestDate',
        ])
            ->getQuery()
            ->getSingleResult()
        ;

        return [
            'totalRecords' => (int) $result['totalRecords'],
            'totalCurrencies' => (int) $result['totalCurrencies'],
            'earliestDate' => $result['earliestDate'],
            'latestDate' => $result['latestDate'],
        ];
    }

    /**
     * 保存实体
     */
    public function save(CurrencyRateHistory $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除实体
     */
    public function remove(CurrencyRateHistory $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 刷新所有待处理的更改
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
