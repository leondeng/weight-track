<?php

namespace Fan\WeightTrackBundle\Controller;

interface TransactionWrapController
{
  public function needsRollback();
}