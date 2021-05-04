<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name');
        yield TextField::new('author');

        //yield TextField::new('image')->hideOnForm();

        yield ImageField::new('image')
          ->setUploadDir('public/uploads')
          ->setBasePath('uploads')
          ->setUploadedFileNamePattern('[name]-[randomhash].[extension]')
          ->onlyOnForms();

        yield ImageField::new('image')
          ->setBasePath('uploads')
          ->hideOnForm();

        yield AssociationField::new('category');
    }
    
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
        ;
    }
    
 
}
