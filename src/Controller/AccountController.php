<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\WishList;
use App\Entity\Customer;
use App\Entity\Newsletter;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\EditAddressType;
use App\Form\EditPasswordType;
use App\Form\EditProfileType;
use App\Form\NewsletterType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AccountController
 * @package App\Controller
 */
class AccountController extends AbstractController
{

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
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

    public function __construct(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, Cart $cart, WishList $wishlist, BannerRepository $bannerRepository)
	{
		$this->entityManager = $entityManager;
        $this->cart = $cart;
        $this->wishlist = $wishlist;
        $this->categoryRepository = $categoryRepository;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @Route("/{locale}/account", name="account", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
	public function index($locale, Request $request): Response
	{
        /**
         * @var Customer $customer
         */
        $customer = $this->getUser();
        if(!$customer)
            return $this->redirectToRoute('home', ['locale' => $locale]);
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
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders($customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        $path = ($locale == "en") ? 'account/index.html.twig' : 'account/indexAr.html.twig';
		return $this->render($path, [
            'page' => 'account',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
            'categories' => $this->categoryRepository->findAll(),
            'banner' => $this->bannerRepository->findOneBy(['page'=>'Account']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
	}

    /**
     * @Route("/{locale}/account/edit-account", name="edit.account", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function edit($locale, Request $request): Response
    {
        $customer = $this->getUser();
        if(!$customer)
            return $this->redirectToRoute('home', ['locale' => $locale]);
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        $form = $this->createForm(EditProfileType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('account', ['locale' => $locale]);
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
        $path = ($locale == "en") ? 'account/edit_profile.html.twig' : 'account/edit_profileAr.html.twig';
        return $this->render($path, [
            'page' => 'account',
            'form' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
            'categories' => $this->categoryRepository->findAll(),
            'banner' => $this->bannerRepository->findOneBy(['page'=>'Account']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @Route("/{locale}/account/my-orders", name="my.orders", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function myOrders($locale, Request $request): Response
    {
        /**
         * @var Customer $customer
         */
        $customer = $this->getUser();
        if(!$customer)
            return $this->redirectToRoute('home', ['locale' => $locale]);
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
        $orders = $this->entityManager->getRepository(Order::class)->findBy(['customer' => $customer]);
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        $path = ($locale == "en") ? 'account/my_orders.html.twig' : 'account/my_ordersAr.html.twig';
        return $this->render($path, [
            'page' => 'my.orders',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'orders' => $orders,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
            'categories' => $this->categoryRepository->findAll(),
            'banner' => $this->bannerRepository->findOneBy(['page'=>'Account']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @Route("/{locale}/account/my-order-detail/{id}", name="my.order.detail", defaults={"locale"="en"})
     * @param $locale
     * @param Order $order
     * @param Request $request
     * @return Response
     */
    public function myOrderDetail($locale, Order $order, Request $request): Response
    {
        $customer = $this->getUser();
        if(!$customer)
            return $this->redirectToRoute('home', ['locale' => $locale]);
        if (!$order || $order->getCustomer() != $customer){
			return $this->redirectToRoute('home', ['locale' => $locale]);
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
        $orderDetails = $this->entityManager->getRepository(OrderDetails::class)->findBy(['myOrder' => $order]);
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        $path = ($locale == "en") ? 'account/my_order_detail.html.twig' : 'account/my_order_detailAr.html.twig';
        return $this->render($path, [
            'page' => 'my.order',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'order' => $order,
            'orderDetails' => $orderDetails,
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
            'categories' => $this->categoryRepository->findAll(),
            'banner' => $this->bannerRepository->findOneBy(['page'=>'Account']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @Route("/{locale}/account/edit-password", name="edit.password", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function editPassword($locale, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $customer = $this->getUser();
        if(!$customer)
            return $this->redirectToRoute('home', ['locale' => $locale]);
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
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        $form = $this->createForm(EditPasswordType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($customer, $old_password)) {
                $new_password = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($customer, $new_password);
                $customer->setPassword($password);
                $this->entityManager->flush();
                $message = ($locale == "en") ? 'Your changes were saved!' : 'تم حفظ التغييرات الخاصة بك!';
                $this->addFlash(
                    'notice',
                    $message
                );
            } else {
                $message = ($locale == "en") ? 'Check your password' : 'تحقق من كلمة المرور الخاصة بك';
                $this->addFlash(
                    'notice',
                    $message
                );
            }
        }
        $path = ($locale == "en") ? 'account/edit_password.html.twig' : 'account/edit_passwordAr.html.twig';
        return $this->render($path, [
            'page' => 'account',
            'form' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $this->getUser(),
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
            'categories' => $this->categoryRepository->findAll(),
            'banner' => $this->bannerRepository->findOneBy(['page'=>'Account']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @Route("/{locale}/account/my-address", name="address", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function address($locale, Request $request): Response
    {
        /**
         * @var Customer $customer
         */
        $customer = $this->getUser();
        if(!$customer)
            return $this->redirectToRoute('home', ['locale' => $locale]);
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
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders($customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        $path = ($locale == "en") ? 'account/my_address.html.twig' : 'account/my_addressAr.html.twig';
        return $this->render($path, [
            'page' => 'address',
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
            'categories' => $this->categoryRepository->findAll(),
            'banner' => $this->bannerRepository->findOneBy(['page'=>'Account']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }

    /**
     * @Route("/{locale}/account/edit-address", name="edit.address", defaults={"locale"="en"})
     * @param $locale
     * @param Request $request
     * @return Response
     */
    public function editAddress($locale, Request $request): Response
    {
        $customer = $this->getUser();
        $pendingOrders = $this->entityManager->getRepository(Order::class)->findPendingOrders( $customer);
        $successOrders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($customer);
        $canceledOrders = $this->entityManager->getRepository(Order::class)->findCanceledOrders($customer);
        $form = $this->createForm(EditAddressType::class, $customer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('address', ['locale' => $locale]);
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
        $path = ($locale == "en") ? 'account/edit_address.html.twig' : 'account/edit_addressAr.html.twig';
        return $this->render($path, [
            'page' => 'address',
            'form' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'customer' => $customer,
            'pendingOrders' => $pendingOrders,
            'successOrders' => $successOrders,
            'canceledOrders' => $canceledOrders,
            'categories' => $this->categoryRepository->findAll(),
            'banner' => $this->bannerRepository->findOneBy(['page'=>'Account']),
            'newsletterForm' => $newsletterType->createView(),
        ]);
    }
}
