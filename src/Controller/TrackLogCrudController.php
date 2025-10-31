<?php

declare(strict_types=1);

namespace Tourze\UserTrackBundle\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Tourze\UserTrackBundle\Entity\TrackLog;

/**
 * 用户轨迹日志管理控制器
 */
#[AdminCrud(routePath: '/user-track/track-log', routeName: 'user_track_track_log')]
final class TrackLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TrackLog::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),

            TextField::new('userId', '用户ID')
                ->setRequired(false)
                ->setHelp('记录轨迹的用户ID')
                ->setMaxLength(120),

            TextField::new('event', '事件名称')
                ->setRequired(true)
                ->setHelp('轨迹事件的名称标识')
                ->setMaxLength(191),

            ArrayField::new('params', '事件参数')
                ->setRequired(false)
                ->setHelp('事件相关的参数数据，JSON格式'),

            AssociationField::new('reporter', '上报者')
                ->setRequired(false)
                ->setHelp('上报该轨迹的用户')
                ->hideOnIndex(),

            TextField::new('createdFromIp', '来源IP')
                ->setRequired(false)
                ->setHelp('记录轨迹时的来源IP地址')
                ->hideOnForm(),

            DateTimeField::new('createTime', '创建时间')
                ->hideOnForm()
                ->setFormat('yyyy-MM-dd HH:mm:ss'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('event')
            ->add('userId')
            ->add('createTime')
            ->add('reporter')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('轨迹日志')
            ->setEntityLabelInPlural('轨迹日志')
            ->setSearchFields(['event', 'userId', 'createdFromIp'])
            ->setDefaultSort(['createTime' => 'DESC'])
        ;
    }
}
