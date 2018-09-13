<?php

namespace FortheHorde\Twig\Extensions;

use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class Text extends Twig_Extension {

    public function getFunctions() {
        $functions = array(
            new Twig_SimpleFunction('showMain', array($this, 'showGURoster'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new Twig_SimpleFunction('showAlts', array($this, 'showGURosterAlts'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
        return $functions;
    }

// count for ilevel

    private function displayRoster($charobject, $faction) {
        $color = Utils::getColor($charobject->character->class);
        $d = '';
        //'Shiromi'
        if ($faction == 'A') {
            $mainandalt = [
                "Absofang" => ['Absoarka', "Absocreep", "Absozen", "Absohavok", "Absoluthion", 'Absorea'],
                "Myrrdin" => ["Alleister", "Khalima", 'Alleyster', 'Elrudel'],
                "Banniway" => ["Baniway", "Baniways", "Banniways", "Bäniways", "Bänniways", 'Rainbowdashh', "Yawinaab"],
                "Caliwiel" => [],
                "Emmental" => ["Barillium", "Bleuh", 'Chèvredoux', 'Lopinel', 'Morchort', 'Poalhan', 'Roqvefort', 'Rouflakette', 'Cabecou'],
                "Helianthe" => ['Ireene', 'Nehelina'],
                "Hylidev" => ['Feorn', 'Sokea'],
                "Krows" => [],
                "Laycka" => ['Lioz', 'Liøz', 'Shiromi'],
                "Mÿlady" => ['Mirïana', 'Lïnx'],
                "Poilant" => ['Fourras', 'Panoramix', 'Raedsyndar', 'Sharisad', 'Widdershins'],
                "Poilhan" => ['Fkaffe', 'Fkoil', 'Grond', 'Iakf', 'Narjhhan', 'Makroff'],
                "Rengonocho" => ['Baouss', 'Berlonocho', 'Crinacha', 'Gimeno', 'Zapova'],
                "Rhtaar" => ['Hvance', 'Yllaltan'],
                "Saethia" => [],
                "Shuntor" => ['Kathor', 'Shunty', 'Shunthor'],
                "Sneakyzouz" => ['Coachfab', 'Elementzouz', 'Pèrefab'],
                "Lohki" => ['Gaiana', 'Meï', 'Taoling']
            ];
        } else {
            //Dremora,Grimmlock,Grishnok,Halani
            $mainandalt = [
                "Fkaïroc" => ['Aache', 'Aibus', 'Borderpat', 'Illidawi'],
                "Absohawok" => ["Absojöllnir", "Absoriah", 'Absotera'],
                "Sweetiebelle" => ["Bänniways", "Yawinaab"],
                "Baouss" => ['Oxynocho','Taurocho'],
                "Emmental" => ["Aclasse", "Meuhane", 'Moumental', "Wanjin"],
                "Elrudel" => ['Maëlfiel', "Wazzuli", 'Grimmlock','Grishnok'],
                "Fabsneaky" => ['Wildfab'],
                "Hirine" => ['Hiejire', 'Pwalhan', 'Halani'],
                "Petrollhahn" => [],
                "Tynae" => ['Vàsea'],
                "Varhos" => [],
            ];
        }
        foreach ($mainandalt as $main => $alts) {
            if ($main == $charobject->character->name) {
                // main
                $d = 'main';
            }
            if (in_array($charobject->character->name, $alts)) {
                $d = 'alt ' . $main;
            }
        }
        $c = '<div class="aperso ' . $d . ' ">';
        if ($charobject->character->level < 10) {
            $c .= '<img src="http://eu.battle.net/wow/static/images/2d/avatar/' . $charobject->character->race . '-' . $charobject->character->gender . '.jpg" class="persoimg" height="84" width="84" />';
        } else {
            $c .= '<img src="https://render-eu.worldofwarcraft.com/character/' . $charobject->character->thumbnail . '" class="persoimg" height="84" width="84" />';
        }

        $c .= '<span class="pname ' . $color . '"><span class="ppname">' . $charobject->character->name . '</span>';
        if ($charobject->character->level < 120) { // max level
            $c .= '<span class="level">' . $charobject->character->level . '</span>';
        }
        $c .= '</span>';
        $c .= '</div>';
        return $c;
    }

    public function showGURoster(Twig_Environment $env, $faction) {
        $cool = '<div class="col-md-10">';
        foreach (Utils::getMains($faction) as $character) {
            $cool .= $this->displayRoster($character, $faction);
        }
        $cool .= "</div>";
        return $cool;
    }

    public function showGURosterAlts(Twig_Environment $env, $faction) {
        $cool = '<div class="col-md-10">';
        foreach (Utils::getAlts($faction) as $character) {
            $cool .= $this->displayRoster($character, $faction);
        }
        $cool .= "</div>";
        return $cool;
    }

}
