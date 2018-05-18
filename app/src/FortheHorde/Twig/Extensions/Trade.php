<?php

namespace FortheHorde\Twig\Extensions;

use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class Trade extends Twig_Extension {

    public function getFunctions() {
        $functions = array(
            new Twig_SimpleFunction('showTrade', array($this, 'showTrade'), array('needs_environment' => true, 'is_safe' => array('html'))),
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
        $cool .= '<td class=' . Utils::getColor($character->character->class) . '>' . $character->character->name . '</td>';
        foreach ($trade as $atrade) {
            $cool .= '<td';
            if (@is_array($atrade[$character->character->name])) {
                if ($atrade[$character->character->name]["rank"] >= 800) {
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

    public function showTrade(Twig_Environment $env,$faction) {

        $path = realpath(__DIR__ . "/../../../../../app/_data/guild" . $faction . ".json");

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
        foreach (Utils::getMains($faction) as $character) {
            $trade = $this->getprofession($character->character->name, $trade);
        }
        foreach (Utils::getAlts($faction) as $character) {
            $trade = $this->getprofession($character->character->name, $trade);
        }

        $cool = '<table class="roster">';
        $cool .= "<tr>";
        $cool .= "<th>Name</th>";
        foreach ($trade as $atrade) {
            $cool .= '<th><img class="tradeskill" src="http://media.blizzard.com/wow/icons/56/' . $atrade['icon'] . '.jpg"/></th>';
        }
        $cool .= "</tr>";
        foreach (Utils::getMains($faction) as $character) {
            $cool .= $this->displayrowtrade("mainchar", $character, $trade);
        }
        foreach (Utils::getAlts($faction) as $character) {
            $cool .= $this->displayrowtrade("altchar", $character, $trade);
        }
        //$cool .= var_dump($trade);
        $cool .= "</table>";
        return $cool;
    }

}
