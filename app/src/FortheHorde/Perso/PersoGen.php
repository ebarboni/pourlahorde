<?php

namespace FortheHorde\Perso;

use Sculpin\Core\Source\DataSourceInterface;
use Sculpin\Core\Source\SourceSet;
use Sculpin\Core\Source\FileSource;
use Symfony\Component\Finder\SplFileInfo;
use Dflydev\Canal\Analyzer\Analyzer;

class PersoGen implements DataSourceInterface {

    public function dataSourceId() {
        return "hordeperso";
    }

    private function doAchar($faction, $character, $sourceSet) {
        $name = $character->character->name;
        if ($faction == 'H') {
            $layout = "defaulth";
        } else {
            $layout = "defaulta";
        }if ($faction == 'H') {
            $guild = "Woodoo Awmy";
        } else {
            $guild = "Pour la Horde";
        }
        @mkdir(realpath(__DIR__ . "/../../../../source") . "/_persos/", 0777, true);
        $t = realpath(__DIR__ . "/../../../../source/_persos") . "/" . $name . ".md";
        $myfile = fopen($t, "w");
        $txt = "---\n" . "layout: " . $layout . "\n" . "title: $guild  \n" . "---\n"
                . "\n"
                . "$name"
                . ""
                . "";
        // echo $txt;


        $chardata = UtilVs::getDecodedPlayer($name);
        //--echo $chardata;
        if (isset($chardata->stats)) {
            foreach ($chardata->stats as $key => $value) {
                $txt .= "\n\n**" . $key . "** => " . $value;
            }
        }
        fwrite($myfile, $txt);
        fclose($myfile);

        $source = new FileSource(
                new Analyzer(), $this, new SplFileInfo(
                $t, "persos/", "persos/" . $name . '.html'
                ), false, true
        );
        $source->canBeFormatted();
        $source->setIsNotGenerated();
        $sourceSet->mergeSource($source);
    }

    public function refresh(SourceSet $sourceSet) {
        // $sourceSet->mergeSource($source);
        //$source = new FileSource();
        // $sourceSet->mergeSource($source);
        $faction = 'H';
        foreach (UtilVs::getPersos($faction) as $character) {
            $this->doAchar($faction, $character, $sourceSet);
        }
        $faction = 'A';
        foreach (UtilVs::getPersos($faction) as $character) {
            $this->doAchar($faction, $character, $sourceSet);
        }
    }

}
