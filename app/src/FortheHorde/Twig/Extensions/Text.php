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
        foreach ($json_decoded->professions as $typeofprof) {
            foreach ($typeofprof as $prof) {
                $trade[$prof->id][$name] = ["rank" => $prof->rank, "max" => $prof->max];
                $trade[$prof->id]["icon"] = $prof->icon;
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
            $cool .= '<th><img src="http://media.blizzard.com/wow/icons/56/' . $atrade['icon'] . '.jpg"/></th>';
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
        if (array_key_exists($this->slotNameID[$key], $json_decoded["items"])) {
            $ii = $json_decoded["items"][$this->slotNameID[$key]]["itemLevel"];
            if ($key == 17 && $json_decoded["items"][$this->slotNameID[17]]["artifactAppearanceId"] != 0 && ( $json_decoded["items"][$this->slotNameID[17]]["artifactAppearanceId"] == $json_decoded["items"][$this->slotNameID[16]]["artifactAppearanceId"])) {
                $art = true;
                $ii = 0;
            }
            $set = '';
            if (!empty($json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["set"])) {
                $set .= '&amp;pcs=' . implode(":", $json_decoded["items"][$this->slotNameID[$key]]["tooltipParams"]["set"]);
            }
            $wowh = '<a href="http://fr.wowhead.com/item=' . $json_decoded["items"][$this->slotNameID[$key]]["id"] . '" rel="item=' .
                    $json_decoded["items"][$this->slotNameID[$key]]["id"] . '&amp;bonus=' .
                    implode(":", $json_decoded["items"][$this->slotNameID[$key]]["bonusLists"])
                    . $set . '"><img src="http://media.blizzard.com/wow/icons/36/' . $json_decoded["items"][$this->slotNameID[$key]]["icon"] . '.jpg"/></a><br>';
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

        /* $tmp = 0;
          $di = 15;
          foreach ($this->displayitem as $key => $value) {
          //echo serialize($json_decoded["items"][$il[$i]]);
          $itemlvl = 0;
          if (array_key_exists($value, $json_decoded["items"])) {
          $itemlvl = $json_decoded["items"][$this->slotNameID[$key]]["itemLevel"];
          }
          if ($key == 17 && array_key_exists($this->slotNameID[17], $json_decoded["items"]) && $json_decoded["items"][$this->slotNameID[17]]["artifactAppearanceId"] != 0 && ( $json_decoded["items"][$this->slotNameID[16]]["artifactAppearanceId"] == $json_decoded["items"][$this->slotNameID[17]]["artifactAppearanceId"])) {
          $itemlvl = 0;
          }
          $tmp += $itemlvl;
          if ($itemlvl == 0) {
          $di--;
          }
          }
          if ($di != 15) {
          $di = 14;
          } */
        return '<td>' . $json_decoded["items"]['averageItemLevel'] . '</td><td>' . $json_decoded["items"]['averageItemLevelEquipped'] . '</td>'; //<td>' . floor($tmp / $di) . '</td>';
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
            $cool .= '<th>' . $this->nameofSlot[$item] . '</th>';
        }
        $cool .= '</tr>';

        foreach ($this->getMains() as $character) {
            $cool .= $this->displayrowItem("mainchar", $character);
        }
        foreach ($this->getAlts() as $character) {
            $cool .= $this->displayrowItem("altchar", $character);
        }
        //$cool .= var_dump($trade);
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
