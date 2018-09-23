<?php

namespace FortheHorde\Perso;

use Sculpin\Core\Source\DataSourceInterface;
use Sculpin\Core\Source\SourceSet;
use FortheHorde\Twig\Extensions\Utils;

class UtilVs {

    static function getDecodedPlayer($name) {
        $path = realpath(__DIR__ . "/../../../../app/_data/" . $name . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        return json_decode($json);
    }

    static function getPersos($faction) {
        $path = realpath(__DIR__ . "/../../../../app/_data/guild" . $faction . ".json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);
        $mains = array();
        for ($rank = 0; $rank <= 0; $rank++) {
            foreach ($json_decoded->members as $character) {
                if ($character->rank == $rank) {
                    $mains[$character->character->name] = $character;
                }
            }
        }
        ksort($mains);

        return $mains;
    }

}
