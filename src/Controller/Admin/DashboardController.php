<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
      $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
          ->setController(BookCrudController::class)
          ->set('menuIndex', 1)
          ->setAction(Action::INDEX)
          ->generateUrl();

        return $this->redirect($url);

        return parent::index();

        // you can also render some template to display a proper Dashboard
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Book Library');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Library');
        yield MenuItem::linkToCrud('Books', 'fas fa-list', Book::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);

        yield MenuItem::section('User');
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);

    }
}
