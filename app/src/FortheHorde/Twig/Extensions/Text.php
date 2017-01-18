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
            new Twig_SimpleFunction('showTrade', array($this, 'showTrade'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new Twig_SimpleFunction('item', array($this, 'itemIcon'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new Twig_SimpleFunction('itemlevel', array($this, 'itemLevel'), array('needs_environment' => true, 'is_safe' => array('html'))),
                //  new Twig_SimpleFunction('showItem', array($this, 'showTrade'), array('needs_environment' => true, 'is_safe' => array('html'))),
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
    private $nameofSlot = [
        1 => "casque",
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

    private function getColor($d) {
        switch ($d) {
            case 1:
                return "warrior";
            case 2:
                return "paladin";
            case 3:
                return "hunter";
            case 4:
                return "rogue";
            case 5:
                return "priest";
            case 6:
                return "dk";
            case 7:
                return "shaman";
            case 8:
                return "mage";
            case 9:
                return "warlock";
            case 10:
                return "monk";
            case 11:
                return "druid";
            case 12:
                return "dh";
        }
        return "cyan";
    }

    private function getMains() {
        $path = realpath(__DIR__ . "/../../../../../app/_data/guild.json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);
        $mains = array();
        for ($rank = 0; $rank <= 1; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $mains[$character->character->name] = $character;
                }
            }
        }
        ksort($mains);
        return $mains;
    }

    private function getAlts() {
        $path = realpath(__DIR__ . "/../../../../../app/_data/guild.json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);
        $alts = array();
        for ($rank = 2; $rank < 10; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $alts[$character->character->name] = $character;
                }
            }
        }
        ksort($alts);
        return $alts;
    }

    private function displayRoster($charobject) {
        $color = $this->getColor($charobject->character->class);
        $c = '<div class="aperso">';
        $c .= '<img src="https://render-eu.worldofwarcraft.com/character/' . $charobject->character->thumbnail . '" class="persoimg" height="84" width="84" />';
        $c .= '<span class="pname ' . $color . '">' . $charobject->character->name;
        if ($charobject->character->level < 110) { // max level
            $c .= '<span class="level">' . $charobject->character->level . '</span>';
        }
        $c .= '</span>';
        $c .= '</div>';
        return $c;
    }

    private function getprofession($name, $trade) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/" . $name . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);
        if (property_exists($json_decoded, 'professions')) {
            foreach ($json_decoded->professions as $typeofprof) {
                foreach ($typeofprof as $prof) {
                    $trade[$prof->id][$name] = ["rank" => $prof->rank, "max" => $prof->max];
                    $trade[$prof->id]["icon"] = $prof->icon;
                }
            }
        }
        return $trade;
    }

    private function displayrowtrade($cssclass, $character, $trade) {
        $cool = '<tr class="' . $cssclass . '">';
        $cool .= '<td class=' . $this->getColor($character->character->class) . '>' . $character->character->name . '</td>';
        foreach ($trade as $atrade) {
            $cool .= '<td';
            if (@is_array($atrade[$character->character->name])) {
                if ($atrade[$character->character->name]["rank"] == 800) {
                    $cool .= ' class="trademax"';
                }
            }
            $cool .= '>';
            if (@is_array($atrade[$character->character->name])) {
                if ($atrade[$character->character->name]["rank"] > 0) {
                    $cool .= $atrade[$character->character->name]["rank"];
                }
            } else {
                
            }
            if (@is_array($atrade[$character->character->name])) {
                if ($atrade[$character->character->name]["max"] < 800 && $atrade[$character->character->name]["max"] > 0) {
                    $cool .= '<span class="notmaxed">' . $atrade[$character->character->name]["max"] . '</span>';
                }
            }
            $cool .= '</td>';
        }
        $cool .= "</tr>";
        return $cool;
    }

    public function showTrade(Twig_Environment $env) {

        $path = realpath(__DIR__ . "/../../../../../app/_data/guild.json");

        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        // 182	186	393	171	773	164	202	755	165	333	197	185	129	356	794	

        $trade = ["182" => [],
            "186" => [],
            "393" => [],
            "171" => [],
            "773" => [],
            "164" => [],
            "202" => [],
            "755" => [],
            "165" => [],
            "333" => [],
            "197" => [],
            "185" => [],
            "129" => [],
            "356" => [],
            "794" => []];
        $json_decoded = json_decode($json);
        foreach ($this->getMains() as $character) {
            $trade = $this->getprofession($character->character->name, $trade);
        }
        foreach ($this->getAlts() as $character) {
            $trade = $this->getprofession($character->character->name, $trade);
        }

        $cool = '<table class="roster">';
        $cool .= "<tr>";
        $cool .= "<th>Name</th>";
        foreach ($trade as $atrade) {
            $cool .= '<th><img class="tradeskill" src="http://media.blizzard.com/wow/icons/56/' . $atrade['icon'] . '.jpg"/></th>';
        }
        $cool .= "</tr>";
        foreach ($this->getMains() as $character) {
            $cool .= $this->displayrowtrade("mainchar", $character, $trade);
        }
        foreach ($this->getAlts() as $character) {
            $cool .= $this->displayrowtrade("altchar", $character, $trade);
        }
        //$cool .= var_dump($trade);
        $cool .= "</table>";
        return $cool;
    }

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
            $wowh = '<a href="http://fr.wowhead.com/item=' . $json_decoded["items"][$this->slotNameID[$key]]["id"] . '" rel="item=' .
                    $json_decoded["items"][$this->slotNameID[$key]]["id"] . '&amp;bonus=' .
                    implode(":", $json_decoded["items"][$this->slotNameID[$key]]["bonusLists"])
                    . $set . $gem . $ench . '&amp;lvl=' . $json_decoded["level"] . '"><img class="wowitem q' . $json_decoded["items"][$this->slotNameID[$key]]['quality'] . '" src="http://media.blizzard.com/wow/icons/36/' . $json_decoded["items"][$this->slotNameID[$key]]["icon"] . '.jpg"/></a><br>';
        }

        if ($ii == 0 && !$art) {
            return '<td>&nbsp;</td>';
        } else if ($ii == 0 && $art) {
            return '<td class="anitem">' . $wowh . '&nbsp;</td>';
        } else {
            return '<td class="anitem">' . $wowh . $ii . '</td>';
        }
        //<a href="http://fr.wowhead.com/item=' . $id . '" >item</a>
    }

    private function getItemLevelALL($name) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/" . $name . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json, true);
        if (@array_key_exists('items', $json_decoded)) {
            return '<td>' . $json_decoded["items"]['averageItemLevel'] . '</td><td>' . $json_decoded["items"]['averageItemLevelEquipped'] . '</td>';
        } //<td>' . floor($tmp / $di) . '</td>';
        else {
            return '<td></td><td></td>';
        }
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
                2 => 'spalieres',
                4 => 'torse',
                5 => 'ceinture',
                9 => 'gants',
                8 => 'manchettes',
                7 => 'bottes',
                10 => 'anneau',
                11 => 'anneau',
                12 => 'bijou',
                14 => 'cape',
                15 => 'main gauche'];
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
        $cool .= '<td class=' . $this->getColor($character->character->class) . '>' . $character->character->name . '</td>';


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

    public function itemLevel(Twig_Environment $env) {
        $cool = '<table class="roster">';
        $cool .= '<tr>';
        $cool .= "<th>Name</th>";
        $cool .= "<th>Pot.</th>";
        $cool .= "<th>Equ.</th>";
        //$cool .= "<th>Calc</th>";
        foreach ($this->displayitem as $item) {
            $cool .= '<th>&nbsp;</th>';
        }
        $cool .= '<th>Audit</th>';
        $cool .= '</tr>';

        foreach ($this->getMains() as $character) {
            $cool .= $this->displayrowItem("mainchar", $character);
        }

        foreach ($this->getAlts() as $character) {
            $cool .= $this->displayrowItem("altchar", $character);
        }
        $cool .= "</table>";
        return $cool;
    }

    public function showGURoster(Twig_Environment $env) {
        $cool = '<div class="col-md-10">';
        foreach ($this->getMains() as $character) {
            $cool .= $this->displayRoster($character);
        }
        $cool .= "</div>";
        return $cool;
    }

    public function showGURosterAlts(Twig_Environment $env) {
        $cool = '<div class="col-md-10">';
        foreach ($this->getAlts() as $character) {
            $cool .= $this->displayRoster($character);
        }
        $cool .= "</div>";
        return $cool;
    }

    public function itemIcon(Twig_Environment $env, $id) {
        return '<a href="http://fr.wowhead.com/item=' . $id . '" >item</a>';
    }

}
