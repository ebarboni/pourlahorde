<?php

namespace FortheHorde\Twig\Extensions;

use Twig_Extension;
use Twig_SimpleFunction;

class Text extends Twig_Extension {

    public function getFunctions() {
        $functions = array(
            new Twig_SimpleFunction('showRoster', array($this, 'showGURoster'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
        return $functions;
    }

    public function showGURoster() {
        $cool = "ABC";
        return $cool;
    }

}

?>