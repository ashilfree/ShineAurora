<?php


namespace App\Listener;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceListener
{
    private $maintenance;
    /**
     * @var Environment
     */
    private $twig;

    /**
     * MaintenanceListener constructor.
     * @param $maintenance
     * @param Environment $twig
     */
    public function __construct($maintenance, Environment $twig)
    {
        $this->maintenance = $maintenance;
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if(!file_exists($this->maintenance))
            return;

        $event->setResponse(
            new Response(
                $this->twig->render('maintenance/maintenance.html.twig'),
                Response::HTTP_SERVICE_UNAVAILABLE
            )
        );

        $event->stopPropagation();
    }
}