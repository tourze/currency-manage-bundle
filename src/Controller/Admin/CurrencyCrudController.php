<?php

namespace Tourze\CurrencyManageBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\CurrencyManageBundle\Entity\Country;
use Tourze\CurrencyManageBundle\Entity\Currency;

/**
 * 货币管理控制器
 */
class CurrencyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Currency::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('货币')
            ->setEntityLabelInPlural('货币管理')
            ->setPageTitle('index', '货币列表')
            ->setPageTitle('new', '新增货币')
            ->setPageTitle('edit', '编辑货币')
            ->setPageTitle('detail', '货币详情')
            ->setHelp('index', '管理系统中的货币信息，包括货币代码、名称、符号和汇率等')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'code', 'symbol']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm();

        yield TextField::new('name', '货币名称')
            ->setRequired(true)
            ->setHelp('货币的中文名称，如：人民币、美元等');

        yield TextField::new('code', '货币代码')
            ->setRequired(true)
            ->setHelp('国际标准货币代码，如：CNY、USD、EUR等');

        yield TextField::new('symbol', '货币符号')
            ->setRequired(true)
            ->setHelp('货币符号标识，如：¥、$、€等');

        yield AssociationField::new('country', '所属国家')
            ->setHelp('该货币所属的国家或地区')
            ->autocomplete()
            ->formatValue(function ($value, $entity) {
                if ($value instanceof Country) {
                    return $value->getName() . ' [' . $value->getCode() . ']';
                }
                return $value;
            });

        yield NumberField::new('rateToCny', '对人民币汇率')
            ->setNumDecimals(6)
            ->setHelp('该货币兑换人民币的汇率，如1美元=7.2人民币则填写7.2');

        yield DateTimeField::new('updateTime', '汇率更新时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '货币名称'))
            ->add(TextFilter::new('code', '货币代码'))
            ->add(TextFilter::new('symbol', '货币符号'))
            ->add(EntityFilter::new('country', '所属国家'))
            ->add(NumericFilter::new('rateToCny', '对人民币汇率'))
            ->add(DateTimeFilter::new('updateTime', '汇率更新时间'));
    }
}
