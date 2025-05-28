<?php

namespace Tourze\CurrencyManageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;

/**
 * @extends ServiceEntityRepository<CurrencyRateHistory>
 *
 * @method CurrencyRateHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CurrencyRateHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CurrencyRateHistory[]    findAll()
 * @method CurrencyRateHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRateHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CurrencyRateHistory::class);
    }

    /**
     * 保存历史汇率记录
     */
    public function save(CurrencyRateHistory $history, bool $flush = false): void
    {
        $this->getEntityManager()->persist($history);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除历史汇率记录
     */
    public function remove(CurrencyRateHistory $history, bool $flush = false): void
    {
        $this->getEntityManager()->remove($history);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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
     */
    public function findByCurrencyCode(string $currencyCode, ?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('h')
            ->where('h.currencyCode = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->orderBy('h.rateDate', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * 根据日期范围查找历史汇率
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate, ?string $currencyCode = null): array
    {
        $qb = $this->createQueryBuilder('h')
            ->where('h.rateDate >= :startDate')
            ->andWhere('h.rateDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('h.rateDate', 'DESC')
            ->addOrderBy('h.currencyCode', 'ASC');

        if ($currencyCode !== null) {
            $qb->andWhere('h.currencyCode = :currencyCode')
                ->setParameter('currencyCode', $currencyCode);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * 获取指定货币的最新历史汇率
     */
    public function findLatestByCurrency(string $currencyCode): ?CurrencyRateHistory
    {
        return $this->createQueryBuilder('h')
            ->where('h.currencyCode = :currencyCode')
            ->setParameter('currencyCode', $currencyCode)
            ->orderBy('h.rateDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * 获取所有货币在指定日期的汇率
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
        return $this->createQueryBuilder('h')
            ->delete()
            ->where('h.rateDate < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->execute();
    }

    /**
     * 获取历史汇率统计信息
     */
    public function getStatistics(): array
    {
        $qb = $this->createQueryBuilder('h');
        
        $result = $qb->select([
                'COUNT(h.id) as totalRecords',
                'COUNT(DISTINCT h.currencyCode) as totalCurrencies',
                'MIN(h.rateDate) as earliestDate',
                'MAX(h.rateDate) as latestDate'
            ])
            ->getQuery()
            ->getSingleResult();

        return [
            'totalRecords' => (int) $result['totalRecords'],
            'totalCurrencies' => (int) $result['totalCurrencies'],
            'earliestDate' => $result['earliestDate'],
            'latestDate' => $result['latestDate'],
        ];
    }

    /**
     * 刷新所有待处理的更改
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
} 