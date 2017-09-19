<?php

namespace FortheHorde\Twig\Extensions;

use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class Text extends Twig_Extension {

    public function getFunctions() {
        $functions = array(
            new Twig_SimpleFunction('showMain', array($this, 'showGURoster'), array('needs_environment' => true, 'is_safe' => array('html'))),
            new Twig_SimpleFunction('showRawAchievement', array($this, 'showGUAchievement'), array('needs_environment' => true, 'is_safe' => array('html'))),
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
        $d = '';
        //'Shiromi'
        $mainandalt = [
            "Absozen" => ["Absocreep", "Absofang", "Absohavok", "Absoluthion",'Absorea'],
            "Arkeaiin" => ["Alleister","Khalima"],
            "Banniway" => ["Baniway", "Baniways", "Banniways", "Bäniways", "Bänniways","Yawinaab"],
            "Caliwiel" => [],
            "Emmental" => ["Barillium", "Bleuh", 'Lopinel', 'Morchort', 'Poalhan', 'Rhonium', 'Roqvefort','Rouflakette', 'Zefresis'],
            "Helianthe" => ['Ireene', 'Nehelina'],
            "Hylidev" => ['Feorn'],
            "Krows" => [],
            "Laycka" => ['Lioz', 'Liøz','Shiromi'],
            "Poilant" => ['Fourras', 'Panoramix', 'Raedsyndar', 'Sharisad', 'Widdershins'],
            "Poilhan" => ['Fkaffe', 'Fkoil', 'Iakf', 'Narjhhan'],
            "Rengonocho" => ['Baouss', 'Berlonocho', 'Crinacha','Gimeno', 'Zapova'],
            "Rhtaar" => ['Hvance', 'Yllaltan'],
            "Saethia" => [],
            "Shuntor" => ['Kathor','Shunthor'],
            "Sneakyzouz" => ['Elementzouz'],
            "Taoling" => ['Gaiana', 'Meï']
        ];
        foreach ($mainandalt as $main => $alts) {
            if ($main == $charobject->character->name) {
                // main
                $d = 'main';
            }
            if (in_array($charobject->character->name, $alts)) {
                $d = 'alt ' . $main;
            }
        }
        $c = '<div class="aperso ' . $d . ' ">';
        if ($charobject->character->level < 10) {
            $c .= '<img src="http://eu.battle.net/wow/static/images/2d/avatar/' . $charobject->character->race . '-' . $charobject->character->gender . '.jpg" class="persoimg" height="84" width="84" />';
        } else {
            $c .= '<img src="https://render-eu.worldofwarcraft.com/character/' . $charobject->character->thumbnail . '" class="persoimg" height="84" width="84" />';
        }

        $c .= '<span class="pname ' . $color . '"><span class="ppname">' . $charobject->character->name . '</span>';
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
                13 => '2inconnu',
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

    private function getCompletedAchievement() {
        $path = realpath(__DIR__ . "/../../../../../app/_data/guild.json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);
        $mains = array();
        foreach ($json_decoded->achievements->achievementsCompleted as $completed) {
            $mains[$completed] = $completed;
        }
        ksort($mains);
        return $mains;
    }

    private function getCompletedCriterias() {
        $path = realpath(__DIR__ . "/../../../../../app/_data/guild.json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);
        $mains = array();

        $i = 0;
        foreach ($json_decoded->achievements->criteria as $criterariaid) {
            $mains[$criterariaid] = $json_decoded->achievements->criteriaQuantity[$i];
            $i++;
        }
        ksort($mains);
        return $mains;
    }

    private function getAchievementJSon() {
        $path = realpath(__DIR__ . "/../../../../../app/_data/achievementguild.json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);

        return $json_decoded;
    }

    private $critids = array();
    private $critidsm = array();

    private function displayAchievement($ach, $cri, $done) {
        $doneclass = "";
        if (!$done) {
            $doneclass .= ' achdone';
        }
        $tmp = '<div class=" anach ' . $doneclass . '">';
        $icon = '';
        if (isset($ach->icon)) {
            $icon = '<img class="achimage" src="http://media.blizzard.com/wow/icons/56/' . $ach->icon . '.jpg"/>';
        }
        $tmp .= $icon . '<div class="achtitle">' . $ach->title . '' . '</div><div class="achdescription">' . $ach->description . '</div>';
        $tmp .= '<ul class=" aul">';

        foreach ($ach->criteria as $acrit) {
            $crid = $acrit->id;
            if ($crid == 15327 || $crid == 14794) {
                continue;
            }

            $val = '0';
            $status = 'critincomp';
            $cmp = false;
            if (array_key_exists($crid, $cri)) {
                $val = $cri[$crid];
                $cpm = true;
                $status = 'critcomp';
            }
            $comp = false;
            if ($val >= $acrit->max) {
                $status = 'critcomp';
                $comp = true;
            } else {
                $status = 'critincomp';
            }
            $icon = '';
            if (isset($acrit->icon)) {
                $icon = '<img class="tradeskill" src="http://media.blizzard.com/wow/icons/56/' . $acrit->icon . '.jpg"/>';
            }
            if (in_array($acrit->id, array(14500, 14805))) {

                $val = $val . " / " . $acrit->max;
                // boolean
            } elseif (in_array($acrit->id, array(14576, 14577, 14578, 14579, 14580, 19473, 14581,
                        21189, 21191, 21192, 21193, 21194, 21195, 21197,
                        15524, 15525, 15526, 15527, 15528, 15529, 15530, 15531, 15532, 15533, 15544,
                        15545, 15546, 15547, 15548, 15549, 15550, 15551, 15552, 15553, 15564, 15565, 15566,
                        15567, 15568, 15569, 15570, 15571, 15572, 15573, 15584, 15585, 15586, 15587, 15588,
                        15589, 15590, 15591, 15592, 15593, 13905, 15485, 15486, 15487, 23220, 15488, 15489,
                        15490, 15491, 15492, 15493, 15494, 15495, 15496, 15497, 23219, 15498, 15499, 15500,
                        15501, 15502, 15503, 15504, 15505, 15506, 15507, 23218, 15508, 15509, 15510, 15511,
                        15512, 15513, 15514, 15515, 15516, 15517, 23217, 15518, 15519, 15520, 15521, 15522,
                        15523, 15691, 19303, 14701, 14702, 14703, 14704, 14705, 14706, 19285, 14662, 14663,
                        14664, 14665, 14666, 14667, 14668, 19289, 19292, 19290, 19293, 19295, 19294, 19291,
                        19279, 14655, 14656, 14657, 14658, 14659, 14660, 14661, 19286, 14669, 14670, 14671,
                        14672, 14673, 14674, 14675, 19287, 14685, 14677, 14678, 14679, 14680, 14681, 14682,
                        14683, 14684, 19288, 14686, 14687, 14688, 14689, 14690, 14691, 14692, 14693, 14694,
                        14695, 14696, 14697, 14698, 14699, 14700, 14320, 14588, 14322, 14324, 14329, 14330,
                        14331, 14333, 14334, 14335, 14336, 14337, 14338, 22241, 14341, 14342, 14344, 14345,
                        14346, 14343, 14317, 14589, 14319, 14321, 14323, 20987, 14332, 14354, 14371, 14355,
                        14356, 14358, 14360, 14361, 14362, 14363, 14365, 14367, 14368, 14373, 14375, 14376,
                        14377, 14378, 14379, 14380, 14381, 14357, 14359, 14364, 14366, 14369, 14370, 14386,
                        14388, 14390, 14391, 14393, 14395, 14397, 14401, 14403, 14405, 14387, 14389, 14392,
                        14394, 14396, 14404, 14241, 14242, 14240, 14243, 14244, 14249, 15694, 14250, 15693,
                        14251, 15692, 14482, 14487, 16208, 14490, 16865, 16866, 18476, 18477, 14483, 14484,
                        14485, 14486, 14488, 18475, 14001, 14002, 14003, 14004, 14005, 14006, 14012, 14146,
                        14007, 14008, 14009, 14010, 15480, 18478, 18479, 18096, 18097, 18098, 18099, 18100,
                        18101, 18102, 18480, 18481, 18482, 18483, 18484, 18485, 18486, 18487, 15695, 15696,
                        15697, 15698, 15699, 15700, 15701, 15702, 18103, 18104, 18489, 18490, 19684, 19676,
                        19677, 19678, 19483, 19681, 19682, 19683, 19679, 22384, 19485, 19486, 19487, 19114,
                        19488, 19489, 19490, 19491, 19630, 19492, 19493, 19651, 19652, 19494, 19495, 19566,
                        19567, 19653, 19498, 19496, 22908, 22909, 23072, 23073, 23074, 23075, 23076, 23077,
                        23078, 23079, 23080, 23081, 23082, 23083, 23070, 23084, 24147, 24148, 24149, 24150,
                        23692, 23693, 23694, 23695, 23696, 23697, 23698, 23699, 23700, 23702, 23703, 23701,
                        23704, 23705, 23706, 25509, 25510, 25511, 25512, 25514, 25516, 25513, 25515, 25769,
                        25770, 25713, 25714, 25715, 25716, 25717, 25718, 25719, 25712, 25720, 25721, 25722,
                        25723, 25724, 25725, 25726, 25727, 25728, 25729, 25566, 25567, 28556, 28557, 34531,
                        31470, 31471, 31472, 31473, 31474, 31475, 31476, 31477, 31478, 31479, 31480, 34849,
                        32362, 34530, 31481, 31482, 31486, 31484, 31487, 31485, 31483, 34816, 34818, 34817,
                        31493, 31492, 31488, 31490, 31491, 31495, 31497, 31496, 31494, 31489, 36533, 36534,
                        36535, 36536, 36537, 36538, 36539, 36540, 36541, 31468, 34819, 31469, 36542, 13882,
                        13883, 13884, 13885, 13886, 13887, 13888, 13889, 13890, 13891, 13892, 13893, 13894,
                        13895, 13896, 13897, 13898, 13899, 13900, 14199, 14198, 14200, 14202, 14203, 14204,
                        14205, 14206, 14207, 14208, 14209, 14210, 14211, 14212, 14213, 14214, 14215, 14216,
                        14217, 14218, 14231, 14233, 14234, 14235, 14236, 14237, 14238, 14239, 14219, 14220,
                        14221, 14222, 14223, 14224, 14225, 14226, 14227, 14228, 14229, 14230, 14415, 14416,
                        14417, 14418, 14252, 14253, 14254, 14255, 14256, 14257, 14258, 14259, 14491, 14492,
                        14493, 14494, 14495, 14496, 14497, 14498, 14499, 14013, 14261, 14260, 19685, 19686,
                        19687, 19688, 19689, 19690, 19691, 19692, 19693, 21145, 21146, 21144, 25517, 25519,
                        25520, 25521, 25523, 25524, 25525, 25527, 26588, 26589, 13829, 13830, 13832, 13833,
                        13834, 13835, 17720, 23928, 23929, 14185, 14762, 14764, 14770, 14767, 14769, 14760,
                        17718, 18663, 23713, 23714, 23715, 23716, 23717, 23718
                    ))) {
                $val = '';
                // max level
            } elseif (in_array($acrit->id, array(14537, 14538, 14539, 14540, 14541, 14542, 14543, 14648, 22117,
                        14544, 14545, 14546, 14547, 14548, 14549, 14649, 22118, 34793,
                        14550, 14551, 14552, 14553, 14554, 14650, 22119,
                        14555, 14556, 14557, 14558, 14559, 14560, 14651, 14652, 14653, 22120,
                        14561, 14562, 14563, 14564, 14565, 14566, 14567, 22121,
                        14568, 14569, 14570, 14571, 14572, 22255, 14574, 14575,
                        19462, 19454, 19455, 19456, 19458, 19457, 19459,
                        19469, 19468, 19472, 19465, 19464, 14573, 19463,
                        14022, 34791, 14023, 14024, 14025, 19214, 14026, 14027, 14028, 14029, 14030, 14031,
                        17004, 17007, 17008,
                        14918, 14919, 14920, 14921, 14922, 14923, 14924, 14925, 36523, 36524,
                        4984,
                        15475,
                        15025,
                        17009, 32943,
                        14812, 14283, 14284, 14477, 14478, 14479, 14480, 14481, 22256, 27881,
                        19542,
                        5031, 5032, 5033, 5034, 5035, 5036, 5037, 5038, 5039, 15690, 14810,
                        15705,
                        14266, 14435, 14436, 14437, 14438, 14439, 14440, 20430, 20431, 20764, 20765, 20766, 20767, 20768, 20769, 20770, 20771, 20772, 20773, 20774, 20775, 20776,
                        14596,
                        13864, 13865, 13866, 13867, 13868, 13869, 13870, 13871, 13872, 13873, 13874, 14590, 14591, 14592, 14593,
                        13864, 13865, 13866, 13867, 13868, 13869, 13870, 13871, 13872, 13873, 13874, 14590, 14591, 14592, 14593,
                        14282, 14281,
                        4991, 19544,
                        16190, 14811, 19543,
                        21188, 14654, 15703, 19541, 16190, 14811, 19543,
                        15703, 19541
                    ))) {

                $val = $val . " / " . $acrit->max;
            } elseif (in_array($acrit->id, array(4093, 13757))) {
                $val = floor($val / 10000) . '/' . floor($acrit->max / 10000);
                // reputation
            } elseif (in_array($acrit->id, array(17456, 17911, 17462, 17916, 17467, 17919, 17470, 17472, 17922, 17475, 17925, 17926, 17480, 17929, 17485, 17486, 17932, 17934, 17935, 17936, 17937, 17938, 17939, 17940, 17941, 17942, 17943, 17944, 17502, 17947, 17505, 17949, 17507, 17950, 17510, 17954, 17956, 17517, 17519, 17965, 17966, 19734, 19729, 19727, 19476, 19728, 19731, 19733, 19732, 14517, 14518, 13671, 14520, 14521, 4741, 4743, 4737, 4742, 4759, 4764, 4751, 5336, 4761, 2020, 15946, 4767, 4766, 4750, 5335, 17453, 17909, 17459, 17913, 17915, 17921, 17924, 17928, 17930, 17946, 17948, 17951, 17955, 17522, 17961, 17962, 17963, 17964, 17531, 17968, 17969, 17970, 4765, 2048, 2049, 12911, 5333, 5334, 19736))) {
                $val = $val . " / " . $acrit->max;
            } else {
                // if (/*!$comp*/) {
                $this->critids[$acrit->id] = 1;

                // }
            }$this->critidsm[$acrit->max][] = $acrit->id;
            /* if ($acrit->max == 110) { //max level ??
              //$tmp .= '/' . $acrit->max;
              if ($val >= 110) {
              $val = '';
              }
              } elseif ($acrit->max == 1) {
              // if ($val == 1) {
              $val = '';
              //}
              //boolean ?
              } elseif ($acrit->max == 100) {
              // if ($val == 1) {
              //$val = '';
              //}
              //reput ?
              } elseif ($acrit->max == 25) {
              if ($val >= 25) {
              $val = '';
              }
              } elseif ($acrit->max == 250) {
              // if ($val == 1) {
              //$val = '';
              //}
              //reput ?
              } elseif ($acrit->max == 50) {

              } elseif ($acrit->max == 500) {

              } elseif ($acrit->max == 525) {
              if ($val >= 525) {
              $val = '';
              }
              } elseif ($acrit->max == 600) {
              if ($val >= 600) {
              $val = '';
              }
              } elseif ($acrit->max == 7500) {

              } elseif ($acrit->max == 2500) {

              } elseif ($acrit->max == 1500) {

              } elseif ($acrit->max == 1000) {

              } elseif ($acrit->max == 3000) {

              } elseif ($acrit->max == 5000) {

              } elseif ($acrit->max == 10000) {

              } elseif ($acrit->max == 42000) {

              if ($val >= 42000) {
              $val = '';
              }

              //reput ?
              } elseif ($acrit->max == 150000) {

              $val = '';
              } elseif ($acrit->max == 15000) {

              } elseif ($acrit->max == 25000) {

              } elseif ($acrit->max == 75000) {

              } elseif ($acrit->max == 30000) {

              } elseif ($acrit->max == 50000) {

              } elseif ($acrit->max == 100000) {

              } elseif ($acrit->max == 250000) {

              } elseif ($acrit->max == 1000000) {

              } elseif ($acrit->max == 2000) {

              } elseif ($acrit->max == 1000000000) {
              // if ($val == 1) {
              $val = floor($val / 10000);
              //}
              //reput ?
              } elseif ($acrit->max == 2000000000) {
              // if ($val == 1) {
              $val = floor($val / 10000);
              //}
              //reput ?
              } elseif ($acrit->max == 500000000) {
              // if ($val == 1) {
              $val = floor($val / 10000);
              //}
              //reput ?
              } else {
              $status = 'critnotdone';
              $val = 'max' + $acrit->max;
              } */
            if (!$comp && !$done) {
                $tmp .= '<li class="' . $status . '">'; //(' . $crid . ')';
                if ($acrit->description != "") {
                    $tmp .= $acrit->description;
                }
                $tmp .= ' ' . $val . '</li>';
            }
        }
        $tmp .= '</ul>';
        $tmp .= '</div>';
        return $tmp;
    }

    private function displayCat($achs, $criterias, $complete) {
        $tmp = '';
        if (isset($achs->categories)) {

            foreach ($achs->categories as $acat) {
                $tmp .= '<div class="achievementcate"><span>' . $acat->name . '</span>';
                foreach ($acat->achievements as $achievementin) {
                    if ($achievementin->factionId == 2 || $achievementin->factionId == 0) {
                        if (!array_key_exists($achievementin->id, $complete)) {
                            $tmp .= $this->displayAchievement($achievementin, $criterias, false);
                            //'<h4>' . $achievementin->title . '</h4>' . '(' . $achievementin->description . ')';
                            //     $cool .= '<br/>';
                        }
                    }
                }
                foreach ($acat->achievements as $achievementin) {
                    if ($achievementin->factionId == 2 || $achievementin->factionId == 0) {
                        if (array_key_exists($achievementin->id, $complete)) {
                            $tmp .= $this->displayAchievement($achievementin, $criterias, true);

                            //     $cool .= '<br/>';
                        }
                    }
                    // $cool .= '<br/>';
                }$tmp .= '</div>';
            }
        }
        return $tmp;
    }

    public function showGUAchievement(Twig_Environment $env) {
        $cool = '<div class="col-md-10">';
        $complete = $this->getCompletedAchievement();
        $criterias = $this->getCompletedCriterias();
        foreach ($this->getAchievementJSon()->achievements as $achievement) {
            if ($achievement->id != 15093) { // tour de force
                $cool .= '<div class="achievementmeta"><span>' . $achievement->name . '</span>';
                $cool .= '<div class="achievementgrid">';
                $cool .= $this->displayCat($achievement, $criterias, $complete);
                foreach ($achievement->achievements as $achievementin) {
                    if ($achievementin->factionId == 2 || $achievementin->factionId == 0) {
                        if (!array_key_exists($achievementin->id, $complete)) {
                            $cool .= $this->displayAchievement($achievementin, $criterias, false);
                            //'<h4>' . $achievementin->title . '</h4>' . '(' . $achievementin->description . ')';
                            //     $cool .= '<br/>';
                        }
                    }
                }
                foreach ($achievement->achievements as $achievementin) {
                    if ($achievementin->factionId == 2 || $achievementin->factionId == 0) {
                        if (array_key_exists($achievementin->id, $complete)) {
                            $cool .= $this->displayAchievement($achievementin, $criterias, true);

                            //     $cool .= '<br/>';
                        }
                    }
                    // $cool .= '<br/>';
                }
                $cool .= '</div>';
                $cool .= '</div>';
            }
        }
        /* $cool .= count($this->critids);
          $cool .= implode(', ', array_keys($this->critids));
          $testme = $this->critidsm[1];
         */
        /* foreach ($this->critidsm as $key => $maxc) {
          $cool .= '<br/>';
          $cool .= '<b>' . $key . '</b>';
          $cool .= implode(', ', array_unique($maxc));
          if ($key <> 1) {
          $cool .= '<p style="color:red">' . implode(', ', array_intersect($testme, $maxc)) . '</p>';
          }
          } */

        $cool .= "</div>";
        return $cool;
    }

}
