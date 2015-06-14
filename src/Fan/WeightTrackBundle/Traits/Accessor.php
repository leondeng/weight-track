<?php

namespace Fan\WeightTrackBundle\Traits;

use Doctrine\Common\Inflector\Inflector;

trait Accessor
{

  public function get($property) {
    return $this->$property;
  }

  public function set($property, $value) {
    $this->$property = $value;

    return $this;
  }

  public function __call($method, $arguments) {
    $failed = false;
    try {
      if (in_array($verb = substr($method, 0, 3), array (
        'set',
        'get'
      ))) {
        $prop = Inflector::camelize(substr($method, 3));

        $refl = new \ReflectionObject($this);
        if ($refl->hasProperty($prop)) {
          return call_user_func_array(array (
            $this,
            $verb
          ), array_merge(array (
            $prop
          ), $arguments));
        } else {
          throw new \Exception(sprintf('Unknown property %s::$%s!', get_class($this), $prop));
        }
      }
    } catch ( \Exception $e ) {
      throw $e;
    }
  }
}