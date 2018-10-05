<?php

namespace FortheHorde\Twig\Extensions;

class Utils {

    static function getColor($d) {
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

    static function getPersoHash($character) {
        $subpath = explode('/', str_replace("-avatar.jpg", '', $character->thumbnail)); //str_replace("-avatar.jpg", "", );
        $path1 = implode('/', array($subpath[0], $subpath[1]));
        $path2 = $subpath[2];
        $path2 = str_replace("0", "A", $path2);
        $path2 = str_replace("1", "B", $path2);
        $path2 = str_replace("2", "C", $path2);
        $path2 = str_replace("3", "D", $path2);
        $path2 = str_replace("4", "E", $path2);
        $path2 = str_replace("5", "F", $path2);
        $path2 = str_replace("6", "G", $path2);
        $path2 = str_replace("7", "H", $path2);
        $path2 = str_replace("8", "I", $path2);
        $path2 = str_replace("9", "J", $path2);
        return '../persos/' . $path1 . '/' . $path2 . '/index.html';
    }

    static function getCellforPerso($character) {
        $c = '';
        $subpath = explode('/', str_replace("-avatar.jpg", '', $character->character->thumbnail)); //str_replace("-avatar.jpg", "", );
        $path1 = implode('/', array($subpath[0], $subpath[1]));
        $path2 = $subpath[2];
        $path2 = str_replace("0", "A", $path2);
        $path2 = str_replace("1", "B", $path2);
        $path2 = str_replace("2", "C", $path2);
        $path2 = str_replace("3", "D", $path2);
        $path2 = str_replace("4", "E", $path2);
        $path2 = str_replace("5", "F", $path2);
        $path2 = str_replace("6", "G", $path2);
        $path2 = str_replace("7", "H", $path2);
        $path2 = str_replace("8", "I", $path2);
        $path2 = str_replace("9", "J", $path2);
        if ($character->character->level < 10) {
            $c .= '<img src = "http://eu.battle.net/wow/static/images/2d/avatar/' . $character->character->race . '-' . $character->character->gender . '.jpg" class = "persoimgmini" height = "36" width = "36" />' . "<br>";
        } else {
            $c .= '<a href = "../persos/' . $path1 . '/' . $path2 . '/index.html" target = "_blank"><img src = "https://render-eu.worldofwarcraft.com/character/' . $character->character->thumbnail . '" class = "persoimgmini" height = "36" width = "36" /></a>';
        }


        return '<td class = ' . Utils::getColor($character->character->class) . '>' . $c . '<a class = ' . Utils::getColor($character->character->class) . ' target = "_blank" href = "https://worldofwarcraft.com/fr-fr/character/elune/' . $character->character->name . '">' . $character->character->name . '</a></td>

        

        ';
    }

    static function getDecodedPlayer($name) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/" . $name . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!" . $name);
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        return json_decode($json);
    }

    static function getMains($faction) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/guild" . $faction . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!" . $name);
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

    static function getAlts($faction) {
        $path = realpath(__DIR__ . "/../../../../../app/_data/guild" . $faction . ".json");
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

}
