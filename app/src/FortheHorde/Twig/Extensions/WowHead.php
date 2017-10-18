<?php

namespace FortheHorde\Twig\Extensions;

use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class WowHead extends Twig_Extension {

    public function getFunctions() {
        $functions = array(
            new Twig_SimpleFunction('item', array($this, 'itemIcon'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new Twig_SimpleFunction('recipe', array($this, 'recipe'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
        return $functions;
    }

    public function itemIcon(Twig_Environment $env, $id) {
        return '<a href="http://fr.wowhead.com/item=' . $id . '" >item</a>';
    }

    public function recipe(Twig_Environment $env, int ...$a) {
        $nbitem = $a[0];
        $item = $a[1];

        $tmp = $nbitem . '*<a href="http://fr.wowhead.com/item=' . $item . '" >item</a> composé de';
        $nb = count($a) - 2;
        if ($nb > 0) {
            $tmp .= '(<br>';
            for ($i = 0; $i < ($nb / 2); $i++) {
                $tmp .= $a[$i * 2 + 2 + 0] . '<a href="http://fr.wowhead.com/item=' . $a[$i * 2 + 2 + 1] . '" >item</a><br>';
            }
            $tmp .= ')';
        }

        return $tmp;
    }

}
