<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\WishList;
use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\GovernorateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var GovernorateRepository
     */
    private $governorateRepository;
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


    public function __construct(Cart $cart, CategoryRepository $categoryRepository, WishList $wishlist, GovernorateRepository $governorateRepository, BannerRepository $bannerRepository, EntityManagerInterface $entityManager)
    {
        $this->cart = $cart;
        $this->governorateRepository = $governorateRepository;
        $this->wishlist = $wishlist;
        $this->categoryRepository = $categoryRepository;
        $this->bannerRepository = $bannerRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{locale}/cart", name="cart", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function index($locale, Request $request): Response
    {
        $cart = $this->cart->getFull($this->cart->get());
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
        if (empty($cart)) {
            $path = ($locale == "en") ? 'cart/empty-cart.html.twig' : 'cart/empty-cartAr.html.twig';
            return $this->render($path, [
                'cart' => $cart,
                'page' => 'cart',
                'wishlist' => $this->wishlist->getFull(),
                'categories' => $this->categoryRepository->findAll(),
                'banner' =>$this->bannerRepository->findOneBy(['page'=>'Cart']),
                'newsletterForm' => $newsletterType->createView(),
            ]);
        }else {
            $path = ($locale == "en") ? 'cart/index.html.twig' : 'cart/indexAr.html.twig';
            return $this->render($path, [
                'cart' => $cart,
                'wishlist' => $this->wishlist->getFull(),
                'page' => 'cart',
                'delivery' => $this->cart->getDelivery(),
                'deliveryIndex' => $this->cart->getDeliveryIndex(),
                'governorates' => $this->governorateRepository->findAll(),
                'categories' => $this->categoryRepository->findAll(),
                'banner' =>$this->bannerRepository->findOneBy(['page'=>'Cart']),
                'newsletterForm' => $newsletterType->createView(),
                'coupon' => $this->cart->getCoupon(),
            ]);
        }
    }

    /**
     * @Route("/{locale}/cart/add/{id}/{quantity}", name="add.cart", defaults={"id"=0, "quantity"=1, "locale"="en"})
     * @param $locale
     * @param $id
     * @param $quantity
     * @param Request $request
     * @return Response
     */
    public function add($locale, $id, $quantity, Request $request): Response
    {
        $this->cart->add($id, $quantity);
        return $this->redirectToRoute('products', ["locale" => $locale]);
    }

    /**
     * @Route("/{locale}/cart/update", name="update.cart", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function update($locale, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $this->cart->update($data);
        return $this->redirectToRoute('cart', ["locale" => $locale]);
    }

    /**
     * @Route("/{locale}/cart/remove/{route}", name="remove.cart", defaults={"locale"="en"})
     * @param $locale
     * @param $route
     * @return Response
     */
    public function remove($locale, $route): Response
    {
        $this->cart->remove();
        return $this->redirectToRoute('cart', ["locale" => $locale]);
    }

    /**
     * @Route("/{locale}/cart/delete/{id}-{route}", name="delete.cart", defaults={"locale"="en"})
     * @param $locale
     * @param $id
     * @param $route
     * @return Response
     */
    public function delete($locale, $id, $route): Response
    {
        $this->cart->delete($id);
        return $this->redirectToRoute($route, ["locale" => $locale]);
    }
}
