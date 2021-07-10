<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\PaymentMethods;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingFullName', TextType::class, [
                'label'=>false
            ])
            ->add('shippingAddress', TextType::class, [
                'label'=>false
            ])
            ->add('shippingCity', TextType::class, [
                'label'=>false
            ])
            ->add('shippingProvince', TextType::class, [
                'label'=>false
            ])
            ->add('shippingEmail', EmailType::class, [
                'label'=>false
            ])
            ->add('shippingPhone', TelType::class, [
                'label'=>false
            ])
            ->add('shippingSecondPhone', TelType::class, [
                'label'=> false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
