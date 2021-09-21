<?php

namespace App\Controller\Admin;

use App\Entity\PrivacyPolicy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PrivacyPolicyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PrivacyPolicy::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
//            ->overrideTemplate('crud/index', 'admin/slide/index.html.twig')
            ->overrideTemplate('crud/new', 'admin/term/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/term/edit.html.twig')
//            ->overrideTemplate('crud/field/image', 'admin/slide/field/image.html.twig')
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig','admin/term/form.html.twig'])
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
            TextEditorField::new('description', 'description')->onlyOnForms(),
            TextEditorField::new('descriptionAr', 'الوصف')->onlyOnForms()->addCssClass('text-right'),
            ImageField::new('imageFile')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('fileName')->setCustomOption('basePath', 'media/images/about/')->onlyOnIndex(),
        ];
    }
}
