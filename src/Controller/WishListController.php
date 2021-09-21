<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\WishList;
use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishListController extends AbstractController
{
    /**
     * @var WishList
     */
    private $wishlist;
    /**
     * @var Cart
     */
    private $cart;
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


    public function __construct(Cart $cart, CategoryRepository $categoryRepository, WishList $wishlist, BannerRepository $bannerRepository, EntityManagerInterface $entityManager)
    {
        $this->wishlist = $wishlist;
        $this->cart = $cart;
        $this->categoryRepository = $categoryRepository;
        $this->bannerRepository = $bannerRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/{locale}/wishlist", name="wishlist", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function index($locale, Request $request): Response
    {
        $wishlist = $this->wishlist->getFull();
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
        if (empty($wishlist)) {
            $path = ($locale == "en") ? 'wishlist/empty-wishlist.html.twig' : 'wishlist/empty-wishlistAr.html.twig';
            return $this->render($path, [
                'cart' => $this->cart->getFull($this->cart->get()),
                'page' => 'wishlist',
                'wishlist' => $wishlist,
                'categories' => $this->categoryRepository->findAll(),
                'banner' =>$this->bannerRepository->findOneBy(['page'=>'Cart']),
                'newsletterForm' => $newsletterType->createView(),
            ]);
        }else {
            $path = ($locale == "en") ? 'wishlist/index.html.twig' : 'wishlist/indexAr.html.twig';
            return $this->render($path, [
                'cart' => $this->cart->getFull($this->cart->get()),
                'wishlist' => $this->wishlist->getFull(),
                'page' => 'wishlist',
                'categories' => $this->categoryRepository->findAll(),
                'banner' =>$this->bannerRepository->findOneBy(['page'=>'Cart']),
                'newsletterForm' => $newsletterType->createView(),
            ]);
        }
    }
    /**
     * @Route("/{locale}/wishlist/add/{id}", name="add.wishlist", defaults={"id"=0, "locale"="en"})
     * @param $locale
     * @param $id
     * @return Response
     */
    public function add($locale, $id): Response
    {
        $this->wishlist->add($id);
        return $this->redirectToRoute('products', ['locale' => $locale]);
    }

    /**
     * @Route("/{locale}/wishlist/remove/{route}", name="remove.wishlist", defaults={"locale"="en"})
     * @param $locale
     * @param $route
     * @return Response
     */
    public function remove($locale, $route): Response
    {
        $this->wishlist->remove();
        return $this->redirectToRoute($route, ['locale' => $locale]);
    }

    /**
     * @Route("/{locale}/wishlist/delete/{id}-{route}", name="delete.wishlist", defaults={"locale"="en"})
     * @param $locale
     * @param $id
     * @param $route
     * @return Response
     */
    public function delete($locale, $id, $route): Response
    {
        $this->wishlist->delete($id);
        return $this->redirectToRoute($route, ['locale' => $locale]);
    }
}
