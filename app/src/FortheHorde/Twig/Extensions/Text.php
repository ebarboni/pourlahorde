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

    private function getIcon($d) {
        switch ($d) {
            case 1:
                return "#C79C6E";
            case 2:
                return "#f48cba";
            case 3:
                return "#ABD473";
            case 4:
                return "#FFF569";
            case 5:
                return "#FFFFFF";
            case 6:
                return "#C41F3B";
            case 7:
                return "#0070DE";
            case 8:
                return "#69CCF0";
            case 9:
                return "#9482C9";
            case 10:
                return "#00FF96";
            case 11:
                return "#FF7D0A";
            case 12:
                return "#A330C9";
        }
        return "cyan";
    }

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
        for ($rank = 0; $rank <= 1; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $trade = $this->getprofession($character->character->name, $trade);
                }
            }
        }
        for ($rank = 2; $rank < 10; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $trade = $this->getprofession($character->character->name, $trade);
                }
            }
        }
        $cool = '<table class="roster">';
        $cool .= "<tr>";
        $cool .= "<th>Name</th>";
        foreach ($trade as $atrade) {
            $cool .= '<th><img src="http://media.blizzard.com/wow/icons/56/' . $atrade['icon'] . '.jpg"/></th>';
        }
        $cool .= "</tr>";
        for ($rank = 0; $rank <= 1; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $cool .= $this->displayrowtrade("mainchar", $character, $trade);
                }
            }
        }
        for ($rank = 2; $rank < 10; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $cool .= $this->displayrowtrade("altchar", $character, $trade);
                }
            }
        }
        //$cool .= var_dump($trade);
        $cool .= "</table>";
        return $cool;
    }

    private function getItemLevelL($name, $i) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/" . $name . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json, true);
        $il = [
            0 => "head",
            1 => "neck",
            2 => "shoulder",
            3 => "back",
            4 => "chest",
            5 => "wrist",
            6 => "mainHand",
            7 => "offHand",
            8 => "hands",
            9 => "waist",
            10 => "legs",
            11 => "feet",
            12 => "finger1",
            13 => "finger2",
            14 => "trinket1",
            15 => "trinket2",
        ];
        $ii = 0;
        //$itemlvl = $json_decoded->items;
        //echo serialize($itemlvl);
        $wowh = '';
        if (array_key_exists($il[$i], $json_decoded["items"])) {
            $ii = $json_decoded["items"][$il[$i]]["itemLevel"];
            $wowh = '<a href="http://fr.wowhead.com/item=' . $json_decoded["items"][$il[$i]]["id"] . '" ><img src="http://media.blizzard.com/wow/icons/36/' . $json_decoded["items"][$il[$i]]["icon"] . '.jpg"/></a><br>';
        }

        if ($ii == 0) {
            return '<td>&nbsp;</td>';
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
        $il = [
            0 => "head",
            1 => "neck",
            2 => "shoulder",
            3 => "back",
            4 => "chest",
            5 => "wrist",
            6 => "mainHand",
            7 => "offHand",
            8 => "hands",
            9 => "waist",
            10 => "legs",
            11 => "feet",
            12 => "finger1",
            13 => "finger2",
            14 => "trinket1",
            15 => "trinket2",
        ];
        $tmp = 0;
        $di = 15;
        for ($i = 0; $i < 15; $i++) {
            //echo serialize($json_decoded["items"][$il[$i]]); 
            $itemlvl = 0;
            if (array_key_exists($il[$i], $json_decoded["items"])) {
                $itemlvl = $json_decoded["items"][$il[$i]]["itemLevel"];
            }
            $tmp += $itemlvl;
            if ($itemlvl == 0) {
                $di--;
            }
        }
        if ($di != 15) {
            $di = 14;
        }
        return '<td>' . $json_decoded["items"]['averageItemLevel'] . '</td><td>' . $json_decoded["items"]['averageItemLevelEquipped'] . '</td><td>' . floor($tmp / $di) . '</td>';
    }

    private function displayrowItem($classname, $character) {
        $cool = '<tr class="' . $classname . '">';
        $cool .= '<td class=' . $this->getColor($character->character->class) . '>' . $character->character->name . '</td>';


        $itemlist = "";
        for ($i = 0; $i < 15; $i++) {
            $itemlist .= $this->getItemLevelL($character->character->name, $i);
        }


        $itemlvl = $this->getItemLevelALL($character->character->name);
        $cool .= $itemlvl . $itemlist;
        $cool .= '</tr>';
        return $cool;
    }

    public function itemLevel(Twig_Environment $env) {

        $path = realpath(__DIR__ . "/../../../../../app/_data/guild.json");

        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);
        $cool = '<table class="roster">';
        $cool .= '<tr>';
        $cool .= "<th>Name</th>";
        $cool .= "<th>Pot.</th>";
        $cool .= "<th>Equ.</th>";
        $cool .= "<th>Calc</th>";
        for ($i = 0; $i < 15; $i++) {
            $cool .= '<th>&nbsp;</th>';
        }
        $cool .= '</tr>';

        for ($rank = 0; $rank <= 1; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $cool .= $this->displayrowItem("mainchar", $character);
                }
            }
        }
        for ($rank = 2; $rank < 10; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $cool .= $this->displayrowItem("altchar", $character);
                }
            }
        }
        //$cool .= var_dump($trade);
        $cool .= "</table>";
        return $cool;
    }

    public function showGURoster(Twig_Environment $env) {

        $path = realpath(__DIR__ . "/../../../../../app/_data/guild.json");

        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $cool = '<div class="col-md-10">';
        $json_decoded = json_decode($json);
        for ($rank = 0; $rank <= 1; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $cool .= $this->displayRoster($character);
                }
            }
        }
        $cool .= "</div>";
        return $cool;
    }

    public function showGURosterAlts(Twig_Environment $env) {

        $path = realpath(__DIR__ . "/../../../../../app/_data/guild.json");

        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $cool = '<div class="col-md-10">';
        $json_decoded = json_decode($json);
        for ($rank = 2; $rank < 10; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $cool .= $this->displayRoster($character);
                }
            }
        }
        $cool .= "</div>";
        return $cool;
    }

    public function itemIcon(Twig_Environment $env, $id) {
        return '<a href="http://fr.wowhead.com/item=' . $id . '" >item</a>';
    }

}
