<?php

namespace App\Controller\Admin;

use App\Entity\Popup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PopupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Popup::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
//            ->overrideTemplate('crud/index', 'admin/slide/index.html.twig')
            ->overrideTemplate('crud/new', 'admin/popup/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/popup/edit.html.twig')
//            ->overrideTemplate('crud/field/image', 'admin/slide/field/image.html.twig')
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig','admin/popup/form.html.twig'])
            ;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            ImageField::new('imageFile', 'Image(428x420)')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('fileName')->setCustomOption('basePath', 'media/images/popup/')->onlyOnIndex(),
            BooleanField::new('isShow'),
        ];
    }

}
