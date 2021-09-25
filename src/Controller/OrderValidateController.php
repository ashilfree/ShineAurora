<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mailer;
use App\Classes\Transaction;
use App\Classes\WishList;
use App\Entity\Newsletter;
use App\Entity\Order;
use App\Form\NewsletterType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	/**
	 * @var Transaction
	 */
	private $transaction;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var WishList
     */
    private $wishlist;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var BannerRepository
     */
    private $bannerRepository;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, CategoryRepository $categoryRepository, Transaction $transaction,BannerRepository $bannerRepository, Cart $cart, WishList $wishlist, Mailer $mailer)
	{
		$this->entityManager = $entityManager;
		$this->transaction = $transaction;
        $this->cart = $cart;
        $this->mailer = $mailer;
        $this->wishlist = $wishlist;
        $this->session = $session;
        $this->categoryRepository = $categoryRepository;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @Route("/{locale}/order/thank/{reference}", name="order.validate.thank", defaults={"locale"="en"})
     * @param $locale
     * @param $reference
     * @param Request $request
     * @return Response
     */
    public function success($locale, $reference, Request $request): Response
    {
        /** @var Order $order */
	    $order = $this->entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
		if(!$order || $order->getCustomer() != $this->getUser()){
			return $this->redirectToRoute('home');
		}

		if ($this->transaction->check($order, 'checkout')){
            $this->session->clear();
			$this->transaction->applyWorkFlow($order, 'checkout');
            $order->setPaidAt(new \DateTime());
            $order->setIsPaid(1);
			$this->entityManager->flush();
            $this->mailer->sendSuccessOrderEmail($order);
            $this->mailer->sendReceivedOrderEmail($order);
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
        $path = ($locale == "en") ? 'order/order-complete.html.twig' : 'order/order-completeAr.html.twig';
        return $this->render($path, [
        	'order' => $order,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page' => 'order-complete',
            'categories' => $this->categoryRepository->findAll(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Order']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @Route("/{locale}/order/error/{reference}", name="order.validate.error", defaults={"locale"="en"})
     * @param $locale
     * @param $reference
     * @param Request $request
     * @return Response
     */
	public function cancel($locale, $reference, Request $request): Response
	{
		$order = $this->entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
		if(!$order || $order->getCustomer() != $this->getUser()){
			return $this->redirectToRoute('home');
		}
		if ($this->transaction->check($order, 'checkout_canceled'))
			$this->transaction->applyWorkFlow($order, 'checkout_canceled');
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
        $this->cart->increaseStock();
        $this->session->clear();
        $order->setCancelledAt(new \DateTime());
		$this->entityManager->flush();
        $this->mailer->sendFailureOrderEmail($order);
        $path = ($locale == "en") ? 'order/order-canceled.html.twig' : 'order/order-canceledAr.html.twig';
		return $this->render($path, [
			'order' => $order,
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'page' => 'order-canceled',
            'categories' => $this->categoryRepository->findAll(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Order']),
            'newsletterForm' => $newsletterType->createView(),
		]);
	}
}
