<?php

namespace FortheHorde\Twig\Extensions;

use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class PlayerAchievement extends Twig_Extension {

    public function getFunctions() {
        $functions = array(
            new Twig_SimpleFunction('showSpecificAchievements', array($this, 'showSPAchievement'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
        return $functions;
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
        $path = realpath(__DIR__ . "/../../../../../app/_data/achievementperso.json");
        $myfile = fopen($path, "r") or die("Unable to open file!");
        $json = fread($myfile, filesize($path));
        fclose($myfile);
        $json_decoded = json_decode($json);

        return $json_decoded;
    }

    private $critids = array();
    private $critidsm = array();

    private function displayAchievement($ach/* , $cri, $done */) {
        $doneclass = "";
        /* if (!$done) {
          $doneclass .= ' achdone';
          } */
        $tmp = '<div class=" anach ' . $doneclass . '">';
        $icon = '';
        if (isset($ach->icon)) {
            $icon = '<img class="achimage" src="https://render-eu.worldofwarcraft.com/icons/56/' . $ach->icon . '.jpg"/>';
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
            /*   if (array_key_exists($crid, $cri)) {
              $val = $cri[$crid];
              $cpm = true;
              $status = 'critcomp';
              } */
            $comp = false;
            if ($val >= $acrit->max) {
                $status = 'critcomp';
                $comp = true;
            } else {
                $status = 'critincomp';
            }
            $icon = '';
            if (isset($acrit->icon)) {
                $icon = '<img class="tradeskill" src="https://render-eu.worldofwarcraft.com/icons/56/' . $acrit->icon . '.jpg"/>';
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

            // if (!$comp && !$done) {
            $tmp .= '<li class="' . $status . '">'; //(' . $crid . ')';
            if ($acrit->description != "") {
                $tmp .= $acrit->description;
            }
            $tmp .= ' ' . $val . '</li>';
            //}
        }
        $tmp .= '</ul>';
        $tmp .= '</div>';
        return $tmp;
    }

    private function displayCat($achs/* , $criterias, $complete */) {
        $tmp = '';
        if (isset($achs->categories)) {

            foreach ($achs->categories as $acat) {
                $tmp .= '<div class="achievementcate"><span>' . $acat->name . '</span>';
                foreach ($acat->achievements as $achievementin) {
                    //if ($achievementin->factionId == 2 || $achievementin->factionId == 0) {
                    //        if (!array_key_exists($achievementin->id, $complete)) {
                    $tmp .= $this->displayAchievement($achievementin/* , $criterias, false */);
                    //        }
                    //}
                }
                foreach ($acat->achievements as $achievementin) {
                    //if ($achievementin->factionId == 2 || $achievementin->factionId == 0) {
                    //    if (array_key_exists($achievementin->id, $complete)) {
                    $tmp .= $this->displayAchievement($achievementin/* , $criterias, true */);
                    //      }
                    // }
                }/**/
                $tmp .= '</div>';
            }
        }
        return $tmp;
    }

    public function showSPAchievement(Twig_Environment $env) {
        $cool = '<div class="col-md-10">';
        $majorcat = array(168);
        // $complete = $this->getCompletedAchievement();
        // $criterias = $this->getCompletedCriterias();
        //if (in_array("Glenn", $people))
        foreach ($this->getAchievementJSon()->achievements as $achievement) {
            //if (in_array($achievement->id, $majorcat)) { // tour de force
            $cool .= '<div class="achievementmeta"><span>' . $achievement->id . ' ' . $achievement->name . '</span>';
            $cool .= '<div class="achievementgrid">';
            $cool .= $this->displayCat($achievement/* , $criterias, $complete */);
            /* foreach ($achievement->achievements as $achievementin) {
              if ($achievementin->factionId == 2 || $achievementin->factionId == 0) {
              if (!array_key_exists($achievementin->id, $complete)) {
              $cool .= $this->displayAchievement($achievementin, $criterias, false);
              //'<h4>' . $achievementin->title . '</h4>' . '(' . $achievementin->description . ')';
              //     $cool .= '<br/>';
              }
              }
              } */
            /* foreach ($achievement->achievements as $achievementin) {
              if ($achievementin->factionId == 2 || $achievementin->factionId == 0) {
              if (array_key_exists($achievementin->id, $complete)) {
              $cool .= $this->displayAchievement($achievementin, $criterias, true);

              }
              }
             */
            //}
            $cool .= '</div>';
            $cool .= '</div>';
            //  }
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
