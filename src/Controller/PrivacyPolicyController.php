<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\WishList;
use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\PrivacyPolicyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrivacyPolicyController extends AbstractController
{

    /**
     * @Route("/{locale}/privacy-policy", name="privacy.policy", defaults={"locale"="en"})
     * @param $locale
     * @param CategoryRepository $categoryRepository
     * @param Cart $cart
     * @param WishList $wishlist
     * @param PrivacyPolicyRepository $privacyPolicyRepository
     * @param BannerRepository $bannerRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index($locale,CategoryRepository $categoryRepository, Cart $cart, WishList $wishlist, PrivacyPolicyRepository $privacyPolicyRepository, BannerRepository $bannerRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $newsletter = new Newsletter();
        $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        $newsletterType->handleRequest($request);
        if ($newsletterType->isSubmitted() && $newsletterType->isValid()) {
            $entityManager->persist($newsletter);
            $entityManager->flush();
            unset($newsletter);
            unset($newsletterType);
            $newsletter = new Newsletter();
            $newsletterType = $this->createForm(NewsletterType::class, $newsletter);
        }
        $path = ($locale == "en") ? 'privacy/index.html.twig' : 'privacy/indexAr.html.twig';
        return $this->render($path, [
            'page' => 'terms.conditions',
            'cart' => $cart->getFull($cart->get()),
            'wishlist' => $wishlist->getFull(),
            'about' => $privacyPolicyRepository->find(1),
            'categories' => $categoryRepository->findAll(),
            'banner' =>$bannerRepository->findOneBy(['page'=>'About']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }
}
