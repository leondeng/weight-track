<?php

namespace Fan\LawnBotBundle\EventListener;

use Fan\LawnBotBundle\Controller\TransactionWrapController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Doctrine\ORM\EntityManager;

class TransactionWrapListener
{
  protected $em;

  public function __construct(EntityManager $em) {
      $this->em = $em;
  }

  public function onKernelController(FilterControllerEvent $event) {
    $controller = $event->getController();

    /*
     * $controller passed can be either a class or a Closure.
     * This is not usual in Symfony but it may happen.
     * If it is a class, it comes in array format
     */
    if (! is_array($controller)) {
      return;
    }

    if ($controller[0] instanceof TransactionWrapController) {
      $this->em->beginTransaction();

      // mark the request as having wrapped transaction
      $event->getRequest()->attributes->set('transaction_wrapped', true);

      if ($controller[0]->needsRollback()) $event->getRequest()->attributes->set('rollback', true);
    }
  }

  public function onKernelResponse(FilterResponseEvent $event) {
    // check to see if onKernelController marked this as a token "transaction wrapped" request
    if (!$event->getRequest()->attributes->get('transaction_wrapped')) {
        return;
    }

    if ($event->getRequest()->attributes->get('rollback')) {
      $this->em->rollback();
    }
  }

}