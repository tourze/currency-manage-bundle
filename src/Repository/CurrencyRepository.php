<?php

namespace Tourze\CurrencyManageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\CurrencyManageBundle\Entity\Currency;

/**
 * @extends ServiceEntityRepository<Currency>
 *
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    /**
     * 保存货币
     */
    public function save(Currency $currency, bool $flush = false): void
    {
        $this->getEntityManager()->persist($currency);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除货币
     */
    public function remove(Currency $currency, bool $flush = false): void
    {
        $this->getEntityManager()->remove($currency);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * 根据货币代码查找货币
     */
    public function findByCode(string $code): ?Currency
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * 刷新所有待处理的更改
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
