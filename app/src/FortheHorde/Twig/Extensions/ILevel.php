<?php

namespace FortheHorde\Twig\Extensions;

use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class ILevel extends Twig_Extension {

    public function getFunctions() {
        $functions = array(
            new Twig_SimpleFunction('itemlevel', array($this, 'itemLevel'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
        return $functions;
    }

// count for ilevel
    private $displayitem = [1, 2, 3, 4, 5, 9, 16, 17, 10, 6, 7, 8, 11, 12, 13, 14];
    private $slotNameID = [
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

    private function getItemLevelL($name, $key) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/" . $name . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json, true);

        $ii = 0;
        //$itemlvl = $json_decoded->items;
        //echo serialize($itemlvl);
        $wowh = '';
        $art = false;
        if (@array_key_exists($this->slotNameID[$key], $json_decoded["items"])) {
            $ii = $json_decoded["items"][$this->slotNameID[$key]]["itemLevel"];
            if ($key == 17 && $json_decoded["items"][$this->slotNameID[17]]["artifactAppearanceId"] != 0 && ( $json_decoded["items"][$this->slotNameID[17]]["artifactAppearanceId"] == $json_decoded["items"][$this->slotNameID[16]]["artifactAppearanceId"])) {
                $art = true;
                $ii = 0;
            }
            $set = '';
            if (!empty($json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["set"])) {
                $set .= '&amp;pcs=' . implode(":", $json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["set"]);
            }
            $ench = '';
            if (!empty($json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["enchant"])) {
                $ench .= '&amp;ench=' . $json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["enchant"];
            }
            $gem = '';

            if (!empty($json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["gem0"])) {
                $gem .= '&amp;gems=';

                $gem .= $json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["gem0"];
                for ($i = 1; $i < 5; $i++) {
                    if (!empty($json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["gem" . $i])) {
                        $gem .= ':' . $json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["gem" . $i];
                    }
                }
            }
            $aze = '';
            if (!empty($json_decoded["items"][$this->slotNameID[$key]]["azeriteEmpoweredItem"]['azeritePowers'][0])) {
                $aze .= '&amp;azerite-powers=';

                $aze .= $json_decoded["class"];
                for ($i = 1; $i < 4; $i++) {
                    if (!empty($json_decoded["items"][$this->slotNameID[$key]]["azeriteEmpoweredItem"]['azeritePowers'][$i])) {
                        $aze .= ':' . $json_decoded["items"][$this->slotNameID[$key]]["azeriteEmpoweredItem"]['azeritePowers'][$i]['id'];
                    }
                }
            }

            $wowh = '<a href="http://fr.wowhead.com/item=' . $json_decoded["items"][$this->slotNameID[$key]]["id"] . '" rel="item=' .
                    $json_decoded["items"][$this->slotNameID[$key]]["id"] . '&amp;bonus=' .
                    implode(":", $json_decoded["items"][$this->slotNameID[$key]]["bonusLists"])
                    . $set . $gem . $ench . $aze . '&amp;lvl=' . $json_decoded["level"] . '"><img class="wowitem q' . $json_decoded["items"][$this->slotNameID[$key]]['quality'] . '" src="https://render-eu.worldofwarcraft.com/icons/36/' . $json_decoded["items"][$this->slotNameID[$key]]["icon"] . '.jpg"/></a><br>';
        }

        if ($ii == 0 && !$art) {
            return '<td>&nbsp;</td>';
        } else if ($ii == 0 && $art) {
            return '<td class="anitem">' . $wowh . '&nbsp;</td>';
        } else {
            if ($aze == '') {
                return '<td class="anitem">' . $wowh . $ii . '</td>';
            } else {
                return '<td class="anitem anitemaze">' . $wowh . $ii . '</td>';
            };
        }
        //<a href="http://fr.wowhead.com/item=' . $id . '" >item</a>
    }

    private function getItemLevelALL($name) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/" . $name . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $tmp = "";
        $json_decoded = json_decode($json, true);
        if (@array_key_exists('items', $json_decoded)) {
            $tmp = '<td>' . $json_decoded["items"]['averageItemLevel'] . '</td><td>' . $json_decoded["items"]['averageItemLevelEquipped'] . '</td>';
        } //<td>' . floor($tmp / $di) . '</td>';
        else {
            $tmp = '<td></td><td></td>';
        }
        if (@$json_decoded["items"]['neck']['azeriteItem']['azeriteLevel'] > 0) {
            $tmp .= '<td>' . $json_decoded["items"]['neck']['azeriteItem']['azeriteLevel'];
            $tmp .= '<br>' . '<span class="anitem">' . number_format(100 * ($json_decoded["items"]['neck']['azeriteItem']['azeriteExperience'] / $json_decoded["items"]['neck']['azeriteItem']['azeriteExperienceRemaining']), 2, ',', '') . '</span>';
            $tmp .= '</td>';
        } else {
            $tmp .= '<td></td>';
        }
        return $tmp;
    }

    private function displayAudit($character) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/" . $character . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json, true);
        $audit = '';
        if (@array_key_exists('audit', $json_decoded)) {
            $itemesocket = $json_decoded["audit"]['itemsWithEmptySockets'];
            $itemenchant = $json_decoded["audit"]['unenchantedItems'];
            // var_dump($itemesocket);
            // tete 1
            // collier 2 
            // epau 3
            // cape 16
            $itemslot = [
                0 => 'casque',
                1 => 'collier',
                2 => 'spalières',
                3 => '1inconnu',
                4 => 'torse',
                5 => 'ceinture',
                6 => 'jambières',
                7 => 'bottes',
                8 => 'manchettes',
                9 => 'gants',
                10 => 'anneau',
                11 => 'anneau',
                12 => 'bijou',
                13 => 'bijou',
                14 => 'cape',
                15 => 'main gauche',
                16 => 'main droite?'];
            $auditgem = [];
            for ($i = 0; $i < 20; $i++) {
                if (array_key_exists($i, $itemesocket) && ($itemesocket[$i] >= 1)) {
                    $auditgem[] = $itemslot[$i];
                }
            }
            if (sizeof($auditgem) > 0) {
                $audit .= 'Gemmes manquantes sur : ' . implode(',', $auditgem) . '<br>';
            }
            $auditenchant = [];
            $enchantable = ['14', '10', '11', '2'];
            foreach ($enchantable as $value) {
                if (@array_key_exists($value, $itemenchant)) {
                    $auditenchant[] = $itemslot[$value];
                }
            }
            if (sizeof($auditenchant) > 0) {
                $audit .= 'Enchants  manquants sur : ' . implode(',', $auditenchant);
            }
            /* if (array_key_exists(2, $itemesocket) && ($itemesocket[2] >= 1)) {
              $audit .= 'Gemme sur epaule<br>';
              } if (array_key_exists(4, $itemesocket) && ($itemesocket[4] >= 1)) {
              $audit .= 'Gemme sur torse<br>';
              } if (array_key_exists(5, $itemesocket) && ($itemesocket[5] >= 1)) {
              $audit .= 'Gemme sur ceinture<br>';
              } if (array_key_exists(8, $itemesocket) && ($itemesocket[8] >= 1)) {
              $audit .= 'Gemme sur poignet<br>';
              }if (array_key_exists(10, $itemesocket) && ($itemesocket[10] >= 1)) {
              $audit .= 'Gemme sur anneau<br>';
              } if (array_key_exists(11, $itemesocket) && ($itemesocket[11] >= 1)) {
              $audit .= 'Gemme sur anneau<br>';
              }
              if (array_key_exists(14, $itemesocket) && ($itemesocket[14] >= 1)) {
              $audit .= 'Gemme sur cape<br>';
              } */
            // echo serialize($itemesocket);
        }
        return '<td class="audit">' . $audit . '</td>';
    }

    private function displayrowItem($classname, $character) {
        $cool = '<tr class="' . $classname . '">';
        $cool .= Utils::getCellforPerso($character); //';


        $itemlist = "";
        foreach ($this->displayitem as $key) {
            $itemlist .= $this->getItemLevelL($character->character->name, $key);
        }


        $itemlvl = $this->getItemLevelALL($character->character->name);
        $cool .= $itemlvl . $itemlist;
        $cool .= $this->displayAudit($character->character->name);
        $cool .= '</tr>';
        return $cool;
    }

    public function itemLevel(Twig_Environment $env, $faction) {
        $cool = '<table class="roster">';
        $cool .= '<tr>';
        $cool .= "<th>Name</th>";
        $cool .= "<th>Pot.</th>";
        $cool .= "<th>Equ.</th>";
        $cool .= "<th>Azerite</th>";
        //$cool .= "<th>Calc</th>";
        foreach ($this->displayitem as $item) {
            $cool .= '<th>&nbsp;</th>';
        }
        $cool .= '<th>Audit</th>';
        $cool .= '</tr>';

        foreach (Utils::getMains($faction) as $character) {
            $cool .= $this->displayrowItem("mainchar", $character, $faction);
        }

        foreach (Utils::getAlts($faction) as $character) {
            $cool .= $this->displayrowItem("altchar", $character, $faction);
        }
        $cool .= "</table>";
        return $cool;
    }

}
