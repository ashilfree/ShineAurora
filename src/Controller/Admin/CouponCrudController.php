<?php

namespace App\Controller\Admin;

use App\Classes\Mailer;
use App\Entity\Coupon;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CouponCrudController extends AbstractCrudController
{
    /**
     * @var CrudUrlGenerator
     */
    private $crudUrlGenerator;

    public function __construct(CrudUrlGenerator $crudUrlGenerator)
    {
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Coupon::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/index', 'admin/coupon/index.html.twig')
            ->overrideTemplate('crud/new', 'admin/coupon/new.html.twig')
            ->overrideTemplate('crud/edit', 'admin/coupon/edit.html.twig')
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig','admin/coupon/form.html.twig'])
            ->overrideTemplate('crud/action', 'admin/coupon/action.html.twig')
            ->setPaginatorPageSize(100)
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $email = Action::new('canceled', 'Email')->linkToCrudAction('sendCode');
        return $actions
            ->add(Crud::PAGE_INDEX, $email)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('code'),

            ChoiceField::new('type')->setChoices([
                "PERCENT" => 'percent',
                "VALUE" => 'fixed'
            ]),
            MoneyField::new('value')->setCurrency('KWD'),
            PercentField::new('percent_off'),
            DateField::new('startAt'),
            IntegerField::new('validateDays'),
            IntegerField::new('usersNumber'),
            BooleanField::new('status'),
        ];
    }

    /**
     * @Route("/admin/coupon/send-email", name="coupon.code.send", defaults={"locale"="en"}, methods={"POST"})
     * @param Request $request
     * @param Mailer $mailer
     * @return RedirectResponse
     */
    public function send(Request $request, Mailer $mailer): RedirectResponse
    {
        parse_str($request->getContent(),$result);
        $mailer->sendCodeCouponEmail($result['to'], $result['subject'], $result['coupon']);
        $url = $this->crudUrlGenerator->build()
            ->setController(CouponCrudController::class)
            ->setAction('index')
            ->generateUrl();
        return $this->redirect($url);
    }

}
