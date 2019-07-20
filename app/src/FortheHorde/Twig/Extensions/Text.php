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
        /* foreach ($this->mainandalt as $main => $alts) {
          if ($main == $character->name) {
          // main
          $d = 'main';
          }
          if (in_array($character->name, $alts)) {
          $d = 'alt ' . $main;
          }
          } */
        $c = '<div class="vignette aperso ' . $d . ' ">';
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

    private static $displayitem = [1, 2, 3, 4, 5, 9, 16, 17, 10, 6, 7, 8, 11, 12, 13, 14];
    private static $slotNameID = [
        1 => "head",
        2 => "neck",
        3 => "shoulder",
        4 => "back",
        5 => "chest",
        9 => "wrist",
        16 => "mainHand",
        17 => "offHand",
        10 => "hands",
        6 => "waist",
        7 => "legs",
        8 => "feet",
        11 => "finger1",
        12 => "finger2",
        13 => "trinket1",
        14 => "trinket2",
    ];

    private static function getItemLevelL($character, $key) {
        $ii = 0;
        //$itemlvl = $json_decoded->items;
        //echo serialize($itemlvl);
        $wowh = '';
        $art = false;
        $slotname = Text::$slotNameID[$key];
        if (isset($character->items->$slotname)) {
            $slot = $character->items->$slotname;
            $ii = $slot->itemLevel;
            if ($key == 17) {
                $nameof17 = Text::$slotNameID[17];
                $itemat17 = $character->items->$nameof17;
                $nameof16 = Text::$slotNameID[16];
                $itemat16 = $character->items->$nameof16;
                if ($itemat17->artifactAppearanceId != 0 && ( $itemat17->artifactAppearanceId == $itemat16->artifactAppearanceId)) {
                    $art = true;
                    $ii = 0;
                }
            }
            $set = '';
            if (isset($slot->tooltipParams->set)) {
                $set .= '&amp;pcs=' . implode(":", $slot->tooltipParams->set);
            }
            $ench = '';
            if (isset($slot->tooltipParams->enchant)) {
                $ench .= '&amp;ench=' . $slot->tooltipParams->enchant;
            }
            $gem = '';

            if (isset($slot->tooltipParams->gem0)) {
                $gem .= '&amp;gems=';

                $gem .= $slot->tooltipParams->gem0;
                for ($i = 1; $i < 5; $i++) {
                    $gemid = 'gem' . $i;
                    if (isset($slot->tooltipParams->$gemid)) {
                        $gem .= ':' . $slot->tooltipParams->$gemid;
                    }
                }
            }
            $aze = '';
            if (isset($slot->azeriteEmpoweredItem->azeritePowers[0])) {
                $aze .= '&amp;azerite-powers=';

                $aze .= $character->class;
                for ($i = 1; $i < 4; $i++) {
                    if (isset($slot->azeriteEmpoweredItem->azeritePowers[$i])) {
                        $aze .= ':' . $slot->azeriteEmpoweredItem->azeritePowers[$i]->id;
                    }
                }
            }

            $wowh = '<a href="http://fr.wowhead.com/item=' . $slot->id . '" rel="item=' .
                    $slot->id . '&amp;bonus=' .
                    implode(":", $slot->bonusLists)
                    . $set . $gem . $ench . $aze . '&amp;lvl=' . $character->level . '"><img class="wowitem q' . $slot->quality . '" src="https://render-eu.worldofwarcraft.com/icons/36/' . $slot->icon . '.jpg"/></a><br>';
        }

        if ($ii == 0 && !$art) {
            return '<div class="col-xs-1 mw1">&nbsp;</div>';
        } else if ($ii == 0 && $art) {
            return '<div class="col-xs-1 mw1 anitem">' . $wowh . '&nbsp;</div>';
        } else {
            if ($aze == '') {
                return '<div class="col-xs-1 mw1 anitem">' . $wowh . $ii . '</div>';
            } else {
                return '<div class="col-xs-1 mw1 anitem anitemaze">' . $wowh . $ii . '</div>';
            }
        }
        //<a href="http://fr.wowhead.com/item=' . $id . '" >item</a>
    }

    public static function displayStuff($character, $detail = true) {
        //var_dump($character);
        if (isset($character->items)) {
            $tmp = '<div class="col-xs-1 mw">' . $character->items->averageItemLevel . '</div><div class="col-xs-1 mw">' . $character->items->averageItemLevelEquipped . '</div>';
        } //<td>' . floor($tmp / $di) . '</td>';
        else {
            $tmp = '<div class="col-xs-1 mw">&nbsp;</div><div class="col-xs-1">&nbsp;</div>';
        }
        if (isset($character->items->neck->azeriteItem) && $character->items->neck->azeriteItem->azeriteLevel > 0) {
            $tmp .= '<div class="col-xs-1 mw">' . $character->items->neck->azeriteItem->azeriteLevel;
            $tmp .= '<br>' . '<span class="anitem">' . number_format(100 * ($character->items->neck->azeriteItem->azeriteExperience / $character->items->neck->azeriteItem->azeriteExperienceRemaining), 2, ',', '') . '</span>';
            $tmp .= '</div>';
        } else {
            $tmp .= '<div class="col-xs-1 mw">&nbsp;</div>';
        }
        foreach (Text::$displayitem as $key) {
            $tmp .= Text::getItemLevelL($character, $key);
        }

        $tmp .= '<div class="col-xs-1 mw">&nbsp;</div><div class="col-xs-1 mw">&nbsp;</div>';
        if ($detail) {
            $tmp .= '<div class="col-xs-4 mw2"><a href="' . Utils::getPersoHash($character) . '" class="btn btn-info" role="button">Détails</a></div>';
        }
        return $tmp;
    }

    public function showGURoster(Twig_Environment $env, $faction) {
        if ($faction == 'A') {
            $this->mainandalt = [
                "Absofang" => ["Absocreep", "Absozen", "Absoluthion", 'Absorea'],
                "Myrrdin" => ["Alleister", 'Elrudel'],
                "Banniway" => ["Baniway", "Banniways", "Bänniways", 'Rainbowdashh', "Yawinaab"],
                "Caliwiel" => [],
                "Emmental" => ["Barillium", "Bleuh", 'Chèvredoux', 'Lopinel', 'Morchort', 'Poalhan', 'Roqvefort', 'Rouflakette', 'Cabecou'],
                "Helianthe" => [],
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
                'Fkaïroc' => ['Aache', 'Aibus', 'Borderpat', 'Illidawi'],
                'Absohawok' => ['Absojöllnir', 'Absoriah', 'Absotera'],
                'Sweetiebelle' => ['Bänniways', 'Yawinaab'],
                'Baouss' => ['Oxynocho', 'Taurocho'],
                'Emmental' => ['Aclasse', 'Quefort', 'Meuhane', 'Moumental', 'Wanjin'],
                'Elrudel' => ['Maëlfiel', "Wazzuli", 'Grimmlock', 'Grishnok'],
                'Fabsneaky' => ['Wildfab'],
                'Hirine' => ['Hiejire', 'Pwalhan', 'Halani'],
                'Petrollhahn' => [],
                'Tynae' => ['Vàsea'],
                'Varhos' => [],
                'Dremora' => [],
            ];
        }

        $cool = '';
        $list = array();
        foreach (Utils::getAlts($faction) as $altchek) {
            $list[$altchek->character->name] = true;
        }
        foreach (Utils::getMains($faction) as $character) {
            $cool .= "\n" . '<div class="row">';
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
                    unset($list[$deco->name]);
                } else {
                    unset($list[$alts]);
                    $cool .= $this->displayLowRoster($deco, $faction, true);
                }
                $cool .= "</div>";
            }
            for ($j = $i; $j < 12; $j++) {
                $cool .= '<div class="col-sm">';
                $cool .= "</div>";
            }
            $cool .= "</div>";
            $cool .= "\n" . '<div class="row stuff ' . $character->character->name . '">';
            $cool .= $this->displayStuff(Utils::getDecodedPlayer($character->character->name));
            $cool .= "</div>";
            foreach ($this->mainandalt[$character->character->name] as $main => $alts) {
                $deco = Utils::getDecodedPlayer($alts);
                if (isset($deco->class)) {
                    $cool .= "\n" . '<div class="row stuff ' . $alts . '">';
                    $cool .= $this->displayStuff($deco);
                    $cool .= "</div>";
                }
            }
        }
        $cool .= '<span style="display:none">' . print_r($list, true) . '</span>';
        return $cool;
    }

    public function showGURosterAlts(Twig_Environment $env, $faction) {
        $cool = '<div class="col-xs-10">';
        foreach (Utils::getAlts($faction) as $character) {
            $cool .= $this->displayRoster($character, $faction);
        }
        $cool .= "</div>";
        return $cool;
    }

}
