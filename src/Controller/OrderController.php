<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Transaction;
use App\Classes\WishList;
use App\Entity\Coupon;
use App\Entity\Customer;
use App\Entity\Newsletter;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\PaymentMethods;
use App\Form\NewsletterType;
use App\Form\OrderType;
use App\Form\PaymentMethodType;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class OrderController
 * @package App\Controller
 */
class OrderController extends AbstractController
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
     * @var Security
     */
    private $security;
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

    public function __construct
    (
        EntityManagerInterface $entityManager,
        Security $security,
        Cart $cart,
        CategoryRepository $categoryRepository,
        WishList $wishlist,
        SessionInterface $session,
        BannerRepository $bannerRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->cart = $cart;
        $this->security = $security;
        $this->wishlist = $wishlist;
        $this->session = $session;
        $this->categoryRepository = $categoryRepository;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @Route("/{locale}/order", name="order", defaults={"locale"="en"})
     * @param $locale
     * @param Transaction $transaction
     * @param Request $request
     * @return Response
     */
    public function index($locale, Transaction $transaction, Request $request): Response
    {

        if (!empty($this->cart->get())) {
            $this->cart->switch();
        }
        else {
            if (empty($this->cart->getCart2Order()))
                return $this->redirectToRoute('cart', ['locale' => $locale]);
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

        $order = new Order();
        /**
         * @var Customer $user
         */
        $user = $this->security->getUser();
        if ($user) {
            $order->setShippingEmail($user->getEmail());
            $order->setShippingFullName($user->getFullName());
            $order->setShippingPhone($user->getPhone()??'');
            $order->setShippingAddress($user->getAddress()??'');
//            $order->setShippingCity($user->getRegion()??'');
//            $order->setShippingProvince($user->getGovernorate()??'');
        }
        if ($this->session->get('orderId')) {
            $order = $this->entityManager->getRepository(Order::class)->find($this->session->get('orderId'));
        }
        $coupon = $this->cart->getCoupon();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($order->getId() == null) {
                if ($this->cart->checkStock()) {
                    $this->cart->decreaseStock();
                } else {
                    return $this->redirectToRoute('back.to.cart', ['locale' => $locale]);
                }
                $date = new \DateTime();
                $discountCode = $coupon ? $coupon['name'] : 0;
                $discountValue = $coupon ? $coupon['discount'] : 0;
                /** @var Customer $user */
                $user = $this->getUser();
                $reference = $date->format('Ymd') . '-' . uniqid();
                $order->setReference($reference);
                $order->setCustomer($user);
                $order->setCreatedAt($date);
                $order->setDeliveryPrice($this->cart->getDelivery2Order());
                $order->setDiscountCode($discountCode);
                $order->setDiscountValue($discountValue);
                $transaction->applyWorkFlow($order, 'create_order');
                $this->entityManager->persist($order);
                $total = 0.0;
                foreach ($this->cart->getFull($this->cart->getCart2Order()) as $product) {

                    $orderDetail = new OrderDetails();
                    $orderDetail->setMyOrder($order);
                    $orderDetail->setCatalogId($product['catalog']->getId());
                    $orderDetail->setProduct($product['catalog']->getProduct()->getName());
                    $orderDetail->setSize($product['catalog']->getSize());
                    $orderDetail->setQuantity($product['quantity']);
                    $discount = $product['catalog']->getProduct()->getDiscountPrice();
                    $price = ($discount != null || $discount != 0) ? $discount : $product['catalog']->getProduct()->getPrice();
                    $subTotal = $product['quantity'] * $price;
                    $orderDetail->setPrice($price);
                    $orderDetail->setTotal($subTotal);
                    $this->entityManager->persist($orderDetail);
                    $total += $subTotal;
                }
                $order->setTotal($total);
                $order->setIsPaid(false);
                if($discountValue != 0){
                    /**
                     * @var Coupon $coupon
                     */
                    $coupon = $this->entityManager->getRepository(Coupon::class)->findByCode($discountCode);
                    $quantity = $coupon->getUsersNumber();
                    $coupon->setUsersNumber($quantity-1);
                }
            }

            $this->entityManager->flush();
            $paymentMethodForm = $this->createForm(PaymentMethodType::class, $order);
            $this->session->set('orderId', $order->getId());
            $path = ($locale == "en") ? 'order/checkout-two.html.twig' : 'order/checkout-twoAr.html.twig';
            return $this->render($path, [
                    'cart' => $this->cart->getFull($this->cart->get()),
                    'wishlist' => $this->wishlist->getFull(),
                    'cart2order' => $this->cart->getFull($this->cart->getCart2Order()),
                    'order' => $order,
                    'page' => 'order.recap',
                    'form' => $this->createForm(OrderType::class, $order)->createView(),
                    'payment_method_form' => $paymentMethodForm->createView(),
                    'locale' => $locale,
                    'paymentMethods' => $this->entityManager->getRepository(PaymentMethods::class)->findAll(),
                    'categories' => $this->categoryRepository->findAll(),
                    'banner' =>$this->bannerRepository->findOneBy(['page'=>'Order']),
                    'newsletterForm' => $newsletterType->createView(),
                ]
            );

        }
        $path = ($locale == "en") ? 'order/checkout.html.twig' : 'order/checkoutAr.html.twig';
        return $this->render($path, [
            'form' => $form->createView(),
            'cart' => $this->cart->getFull($this->cart->get()),
            'wishlist' => $this->wishlist->getFull(),
            'cart2order' => $this->cart->getFull($this->cart->getCart2Order()),
            'delivery' => $this->cart->getDelivery(),
            'delivery2order' => $this->cart->getDelivery2Order(),
            'page' => 'checkout',
            'categories' => $this->categoryRepository->findAll(),
            'banner' =>$this->bannerRepository->findOneBy(['page'=>'Order']),
            'newsletterForm' => $newsletterType->createView(),
            'coupon' => $coupon,
        ]);
    }

//    /**
//     * @Route("/{locale}/order/recap", name="order.recap", defaults={"locale"="en"})
//     * @param $locale
//     * @param Request $request
//     * @param Transaction $transaction
//     * @return Response
//     */
//    public function add($locale, Request $request, Transaction $transaction): Response
//    {
//
//        if ($this->session->get('orderId')) {
//            $order = $this->entityManager->getRepository(Order::class)->find($this->session->get('orderId'));
//        } else {
//            $order = new Order();
//            if ($this->cart->checkStock()) {
//                $this->cart->decreaseStock();
//            } else {
//                return $this->redirectToRoute('back.to.cart', ['locale' => $locale]);
//            }
//        }
//        $form = $this->createForm(OrderType::class, $order);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            if ($order->getId() == null) {
//                $date = new \DateTime();
//                /** @var Customer $user */
//                $user = $this->getUser();
//                $reference = $date->format('Ymd') . '-' . uniqid();
//                $order->setReference($reference);
//                $order->setCustomer($user);
//                $order->setCreatedAt($date);
//                $order->setDeliveryPrice($this->cart->getDelivery2Order());
//                $transaction->applyWorkFlow($order, 'create_order');
//                $this->entityManager->persist($order);
//                $total = 0.0;
//                foreach ($this->cart->getFull($this->cart->getCart2Order()) as $product) {
//                    $subTotal = $product['quantity'] * $product['catalog']->getProduct()->getPrice();
//                    $orderDetail = new OrderDetails();
//                    $orderDetail->setMyOrder($order);
//                    $orderDetail->setProduct($product['catalog']->getProduct()->getName());
//                    $orderDetail->setSize($product['catalog']->getSize());
//                    $orderDetail->setQuantity($product['quantity']);
//                    $discount = $product['catalog']->getProduct()->getDiscountPrice();
//                    $price = ($discount != null || $discount != 0) ? $discount : $product['catalog']->getProduct()->getPrice();
//                    $orderDetail->setPrice($price);
//                    $orderDetail->setTotal($subTotal);
//                    $this->entityManager->persist($orderDetail);
//                    $total += $subTotal;
//                }
//                $order->setTotal($total);
//            }
//
//            $this->entityManager->flush();
//            $paymentMethodForm = $this->createForm(PaymentMethodType::class, $order);
//            $this->session->set('orderId', $order->getId());
//            $path = ($locale == "en") ? 'order/checkout-two.html.twig' : 'order/checkout-twoAr.html.twig';
//            return $this->render($path, [
//                    'cart' => $this->cart->getFull($this->cart->get()),
//                    'wishlist' => $this->wishlist->getFull(),
//                    'cart2order' => $this->cart->getFull($this->cart->getCart2Order()),
//                    'order' => $order,
//                    'page' => 'order.recap',
//                    'form' => $this->createForm(OrderType::class, $order)->createView(),
//                    'payment_method_form' => $paymentMethodForm->createView(),
//                    'locale' => $locale,
//                    'paymentMethods' => $this->entityManager->getRepository(PaymentMethods::class)->findAll(),
//                    'categories' => $this->categoryRepository->findAll(),
//                ]
//            );
//
//        }elseif (!$form->isValid()){
//            return $this->redirectToRoute('order', ['locale' => $locale, 'errors' => true]);
//        }else{
//            return $this->redirectToRoute('cart', ['locale' => $locale]);
//        }
//    }

    /**
     * @Route("/{locale}/order/back-to-cart", name="back.to.cart", defaults={"locale"="en"})
     * @param $locale
     * @return Response
     */
    public function back($locale): Response
    {
        $this->cart->reverseSwitch();
        return $this->redirectToRoute('cart', ['locale' => $locale]);
    }

}
