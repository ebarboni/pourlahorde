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

    private $tradeall = [
// herbo
        "182" => ["2556", "2555", "2554", "2553", "2552", "2551", "2550", "2549"],
        //minage
        "186" => ["2572", "2571", "2570", "2569", "2568", "2567", "2566", "2565"],
        //depecage
        "393" => ["2564", "2563", "2562", "2561", "2560", "2559", "2558", "2557"],
        // alchimie
        "171" => ["2485", "2484", "2483", "2482", "2481", "2480", "2479", "2478"],
        // calligraphie
        "773" => ["2514", "2513", "2512", "2511", "2510", "2509", "2508", "2507"],
        //forge
        "164" => ["2477", "2476", "2475", "2474", "2473", "2472", "2454", "2437"],
        // inge
        "202" => ["2506", "2505", "2504", "2503", "2502", "2501", "2500", "2499"],
        // joa
        "755" => ["2524", "2523", "2522", "2521", "2520", "2519", "2518", "2517"],
        // travail du cuir
        "165" => ["2532", "2531", "2530", "2529", "2528", "2527", "2526", "2525"],
        // encha
        "333" => ["2494", "2493", "2492", "2491", "2489", "2488", "2487", "2486"],
        // couture
        "197" => ["2540", "2539", "2538", "2537", "2536", "2535", "2534", "2533"],
        // cuisine
        //"185" => ["2548", "2547", "2546", "2545", "2544", "2543", "2542", "2541"],
        //first aid "129" => [],
// peche
        //"356" => ["2592", "2591", "2590", "2589", "2588", "2587", "2586", "2585"],
        //cuisine
        "185" => ["2548", "2547", "2546", "2545", "2544", "2543", "185", "2541"],
        //peche
        "356" => ["2592", "2591", "2590", "2589", "2588", "2587", "356", "2585"],
        "794" => ["794", "794", "794", "794", "794", "794", "794", "794"]
    ];

    private function getprofession($name, $trade) {
        $json_decoded = Utils::getDecodedPlayer($name);
        // no profession exit
        if (!property_exists($json_decoded, 'professions')) {
            return $trade;
        }
        foreach ($json_decoded->professions as $typeofprof) {
            foreach ($typeofprof as $prof) {
                $trade[$prof->id][$name] = ["rank" => $prof->rank, "max" => $prof->max];
                $trade[$prof->id]["icon"] = @$prof->icon;
            }
        }
        return $trade;
    }

    private function displayrowtrade($cssclass, $character, $trade) {
        $cool = '<tr class="' . $cssclass . '">';
        $cool .= '<td class=' . Utils::getColor($character->character->class) . '>' . $character->character->name . '</td>';
        foreach ($this->tradeall as $key1 => $atrade1) {
            $currentkey = $atrade1[7];
            // echo "$currentkey,";
            //if ($character->character->name = "Illidawi") {
            // echo "<br>o:" . $currentkey . "," . $key1 . "::" . $character->character->name;
            //   } 
            $found = false;
            foreach ($trade as $key => $atrade) {
                // echo "$key;+";
                if ($currentkey == $key && @is_array($atrade[$character->character->name])) {
                    $found = true;
                    $cool .= '<td';
                    if (@is_array($atrade[$character->character->name])) {
                        if ($atrade[$character->character->name]["rank"] >= $atrade[$character->character->name]["max"]) {
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
            }
            //echo "<br>f:" . $found;
            if (!$found) {
                //echo "$key";
                // echo "$currentkey;;;;;";
                foreach ($trade as $key => $atrade) {
                    // echo "<br>b:" . $key . "," . $key1 . "::" . $character->character->name;
                    if ($key1 == $key && @is_array($atrade[$character->character->name])) {
                        $found = true;
                        $cool .= '<td';
                        if (@is_array($atrade[$character->character->name])) {
                            if ($atrade[$character->character->name]["rank"] >= $atrade[$character->character->name]["max"]) {
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
                }
            }
            if (!$found) {
                $cool .= '<td></td>';
            }
        }
        $cool .= "</tr>";
        return $cool;
    }

    private function getTrade($faction) {
        $trade = array();
        foreach (Utils::getMains($faction) as $character) {
            $trade = $this->getprofession($character->character->name, $trade);
        }
        foreach (Utils::getAlts($faction) as $character) {
            $trade = $this->getprofession($character->character->name, $trade);
        }
        return $trade;
    }

    public function showTrade(Twig_Environment $env, $faction) {

// 182	186	393	171	773	164	202	755	165	333	197	185	129	356	794	

        $trade = $this->getTrade($faction);

        $cool = '<table class="roster">';
        $cool .= "<tr>";
        $cool .= "<th>Name</th>";
        foreach (array_keys($this->tradeall) as $key) {
            $cool .= '<th><img class="tradeskill" src="https://render-eu.worldofwarcraft.com/icons/56/' . $trade[$key]['icon'] . '.jpg"/></th>';
        }
        $cool .= "</tr>";
        foreach (Utils::getMains($faction) as $character) {
            $cool .= $this->displayrowtrade("mainchar", $character, $trade);
        }
        foreach (Utils::getAlts($faction) as $character) {
            $cool .= $this->displayrowtrade("altchar", $character, $trade);
        }
        $cool .= "</table>";
        return $cool;
    }

}
