<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Filters\CategoryBooksCountFilter;
use App\Repository\CategoryRepository;
use App\Utils\BookCountCacheManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public $categoryRepository;
    public $bookCountCacheManager;

    public function __construct(CategoryRepository $categoryRepository, BookCountCacheManager $bookCountCacheManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->bookCountCacheManager = $bookCountCacheManager;
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $categories = $this->categoryRepository->findAll();
        $books_count_cache = $this->bookCountCacheManager->build($categories);

        return [
            TextField::new('name', 'Names'),

            NumberField::new('id', 'Count of Books')
                ->formatValue(function ($id) use ($books_count_cache) {
                    return $books_count_cache[$id];
                })
                ->setSortable(false)
                ->onlyOnIndex(),

            NumberField::new('booksCount', 'Count of Books')
                ->onlyOnDetail(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add(CategoryBooksCountFilter::new('booksCount', 'Count of Books'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
        ;
    }
}