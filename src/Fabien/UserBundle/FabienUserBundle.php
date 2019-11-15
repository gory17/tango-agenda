<?php

namespace Fabien\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FabienUserBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSUserBundle';
  }
}
