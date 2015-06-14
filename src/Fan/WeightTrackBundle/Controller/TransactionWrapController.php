<?php

namespace Fan\LawnBotBundle\Controller;

interface TransactionWrapController
{
  public function needsRollback();
}