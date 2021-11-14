<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Repository\CouponRepository;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CouponController extends AbstractController
{

    /**
     * @var Cart
     */
    private $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @Route("/{locale}/coupon/{code}", name="coupon.store", defaults={"locale"="en"})
     * @param $locale
     * @param $code
     * @param CouponRepository $couponRepository
     * @return Response
     * @throws \Exception
     */
    public function store($locale, $code, CouponRepository $couponRepository): Response
    {
            $coupon = $couponRepository->findByCode($code);
            if(!$coupon){
                $message = ($locale == "en") ? 'invalid coupon code. please try again':'كوبون غير صالح. حاول مرة اخرى';
                $this->addFlash('error', $message);
                return $this->redirectToRoute('cart', ['locale' => $locale]);
            }
            $duration = 'P'.$coupon->getValidateDays().'D';
            if(!$coupon->getStatus()){
                $message = ($locale == "en") ? 'coupon is blocked': "الكوبون محظور";
                $this->addFlash('error', $message);
                return $this->redirectToRoute('cart', ['locale' => $locale]);
            }
            if(($coupon->getStartAt() > (new \DateTime()))){
                $message = ($locale == "en") ? 'coupon is Not Yet Activate':"الكوبون لم يتم تفعيله بعد";
                $this->addFlash('error', $message);
                return $this->redirectToRoute('cart', ['locale' => $locale]);
            }
            if(((new \DateTime()) > $coupon->getStartAt()->add(new DateInterval($duration)))){
                $message = ($locale == "en") ? 'coupon is expired':"الكوبون منتهي الصلاحية";
                $this->addFlash('error', $message);
                return $this->redirectToRoute('cart', ['locale' => $locale]);
            }
            if(($coupon->getUsersNumber() == 0 )){
                $message = ($locale == "en") ? 'coupon is consumed': "تم استهلاك الكوبون";
                $this->addFlash('error', $message);
                return $this->redirectToRoute('cart', ['locale' => $locale]);
            }
            $this->cart->setCoupon([
                'name' => $coupon->getCode(),
                'discount' => $coupon->discount($this->cart->getTotal()),
                'type' => $coupon->getType(),
                'discountValue' => $coupon->discountValue(),
            ]);
            $message = ($locale == "en") ?'coupon has been applied':"تم تطبيق الكوبون";
            $this->addFlash('error', $message);
            return $this->redirectToRoute('cart', ['locale' => $locale]);

//        return $this->redirectToRoute('cart', ['locale' => $locale]);
    }

    /**
     * @Route("/{locale}/coupon-remove", name="coupon.destroy", defaults={"locale"="en"})
     * @param $locale
     * @return Response
     */
    public function destroy($locale): Response
    {
        $this->cart->removeCoupon();
        $message = ($locale == "en") ? 'coupon has been removed': "تمت إزالة الكوبون";
        $this->addFlash('error', $message);
        return $this->redirectToRoute('cart', ['locale' => $locale]);
    }
}
