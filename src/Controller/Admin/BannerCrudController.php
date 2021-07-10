<?php

namespace App\Controller\Admin;

use App\Entity\Banner;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BannerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Banner::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
//            ->overrideTemplate('crud/index', 'admin/slide/index.html.twig')
            ->overrideTemplate('crud/new', 'admin/banner/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/banner/edit.html.twig')
//            ->overrideTemplate('crud/field/image', 'admin/banner/field/image.html.twig')
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig','admin/banner/form.html.twig'])
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('page'),
            ImageField::new('imageFile', 'Image(1920x230)')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('fileName')->setCustomOption('basePath', 'media/images/banner/')->onlyOnIndex(),
        ];
    }

}
