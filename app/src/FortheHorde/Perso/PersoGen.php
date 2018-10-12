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
            $layout = "icononlyh";
        } else {
            $layout = "icononlya";
        }if ($faction == 'H') {
            $guild = "Woodoo Awmy";
        } else {
            $guild = "Pour la Horde";
        }

        //elune/83/163331155-avatar.jpg
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


        @mkdir(realpath(__DIR__ . "/../../../../source") . "/persos/" . $path1, 0777, true);
        $t = realpath(__DIR__ . "/../../../../source/persos") . "/" . $path1 . '/' . $path2 . ".md";
        $myfile = fopen($t, "w");
        $txt = "---\n" . "layout: " . $layout . "\n" . "title: $guild - $name  \n" . "---\n"
                . "\n"
                . ""
                . ""
                . " {{ showCharDetails('$name') }} \n";
        // echo $txt;


        /* $chardata = UtilVs::getDecodedPlayer($name);
          //--echo $chardata;
          if (isset($chardata->stats)) {
          foreach ($chardata->stats as $key => $value) {
          $txt .= "\n\n**" . $key . "** => " . $value;
          }
          } */
        fwrite($myfile, $txt);
        fclose($myfile);
        /* echo "\n";
          echo "\n";
          echo $t;
          echo "\n";
          echo "persos/" . $path1;
          echo "persos/" . $path1 . '/' . $path2 . '.html';
          echo "\n"; */
        $source = new FileSource(
                new Analyzer(), $this, new SplFileInfo(
                $t, "persos/" . $path1 . "/", "persos/" . $path1 . '/' . $path2 . '.html'
                ), true, true
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
