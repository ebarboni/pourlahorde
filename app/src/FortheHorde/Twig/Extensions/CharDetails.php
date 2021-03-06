<?php

namespace FortheHorde\Twig\Extensions;

use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

class CharDetails extends Twig_Extension {

    public function getFunctions() {
        $functions = array(
            new Twig_SimpleFunction('showCharDetails', array($this, 'showCharDetails'), array('needs_environment' => true, 'is_safe' => array('html'))),
        );
        return $functions;
    }

// count for ilevel
    /* private function displayStuff($character) {
      //var_dump($character);
      if (isset($character->items)) {
      $tmp = '<div class="col-xs-1 mw">' . $character->items->averageItemLevel . '</div><div class="col-xs-1 mw">' . $character->items->averageItemLevelEquipped . '</div>';
      } //<td>' . floor($tmp / $di) . '</td>';
      else {
      $tmp = '<div class="col-xs-1 mw">&nbsp;</div><div class="col-xs-1">&nbsp;</div>';
      }
      if (isset($character->items->neck->azeriteItem) && $character->items->neck->azeriteItem->azeriteLevel > 0) {
      $tmp .= '<div class="col-xs-1 mw">' . $character->items->neck->azeriteItem->azeriteLevel;
      $tmp .= '<br>' . '<span class="anitem">' . number_format(100 * ($character->items->neck->azeriteItem->azeriteExperience / $character->items->neck->azeriteItem->azeriteExperienceRemaining), 2, ',', '') . '</span>';
      $tmp .= '</div>';
      } else {
      $tmp .= '<div class="col-xs-1 mw">&nbsp;</div>';
      }
      foreach ($this->displayitem as $key) {
      $tmp .= $this->getItemLevelL($character, $key);
      }

      $tmp .= '<div class="col-xs-1 mw">&nbsp;</div><div class="col-xs-1 mw">&nbsp;</div>';
      return $tmp;
      }
     */
    public function showCharDetails(Twig_Environment $env, $name) {
        $charobject = Utils::getDecodedPlayer($name);
        if (isset($charobject->class)) {
            $color = Utils::getColor($charobject->class);
            //echo 'name' . $name;
            $d = '';
            $c = '<span class="pname ' . $color . '">' . $charobject->name . '</span>';
            $c .= '<div class="aperso ' . $d . ' ">';
            if ($charobject->level < 10) {
                $c .= '<img src="http://eu.battle.net/wow/static/images/2d/avatar/' . $charobject->race . '-' . $charobject->gender . '.jpg" class="persoimg" height="84" width="84" />';
            } else {
                $c .= '<img class="overflowingVertical" src="https://render-eu.worldofwarcraft.com/character/' . str_replace('-avatar', '-inset', $charobject->thumbnail) . '" height="116" width="230" />';
            }


            if ($charobject->level < 120) { // max level
                $c .= '<span class="level">' . $charobject->level . '</span>';
            }

            $c .= '</div>';
            $c .= '<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-homed-tab" data-toggle="tab" href="#nav-homed" role="tab" aria-controls="nav-homed" aria-selected="true">Détails</a>
    <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="false">Caractérisques calculées</a>
    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Statistiques du personnage</a>
    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Hauts Faits</a>
  </div>
</nav>';
            $statstxt = '<table>';
            if (isset($charobject->stats)) {
                foreach ($charobject->stats as $key => $value) {
                    $statstxt .= "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
                }
            }
            $statstxt .= '</table>';


            $statisticsstxt = '<div>';
            if (isset($charobject->statistics)) {
                $statisticsstxt .= '<span>' . $charobject->statistics->name . '</span>';
                foreach ($charobject->statistics->subCategories as $key => $value) {
                    //   $statisticsstxt .= '<div>';
                    $statisticsstxt .= $this->getMoreStats($value);
                    //   $statisticsstxt .= '</div>';
                }
            }
            $statisticsstxt .= '</div>';
            $perso = '<div class="row">' . Text::displayStuff($charobject, false);

            $perso .= '</div>';
            $c .= '<div class="tab-content" id="nav-tabContent">
                 <div class="tab-pane fade show active" id="nav-homed" role="tabpanel" aria-labelledby="nav-homed-tab">' . $perso . '</div>
  <div class="tab-pane fade" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">' . $statstxt . '</div>
  
  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">' . $statisticsstxt . '</div>
  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">plus tard</div>
</div>';
            return $c;
        }
    }

    private function getMoreStats($sub) {
        $statisticsstxt = '<div>';

        foreach (@$sub->statistics as $key => $value) {
            $statisticsstxt .= '<span>' . $value->name . ' <span class="statdata">';
            if (@$value->quantity > 0) {
                $statisticsstxt .= ' ' . $value->quantity;
            }if (@$value->highest != '') {
                $statisticsstxt .= ' ' . $value->highest;
            }
            $statisticsstxt .= '</span></span><br>';
        }
        if (isset($sub->subCategories)) {
            foreach ($sub->subCategories as $key => $value) {
                //   $statisticsstxt .= '<div>';
                $statisticsstxt .= $this->getMoreStats($value);
                //   $statisticsstxt .= '</div>';
            }
        }
        $statisticsstxt .= '</div>';
        return $statisticsstxt;
    }

}
