<?php


namespace App\Listener;


use App\Entity\Coupon;
use App\Service\CodeCouponGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    /**
     * @var CodeCouponGenerator
     */
    private $codeCouponGenerator;

    public function __construct(CodeCouponGenerator $codeCouponGenerator)
    {

        $this->codeCouponGenerator = $codeCouponGenerator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            BeforeEntityPersistedEvent::class => array('setCodeCoupon'),
        );
    }

    public function setCodeCoupon(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Coupon)) {
            return;
        }


        $entity->setCode($this->codeCouponGenerator->getRandomSecureCode());

    }
}