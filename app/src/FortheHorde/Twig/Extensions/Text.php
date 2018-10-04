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

    private function displayRoster($character, $faction, $reduc) {
        $color = Utils::getColor($character->class);
        $d = '';
        //'Shiromi'
        foreach ($this->mainandalt as $main => $alts) {
            if ($main == $character->name) {
                // main
                $d = 'main';
            }
            if (in_array($character->name, $alts)) {
                $d = 'alt ' . $main;
            }
        }
        $c = '<div class="aperso ' . $d . ' ">';
        if ($reduc) {
            $c .= '<img src="https://render-eu.worldofwarcraft.com/character/' . $character->thumbnail . '" class="persoimgs" height="84" width="84" />';
        } else {
            $c .= '<img src="https://render-eu.worldofwarcraft.com/character/' . $character->thumbnail . '" class="persoimg" height="84" width="84" />';
        }
        $c .= '<span class="pname ' . $color . '"><span class="ppname">' . $character->name . '</span>';
        if ($character->level < 120) { // max level
            $c .= '<span class="level">' . $character->level . '</span>';
        }
        $c .= '</span>';
        $c .= '</div>';
        return $c;
    }

    private function displayLowRoster($character, $faction, $reduc) {
        /* $color = Utils::getColor($character->class);
          $d = '';
          //'Shiromi'
          foreach ($this->mainandalt as $main => $alts) {
          if ($main == $character->name) {
          // main
          $d = 'main';
          }
          if (in_array($character->name, $alts)) {
          $d = 'alt ' . $main;
          }
          }
          $c = '<div class="aperso ' . $d . ' ">';
          if ($character->level < 10) {
          $c .= '<img src="http://eu.battle.net/wow/static/images/2d/avatar/' . $character->race . '-' . $character->gender . '.jpg" class="persoimg" height="120" width="120" />';
          } else {
          $c .= '<img src="https://render-eu.worldofwarcraft.com/character/' . $character->thumbnail . '" class="persoimg" height="120" width="120" />';
          }

          $c .= '<span class="pname ' . $color . '"><span class="ppname">' . $character->name . '</span>';
          if ($character->level < 120) { // max level
          $c .= '<span class="level">' . $character->level . '</span>';
          }
          $c .= '</span>';
          $c .= '</div>'; */
        return ""; //$c;
    }

    public function showGURoster(Twig_Environment $env, $faction) {
        if ($faction == 'A') {
            $this->mainandalt = [
                "Absofang" => ["Absocreep", "Absozen", "Absoluthion", 'Absorea'],
                "Myrrdin" => ["Alleister", 'Elrudel'],
                "Banniway" => ["Baniway", "Baniways", "Banniways", "Bänniways", 'Rainbowdashh', "Yawinaab"],
                "Caliwiel" => [],
                "Emmental" => ["Barillium", "Bleuh", 'Chèvredoux', 'Lopinel', 'Morchort', 'Poalhan', 'Roqvefort', 'Rouflakette', 'Cabecou'],
                "Helianthe" => ['Ireene'],
                "Hylidev" => ['Feorn', 'Sokea'],
                "Krows" => [],
                "Laycka" => ['Lioz', 'Liøz', 'Shiromi'],
                "Mÿlady" => ['Mirïana', 'Lïnx'],
                "Poilant" => ['Fourras', 'Panoramix', 'Raedsyndar', 'Sharisad', 'Widdershins'],
                "Poilhan" => ['Fkaffe', 'Fkoil', 'Grond', 'Iakf', 'Narjhhan', 'Makroff'],
                "Rengonocho" => ['Baouss', 'Berlonocho'],
                "Rhtaar" => ['Hvance', 'Yllaltan'],
                "Saethia" => [],
                "Shuntor" => ['Kathor', 'Shunty', 'Shunthor'],
                "Sneakyzouz" => ['Coachfab', 'Elementzouz', 'Pèrefab'],
                "Lohki" => ['Taoling'],
                'Aranør' => [],
            ];
        } else {
            //Dremora,Grimmlock,Grishnok,Halani
            $this->mainandalt = [
                "Fkaïroc" => ['Aache', 'Aibus', 'Borderpat', 'Illidawi'],
                "Absohawok" => ["Absojöllnir", "Absoriah", 'Absotera', 'Absokor'],
                "Sweetiebelle" => ["Bänniways", "Yawinaab"],
                "Baouss" => ['Oxynocho', 'Taurocho'],
                "Emmental" => ["Aclasse", "Meuhane", 'Moumental', "Wanjin"],
                "Elrudel" => ['Maëlfiel', "Wazzuli", 'Grimmlock', 'Grishnok'],
                "Fabsneaky" => ['Wildfab'],
                "Hirine" => ['Hiejire', 'Pwalhan', 'Halani'],
                "Petrollhahn" => [],
                "Tynae" => ['Vàsea'],
                "Varhos" => [],
                'Dremora' => [],
            ];
        }

        $cool = '';
        foreach (Utils::getMains($faction) as $character) {
            $cool .= '<div class="row">';
            $cool .= '<div class="col-sm">';
            $cool .= $this->displayRoster($character->character, $faction, false);
            $cool .= "</div>";
            $i = 0;
            foreach ($this->mainandalt[$character->character->name] as $main => $alts) {
                $i++;
                $deco = Utils::getDecodedPlayer($alts);
                $cool .= '<div class="col-sm">';
                if (isset($deco->class)) {
                    $cool .= $this->displayRoster($deco, $faction, true);
                } else {
                    $cool .= $this->displayLowRoster($deco, $faction, true);
                }
                $cool .= "</div>";
            }
            for ($j = $i; $j < 12; $j++) {
                $cool .= '<div class="col-sm">';
                $cool .= "</div>";
            }
            $cool .= "</div>";
        }

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
