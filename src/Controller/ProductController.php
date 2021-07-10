<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Filter;
use App\Classes\Search;
use App\Classes\WishList;
use App\Entity\Newsletter;
use App\Form\FilterArType;
use App\Form\FilterType;
use App\Form\NewsletterType;
use App\Form\SearchType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var WishList
     */
    private $wishlist;
    /**
     * @var BannerRepository
     */
    private $bannerRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, BannerRepository $bannerRepository, Cart $cart, WishList $wishlist, EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->wishlist = $wishlist;
        $this->bannerRepository = $bannerRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{locale}/products/{id}-{sub}", name="products", defaults={"locale"="en", "id"= null, "sub"= null})
     * @param $locale
     * @param $id
     * @param $sub
     * @param Request $request
     * @return Response
     */
    public function index($locale, $id, $sub, Request $request): Response
    {
         $page = $request->get('page',1);
         $filter = new Filter();
        $filterType = $this->createForm(FilterType::class, $filter);
        $filterType->handleRequest($request);
        $products = $this->productRepository->findSearch($filter, $id, $sub, $page);
        [$min , $max] = $this->productRepository->findMinMax($filter);
        $newsletter = new Newsletter();
        $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        $newsletterType->handleRequest($request);
//        $content = ($locale == "en") ? 'product/products.html.twig' : 'product/productsAr.html.twig';
//        $sorting = ($locale == "en") ? 'product/_sorting.html.twig' : 'product/_sortingAr.html.twig';
//        $pagination = ($locale == "en") ? 'product/_more.html.twig' : 'product/_moreAr.html.twig';
//        if($request->get('ajax')){
//            return new JsonResponse([
//                "content" => $this->renderView($content, ['products' => $products]),
//                'sorting' => $this->renderView($sorting, ['products' => $products]),
//                'pagination' => $this->renderView($pagination, ['products' => $products]),
//                'pages' => ceil($products->getTotalItemCount() / $products->getItemNumberPerPage()),
//                'min' => $min,
//                'max' => $max
//            ]);
//        }
        if ($newsletterType->isSubmitted() && $newsletterType->isValid()) {
            $this->entityManager->persist($newsletter);
            $this->entityManager->flush();
            unset($newsletter);
            unset($newsletterType);
            $newsletter = new Newsletter();
            $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        }
        $path = ($locale == "en") ? 'product/index.html.twig' : 'product/indexAr.html.twig';
        return $this->render($path, [
            'id'=> $id,
            'sub'=> $sub,
            'page' => 'products',
            'pages' => $page,
            'form' => $filterType->createView(),
            'categories' => $this->categoryRepository->findAll(),
            'products' => $products,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'wish' => $this->wishlist->get(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Shop']),
            'min' => $min,
            'max' => $max,
            'filter' => $filter,
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @Route("/{locale}/product/{slug}", name="product", defaults={"locale"="en"})
     * @param $locale
     * @param $slug
     * @param Request $request
     * @return Response
     */
    public function show($locale, $slug, Request $request): Response
    {
        $product = $this->productRepository->findOneBy(['slug'=>$slug]);
        if(!$product)
            return $this->redirectToRoute('products', ['locale' => $locale]);
        $products = $this->productRepository->findBy(['category'=>$product->getCategory()]);
        $sizes = [];
        $sizesName = [];
        foreach ($product->getCatalogs() as $catalog){
            $sizes[$catalog->getSize()->getId()][] = [$catalog->getColor(), $catalog->getQuantity()];
            if(!isset($sizesName[$catalog->getSize()->getId()]))
                $sizesName[$catalog->getSize()->getId()] = $catalog->getSize()->getName();
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
        $path = ($locale == "en") ? 'product/detail.html.twig' : 'product/detailAr.html.twig';
        return $this->render($path, [
            'page' => 'product',
            'product' => $product,
            'products' => $products,
            'sizes' => $sizes,
            'sizesName' => $sizesName,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'wish' => $this->wishlist->get(),
            'categories' => $this->categoryRepository->findAll(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Product']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }
}
