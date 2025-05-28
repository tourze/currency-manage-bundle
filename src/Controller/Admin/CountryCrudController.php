<?php

namespace Tourze\CurrencyManageBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\CurrencyManageBundle\Entity\Country;

/**
 * 国家管理控制器
 */
class CountryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Country::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('国家')
            ->setEntityLabelInPlural('国家管理')
            ->setPageTitle('index', '国家列表')
            ->setPageTitle('new', '新增国家')
            ->setPageTitle('edit', '编辑国家')
            ->setPageTitle('detail', '国家详情')
            ->setHelp('index', '管理系统中的国家信息，基于 ISO 3166-1 alpha-2 标准')
            ->setDefaultSort(['name' => 'ASC'])
            ->setSearchFields(['name', 'code', 'flagCode'])
            ->setPaginatorPageSize(50);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm();

        yield TextField::new('code', '国家代码')
            ->setRequired(true)
            ->setMaxLength(2)
            ->setHelp('ISO 3166-1 alpha-2 标准国家代码，如：CN、US、JP等');

        yield TextField::new('name', '国家名称')
            ->setRequired(true)
            ->setHelp('国家的中文名称，如：中国、美国、日本等');

        yield TextField::new('flagCode', '国旗代码')
            ->setHelp('国旗图标代码，通常与国家代码相同（小写），如：cn、us、jp等');

        yield BooleanField::new('valid', '是否有效')
            ->setHelp('标记该国家是否在系统中有效使用');

        yield DateTimeField::new('createTime', '创建时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
            ->hideOnForm();

        yield DateTimeField::new('updateTime', '更新时间')
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
            ->add(TextFilter::new('name', '国家名称'))
            ->add(TextFilter::new('code', '国家代码'))
            ->add(TextFilter::new('flagCode', '国旗代码'))
            ->add(BooleanFilter::new('valid', '是否有效'));
    }
}
