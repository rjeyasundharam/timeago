<?php

namespace Drupal\timeago\Controller;

use Symfony\Component\Yaml\Yaml;
use Drupal\Core\Controller\ControllerBase;
/**
 * Class TimeAgoController.
 *
 * @package Drupal\timeago\Controller
 */
class TimeAgoController extends ControllerBase {

  // This function used to reset default configuration of the module

  public function reset() {
    $mod_path = drupal_get_path('module', 'timeago');
    $yaml = new Yaml();
    $config = \Drupal::service('config.factory')->getEditable('timeago.settings');
    $variables_array = $yaml->parseFile($mod_path . '/config/install/timeago.settings.yml');
    if(!empty($variables_array)){
      foreach ($variables_array as $key=>$value) {
        $config->set($key, $value)->save();    
      }
    }

    // This function clear the render caches
    $renderCache = \Drupal::service('cache.render');
    $renderCache->invalidateAll();
    drupal_set_message("succesfully Saved");
    // After changes page will redircted to TimeAgo Form
    return $this->redirect('timeago.configform');
  }
}
