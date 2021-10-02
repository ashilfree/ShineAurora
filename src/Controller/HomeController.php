<?php

namespace App\Controller;


use App\Classes\Cart;
use App\Classes\WishList;
use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\CategoryRepository;
use App\Repository\DiscountBannerRepository;
use App\Repository\NewsletterRepository;
use App\Repository\PopupRepository;
use App\Repository\ProductRepository;
use App\Repository\SlideRepository;
use App\Repository\VisitStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @var SlideRepository
     */
    private $slideRepository;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var WishList
     */
    private $wishlist;
    /**
     * @var VisitStatsRepository
     */
    private $visitStatsRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var DiscountBannerRepository
     */
    private $discountBannerRepository;
    /**
     * @var PopupRepository
     */
    private $popupRepository;
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct( EntityManagerInterface $entityManager, ProductRepository $productRepository,CategoryRepository $categoryRepository, VisitStatsRepository $visitStatsRepository,SlideRepository $slideRepository, DiscountBannerRepository $discountBannerRepository,Cart $cart, WishList $wishlist, PopupRepository $popupRepository, SessionInterface $session)
    {

        $this->slideRepository = $slideRepository;
        $this->cart = $cart;
        $this->categoryRepository = $categoryRepository;
        $this->wishlist = $wishlist;
        $this->visitStatsRepository = $visitStatsRepository;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->discountBannerRepository = $discountBannerRepository;
        $this->popupRepository = $popupRepository;
        $this->session = $session;
    }

    /**
     * @Route("/{locale}", name="home", defaults={"locale"="ar"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function index($locale, Request $request): Response
    {
        $newsletter = new Newsletter();
        $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        $newsletterType->handleRequest($request);
        if ($newsletterType->isSubmitted() && $newsletterType->isValid()) {
            if(!$this->session->get('newsletter')){
                $this->entityManager->persist($newsletter);
                $this->entityManager->flush();
            }
            unset($newsletter);
            unset($newsletterType);
            $newsletter = new Newsletter();
            $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
            $this->session->set('newsletter', true);
        }
        $path = ($locale == "en") ? 'home/index.html.twig' : 'home/indexAr.html.twig';
            return $this->render($path, [
                'page' => 'home',
                'categories' => $this->categoryRepository->findAll(),
//                'products' => $this->productRepository->findBy(['isShow' => 1], array('id' => 'DESC')),
                'products' => $this->productRepository->findVisibleProducts(),
                'slides' => $this->slideRepository->findAll(),
                'cart' => $this->cart->getFull($this->cart->get()),
                'wishlist' => $this->wishlist->getFull(),
                'banner' => $this->discountBannerRepository->find(1),
                'newsletterForm' => $newsletterType->createView(),
                'popup' => $this->popupRepository->find(1),
                'newsletter' => $this->session->get('newsletter')
            ]);

    }
}
