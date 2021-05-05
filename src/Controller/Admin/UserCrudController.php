<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Security\EmailVerifier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class UserCrudController extends AbstractCrudController
{
    public $verifyEmailHelper;
    public $emailVerifier;
    public $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, VerifyEmailHelperInterface $verifyEmailHelper, EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('username');
        yield TextField::new('email')->setFormType(EmailType::class);
        yield TextField::new('plainPassword', 'Password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->onlyOnForms()
        ;

        // yield TextField::new('password', 'Password')
        //  ->onlyOnIndex();

        //yield ArrayField::new('roles')->onlyOnForms();

        yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->setChoices([
                'User' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->renderExpanded(true)
            ->onlyOnForms()
        ;

        yield BooleanField::new('isActive');

        yield BooleanField::new('isVerified');
    }

    public function configureActions(Actions $actions): Actions
    {
        $resend = Action::new('Resend', '')
            ->setIcon('fas fa-paper-plane')
            ->setLabel('Resend Verify Email')
            ->setCssClass('btn btn-secondary')
            ->linkToCrudAction('resend')
        ;

        return $actions
            ->add(Crud::PAGE_EDIT, $resend)
        ;
    }

    public function resend(AdminContext $context)
    {
        $id = $context->getRequest()->query->get('entityId');
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('booklibrary@example.com', 'Book Library'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        $this->addFlash('success', 'New email sent');

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(UserCrudController::class)
                ->setAction(Action::EDIT)
                ->generateUrl()
        );
    }
}