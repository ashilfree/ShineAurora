<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Contact;
use App\Classes\Mailer;
use App\Classes\WishList;
use App\Entity\Newsletter;
use App\Form\ContactType;
use App\Form\NewsletterType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{

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
     * ContactController constructor.
     * @param Mailer $mailer
     * @param Cart $cart
     * @param CategoryRepository $categoryRepository
     * @param BannerRepository $bannerRepository
     * @param WishList $wishlist
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        Mailer $mailer,
         Cart $cart,
        CategoryRepository $categoryRepository,
        BannerRepository $bannerRepository,
        WishList $wishlist,
        EntityManagerInterface $entityManager
    )
    {
        $this->mailer = $mailer;
        $this->cart = $cart;
        $this->wishlist = $wishlist;
        $this->categoryRepository = $categoryRepository;
        $this->bannerRepository = $bannerRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{locale}/contact-us", name="contact.us", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function index($locale, Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->mailer->sendContactEmail($contact);
            $message = ($locale == "en") ? 'Your Message has been sent' : 'تم ارسال رسالتك';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('contact.us', ['locale' => $locale]);
        }
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
        $path = ($locale == "en") ? 'contact/index.html.twig' : 'contact/indexAr.html.twig';
        return $this->render($path, [
            'form' => $form->createView(),
            'page' => 'contact.us',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'categories' => $this->categoryRepository->findAll(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Contact']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @Route("/{locale}/help", name="help", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function help($locale, Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->mailer->sendContactEmail($contact);
            $message = ($locale == "en") ? 'Your Message has been sent' : 'تم ارسال رسالتك';
            $this->addFlash('success', $message);
            return $this->redirectToRoute('contact.us', ['locale' => $locale]);
        }
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
        $path = ($locale == "en") ? 'help/index.html.twig' : 'help/indexAr.html.twig';
        return $this->render($path, [
            'form' => $form->createView(),
            'page' => 'contact.us',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'categories' => $this->categoryRepository->findAll(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Contact']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }
}
