<?php

declare(strict_types=1);

namespace Tourze\CurrencyManageBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\CurrencyManageBundle\Entity\CurrencyRateHistory;

/**
 * 历史汇率管理控制器
 *
 * @extends AbstractCrudController<CurrencyRateHistory>
 */
#[AdminCrud(routePath: '/currency/rate-history', routeName: 'currency_rate_history')]
#[Autoconfigure(public: true)]
final class CurrencyRateHistoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CurrencyRateHistory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('历史汇率')
            ->setEntityLabelInPlural('历史汇率管理')
            ->setPageTitle('index', '历史汇率列表')
            ->setPageTitle('detail', '历史汇率详情')
            ->setHelp('index', '查看和管理货币汇率的历史记录，支持按货币代码和日期查询')
            ->setDefaultSort(['rateDate' => 'DESC', 'currencyCode' => 'ASC'])
            ->setSearchFields(['currencyCode', 'currencyName', 'currencySymbol'])
            ->setPaginatorPageSize(50)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('currencyCode', '货币代码')
            ->setHelp('国际标准货币代码，如：CNY、USD、EUR等')
        ;

        yield TextField::new('currencyName', '货币名称')
            ->setHelp('货币的中文名称，如：人民币、美元等')
        ;

        yield TextField::new('currencySymbol', '货币符号')
            ->setHelp('货币符号标识，如：¥、$、€等')
        ;

        yield TextField::new('flag', '国旗代码')
            ->setHelp('国旗代码，如：cn、us、eu等')
        ;

        yield NumberField::new('rateToCny', '对人民币汇率')
            ->setNumDecimals(6)
            ->setHelp('该货币兑换人民币的汇率')
        ;

        yield DateField::new('rateDate', '汇率日期')
            ->setFormat('yyyy-MM-dd')
            ->setHelp('汇率记录的日期')
        ;

        yield DateTimeField::new('createTime', '记录创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm()
            ->setHelp('历史记录的创建时间')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW)
            ->disable(Action::EDIT)
            ->disable(Action::DELETE)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('currencyCode', '货币代码'))
            ->add(TextFilter::new('currencyName', '货币名称'))
            ->add(TextFilter::new('currencySymbol', '货币符号'))
            ->add(TextFilter::new('flag', '国旗代码'))
            ->add(NumericFilter::new('rateToCny', '对人民币汇率'))
            ->add(DateTimeFilter::new('rateDate', '汇率日期'))
            ->add(DateTimeFilter::new('createTime', '记录创建时间'))
        ;
    }
}
