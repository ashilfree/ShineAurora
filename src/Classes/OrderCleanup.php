<?php

namespace App\Classes;

use App\Entity\Catalog;
use App\Entity\Coupon;
use App\Entity\Order;
use App\Repository\CatalogRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderCleanup{

    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var CatalogRepository
     */
    private $catalogRepository;
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository, CatalogRepository $catalogRepository, Transaction $transaction)
    {
        $this->orderRepository = $orderRepository;
        $this->catalogRepository = $catalogRepository;
        $this->transaction = $transaction;
        $this->entityManager = $entityManager;
    }

    public function clean(): int
    {
        $date = (new \DateTime());
//        $date->setTimezone(new DateTimeZone('Asia/Kuwait'));
        $date->sub(new \DateInterval('PT5M'));

        $orders = $this->orderRepository->getOrdersToClean($date);

        /**
         * @var Order $order
         */
        foreach ($orders as $order){
            if($this->transaction->check($order, 'order_canceled2')){
                $this->transaction->applyWorkFlow($order, 'order_canceled2');
                $order->setCancelledAt(new \DateTime());
            }
            if($this->transaction->check($order, 'order_canceled')){
                $this->transaction->applyWorkFlow($order, 'order_canceled');
                $order->setCancelledAt(new \DateTime());
            }
            foreach ($order->getOrderDetails() as $orderDetail){
                /**
                 * @var Catalog $catalog
                 */
                $catalog = $this->catalogRepository->findByProductName($orderDetail->getCatalogId());

                if($catalog) {
                    if($catalog->getSize()->getName() == $orderDetail->getSize() && $catalog->getProduct()->getName() == $orderDetail->getProduct()) {
                        $newQuantity = $catalog->getQuantity() + $orderDetail->getQuantity();
                        $catalog->setQuantity($newQuantity);
                    }
                }
            }
            if($order->getDiscountValue() != 0){
                /**
                 * @var Coupon $coupon
                 */
                $coupon = $this->entityManager->getRepository(Coupon::class)->findByCode($order->getDiscountCode());
                $quantity = $coupon->getUsersNumber();
                $coupon->setUsersNumber($quantity+1);
            }
            $this->entityManager->flush();
        }

        return count($orders);
    }
}