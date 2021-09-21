<?php

namespace App\Controller\Admin;

use App\Admin\Field\TagField;
use App\Entity\Product;
use App\Form\CatalogType;
use App\Form\ImageFileType;
use App\Form\TogType;
use App\Form\TagType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/index', 'admin/product/index2.html.twig')
            ->overrideTemplate('crud/new', 'admin/product/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/product/edit.html.twig')
//            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig','admin/product/form.html.twig'])
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig','admin/product/form_theme.html.twig'])
            ->setPaginatorPageSize(1000)
            ->overrideTemplates([
                'crud/field/collection' => 'admin/product/collection.html.twig'
            ])
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            TextField::new('nameAr', 'الاسم')->addCssClass('text-right'),
//            TextField::new('slug'),
            SlugField::new('slug')->setTargetFieldName('name'),
            MoneyField::new('price')->setCurrency('KWD'),
            MoneyField::new('discountPrice')->setCurrency('KWD'),
            TextareaField::new('description'),
            TextareaField::new('descriptionAr', 'الوصف')->onlyOnForms()->addCssClass('text-right'),
            TagField::new('tags', TagType::class)->onlyOnForms(),
            TagField::new('togs', TogType::class)->onlyOnForms(),
            AssociationField::new('category'),
            AssociationField::new('subCategory'),
            CollectionField::new('images')
                ->setEntryType(ImageFileType::class)->allowDelete(true)->allowAdd(true),
            CollectionField::new('catalogs')
                ->setEntryType(CatalogType::class)
                ->onlyOnForms(),
            TextEditorField::new('longDescription')->onlyOnForms(),
            TextEditorField::new('longDescriptionAr', 'الوصف الطويل')->onlyOnForms()->addCssClass('text-right'),
            AssociationField::new('fabricType')->onlyOnForms(),
            BooleanField::new('isShow'),
            ImageField::new('imageFile', 'SIZE GUIDE')->setFormType(VichImageType::class)->onlyOnForms(),
//            ImageField::new('fileName', 'SIZE GUIDE')->setCustomOption('basePath', 'media/images/popup/')->onlyOnIndex(),
            Field::new('videoFile', 'Video')->setFormType(VichFileType::class)->setTemplatePath('/media/video')->onlyOnForms(),
//            TextField::new('video', 'Video')->setCustomOption('base_path', '/media/video')->onlyOnIndex(),
        ];
    }

}
