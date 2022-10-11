<?php

namespace App\Controller\Admin;

use App\Entity\FabricType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FabricTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FabricType::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
//            ->overrideTemplate('crud/action', 'admin/order/action.html.twig')
            ->setPaginatorPageSize(1000)
            ;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            TextField::new('nameAr'),
        ];
    }

}
