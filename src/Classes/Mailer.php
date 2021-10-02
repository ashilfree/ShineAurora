<?php

namespace App\Classes;

use App\Entity\Customer;
use App\Entity\Order;
use Twig\Environment;

class Mailer{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;


    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $twig
     */
    public function __construct(
        \Swift_Mailer $mailer,
        Environment $twig
)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(Customer $customer, string $locale)
    {
        $path = 'emails/register-confirmation.mjml.twig';
        $body = $this->twig->render($path,
            [
                'customer' => $customer,
                'locale' => $locale
            ]
        );
        $message = (new \Swift_Message('Confirmation Email'))
            ->setFrom('info@shineaurora.com')
            ->setTo($customer->getEmail())
            ->setReplyTo($customer->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendResetPasswordEmail($customer, $resetToken, $tokenLifetime)
    {
        $body = $this->twig->render('emails/reset-password.mjml.twig', [
                'resetToken' => $resetToken,
                'tokenLifetime' => $tokenLifetime,
            ]
        );
        $message = (new \Swift_Message('Reset Password Email'))
            ->setFrom('info@shineaurora.com')
            ->setTo($customer->getEmail())
            ->setReplyTo($customer->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendContactEmail(Contact $contact)
    {
        $body = $this->twig->render('emails/contact-customer.mjml.twig', [
                'contact' => $contact
            ]
        );
        $message = (new \Swift_Message('Contact Us : '))
            ->setFrom('info@shineaurora.com')
            ->setTo($contact->getEmail())
            ->setReplyTo($contact->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendSuccessOrderEmail(Order $order)
    {
        $body = $this->twig->render('emails/order-success.mjml.twig',
            [
                'order' => $order
            ]
        );
        $message = (new \Swift_Message('Successful Order'))
            ->setFrom('info@shineaurora.com')
            ->setTo($order->getShippingEmail())
            ->setReplyTo($order->getShippingEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendFailureOrderEmail(Order $order)
    {
        $body = $this->twig->render('emails/order-failure.mjml.twig',
            [
                'order' => $order
            ]
        );
        $message = (new \Swift_Message('Unsuccessful Order'))
            ->setFrom('info@shineaurora.com')
            ->setTo($order->getShippingEmail())
            ->setReplyTo($order->getShippingEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

    public function sendReceivedOrderEmail(Order $order)
    {
        $body = $this->twig->render('emails/order-received.mjml.twig',
            [
                'order' => $order
            ]
        );
        $message = (new \Swift_Message('Received New Order'))
            ->setFrom('info@shineaurora.com')
            ->setTo('order@shineaurora.com')
            ->setReplyTo('order@shineaurora.com')
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}