<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mailer;
use App\Classes\WishList;
use App\Entity\Customer;
use App\Entity\Newsletter;
use App\Form\CustomerRegisterType;
use App\Form\NewsletterType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Security\CustomerConfirmationService;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainSecurityController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var WishList
     */
    private $wishlist;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var BannerRepository
     */
    private $bannerRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * MainSecurityController constructor.
     * @param AuthenticationUtils $authenticationUtils
     * @param Cart $cart
     * @param CategoryRepository $categoryRepository
     * @param WishList $wishlist
     * @param BannerRepository $bannerRepository
     * @param Mailer $mailer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        Cart $cart,
        CategoryRepository $categoryRepository,
        WishList $wishlist,
        BannerRepository $bannerRepository,
        Mailer $mailer,
        EntityManagerInterface $entityManager
    )
    {
        $this->authenticationUtils = $authenticationUtils;

        $this->mailer = $mailer;

        $this->cart = $cart;
        $this->wishlist = $wishlist;
        $this->categoryRepository = $categoryRepository;
        $this->bannerRepository = $bannerRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/{locale}/login", name="login", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    // TODO: Use Facebook and Google to Login
    public function login($locale, Request $request): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();
        $newsletter = new Newsletter();
        $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        $newsletterType->handleRequest($request);
        if ($newsletterType->isSubmitted() && $newsletterType->isValid()) {
            $this->entityManager->persist($newsletter);
            $this->entityManager->flush();
            unset($newsletter);
            unset($newsletterType);
            $newsletter = new Newsletter();
            $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        }
        $path = ($locale == "en") ? 'authentication/login.html.twig' : 'authentication/loginAr.html.twig';
            return $this->render($path, [
                'page' => 'login',
                'last_username' => $lastUsername,
                'error' => $error,
                'cart' => $this->cart->getFull($this->cart->get()),
                'wishlist' => $this->wishlist->getFull(),
                'categories' => $this->categoryRepository->findAll(),
                'banner' =>$this->bannerRepository->findOneBy(['page'=>'Authentication']),
                'newsletterForm' => $newsletterType->createView(),
            ]);
    }

    /**
     * @Route("/{locale}/register", name="register", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenGenerator $tokenGenerator
     * @return Response
     */
    // TODO: Use Facebook and Google to Register
    public function register($locale, Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerRegisterType::class, $customer);
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        $newsletter = new Newsletter();
        $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        $newsletterType->handleRequest($request);
        if ($newsletterType->isSubmitted() && $newsletterType->isValid()) {
            $this->entityManager->persist($newsletter);
            $this->entityManager->flush();
            unset($newsletter);
            unset($newsletterType);
            $newsletter = new Newsletter();
            $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($customer, $customer->getPassword());
            $customer->setPassword($password);
            $customer->setConfirmationToken($tokenGenerator->getRandomSecureToken());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();
            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
//            $this->addFlash('success', 'Your account has been registered.');
            //send mail to customer
            $this->mailer->sendConfirmationEmail($customer, $locale);
            //return $this->redirectToRoute('login');
            $path = ($locale == "en") ? 'authentication/check-email-register.html.twig' : 'authentication/check-email-registerAr.html.twig';
            return $this->render($path, [
                'cart' => $this->cart->getFull($this->cart->get()),
                'wishlist' => $this->wishlist->getFull(),
                'page'=> 'register',
                'categories' => $this->categoryRepository->findAll(),
                'banner' =>$this->bannerRepository->findOneBy(['page'=>'Authentication']),
                'newsletterForm' => $newsletterType->createView(),
            ]);
        }
        $path = ($locale == "en") ? 'authentication/register.html.twig' : 'authentication/registerAr.html.twig';
        return $this->render($path, [
            'page'=> 'register',
            'form'=>$form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'categories' => $this->categoryRepository->findAll(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Authentication']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @param string $locale
     * @param string $token
     * @param CustomerConfirmationService $customerConfirmationService
     * @return RedirectResponse
     * @Route("/{locale}/confirm-customer/{token}", name="default.confirm.token", defaults={"locale"="en"})
     */
    public function confirmCustomer(
        string $locale,
        string $token,
        CustomerConfirmationService $customerConfirmationService
    ): RedirectResponse
    {
        $customerConfirmationService->confirmCustomer($token);
        return $this->redirectToRoute('confirmation', ['locale' => $locale]);
    }

    /**
     * @Route("/{locale}/confirmation", name="confirmation", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function confirmation($locale, Request $request): Response
    {
        $newsletter = new Newsletter();
        $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        $newsletterType->handleRequest($request);
        if ($newsletterType->isSubmitted() && $newsletterType->isValid()) {
            $this->entityManager->persist($newsletter);
            $this->entityManager->flush();
            unset($newsletter);
            unset($newsletterType);
            $newsletter = new Newsletter();
            $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        }
        $path = ($locale == "en") ? 'authentication/confirmation.html.twig' : 'authentication/confirmationAr.html.twig';
        return $this->render($path, [
            'page' => 'confirmation',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'categories' => $this->categoryRepository->findAll(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Authentication']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }
}