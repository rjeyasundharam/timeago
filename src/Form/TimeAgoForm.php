<?php
namespace Drupal\timeago\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\core\language\LanguageManager;
use Drupal\core\language\Language;
/**
 * Class TimeAgoForm.
 *
 * @package Drupal\timeago\Form
 */
class TimeAgoForm extends FormBase {
/**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'time_ago_form';
  }
  /**
   * The administrative settings form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    //get the timeagovariables and it's input configurations
    $timeagovariables=TimeAgoForm::timeagoVariables();
    
    //config the existing timeago settings
    $config = \Drupal::config('timeago.settings');

    $form['info'] = [
      '#markup' => '<p>' . t('Note that you can set Timeago as the default <a href="@datetime">date format</a>.',
      ['@datetime' =>'../config/timeago-reset']) . ' ' .t('This will allow you to use it for all dates on the site, overriding the settings below.') . '</p>',
    ];
    $form['timeago_node'] = [
      '#type' => 'checkbox',
      '#title' => t('Use timeago for node creation dates'),
      '#default_value' =>  $config->get('timeago_node') ,
    ];
    $form['timeago_comment'] = [
      '#type' => 'checkbox',
      '#title' => t('Use timeago for comment creation/changed dates'),
      '#default_value' => $config->get('timeago_comment'),
    ];

    $form['timeago_elem'] = [
      '#type' => 'radios',
      '#title' => t('Time element'),
      '#default_value' => $config->get('timeago_elem'),
      '#options' => [
        'span' => t('span'),
        'abbr' => t('abbr'),
        'time' => t('time (HTML5 only)'),
      ],
    ];

    $form['settings'] = [
      '#type' => 'fieldset',
      '#title' => t('Override Timeago script settings'),
      '#collapsible' => FALSE,
    ];
    // Refresh Millisecond variable
    $form['settings']['timeago_js_refresh_millis'] = [
      '#type' => 'textfield',
      '#title' => t('Refresh Timeago dates after'),
      '#description' => t('Timeago can update its dates without a page refresh at this interval. Leave blank or set to zero to never refresh Timeago dates.'),
      '#default_value' => $config->get('timeago_js_refresh_millis'),
      '#field_suffix' => ' ' . t('milliseconds'),
    ];

    $form['settings']['timeago_js_allow_future'] = [
      '#type' => 'checkbox',
      '#title' => t('Allow future dates'),
      '#default_value' => $config->get('timeago_js_allow_future'),
    ];

    $form['settings']['timeago_js_locale_title'] = [
      '#type' => 'checkbox',
      '#title' => t('Set the "title" attribute of Timeago dates to a locale-sensitive date'),
      '#default_value' => $config->get('timeago_js_locale_title'),
      '#description' => t('If this is disabled (the default) then the "title" attribute defaults to the original date that the Timeago script is replacing.'),
    ];

    $form['settings']['timeago_cutoff'] = [
      '#type' => 'textfield',
      '#title' => t('Do not use Timeago dates after'),
      '#field_suffix' => ' '. t('milliseconds'),
      '#description' => t('Leave blank or set to zero to always use Timeago dates.'),
      '#default_value' => $config->get('timeago_cutoff'),
    ];
    if (TimeAgoForm::timeago_library_detect_languages()) {
      $form['settings']['strings']['warning'] = [
        '#markup' => '<div class="messages warning">' . t('JavaScript translation files have been detected in the Timeago library. The following settings will not be used unless you remove those files.') . '</div>',
      ];
    }

    $form['settings']['strings'] = [
      '#type' => 'fieldset',
      '#title' => t('Timeago Strings'),
    ];


    $config = \Drupal::config('timeago.settings');
    foreach ($timeagovariables as $key => $variable) {
      $form['settings']['strings'][$variable['variable_name']] = [
        '#type' => 'textfield',
        '#title' => $variable['title'],
        '#required' => $variable['required'],
        '#default_value' =>  $config->get($variable['variable_name']),
      ];
    }
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'save',
    ];
    return $form;
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }
  /**
   * When User submit the time acho configuration form variables
   * values has been changed to user configurations 
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $timeago_vars=[
      'timeago_node',
      'timeago_elem',
      'timeago_comment',
      'timeago_js_refresh_millis',
      'timeago_js_locale_title',
      'timeago_cutoff',
      'timeago_js_allow_future'
    ];
    $config = \Drupal::service('config.factory')->getEditable('timeago.settings');
    $values=$form_state->getValues();
    $timeagovariables=TimeAgoForm::timeagoVariables();
    foreach ($timeagovariables as  $variable) {
      $config->set($variable['variable_name'], $values[$variable['variable_name']])->save();    
    }
    foreach ($timeago_vars as $variable) {
      $config->set($variable, $values[$variable])->save();
    }
    $renderCache = \Drupal::service('cache.render');
    $renderCache->invalidateAll();
    drupal_set_message("succesfully Saved");
    $form_state->setRedirect('timeago.configform');
  }

  /**
   * Grab an array of settings used by the Timeago plugin.
   * This is used to build the admin form, integrate with the module variables ,
   * and populate the JavaScript settings array.
   */

  public function timeagoVariables(){
    return [
      'prefixAgo' => [
        'title' => t('Prefix ago'),
        'required' => FALSE,
        'variable_name' => 'timeago_js_strings_prefix_ago',
      ],
    
      'prefixFromNow' => [
        'title' => t('Prefix from now'),
        'required' => FALSE,
        'variable_name' => 'timeago_js_strings_prefix_from_now',
      ],
      'suffixAgo' => [
        'title' => t('Suffix ago'),
        'required' => FALSE,
        'variable_name' => 'timeago_js_strings_suffix_ago',
      ],
      'suffixFromNow' => [
        'title' => t('Suffix from now'),
        'required' => FALSE,
        'variable_name' => 'timeago_js_strings_suffix_from_now',
      ],
      'second' => [
        'title' => t('Second'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_second',
       ],
      'seconds' => [
        'title' => t('Seconds'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_seconds',
       ],
      'minute' => [
        'title' => t('Minute'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_minute',
       ],
      'minutes' => [
        'title' => t('Minutes'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_minutes',
       ],
      'hour' => [
        'title' => t('Hour'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_hour',
       ],
      'hours' => [
        'title' => t('Hours'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_hours',
       ],
      'day' => [
        'title' => t('Day'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_day',
       ],
      'days' => [
        'title' => t('Days'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_days',
       ],
      'month' => [
        'title' => t('Month'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_month',
       ],
      'months' => [
        'title' => t('Months'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_months',
       ],
      'year' => [
        'title' => t('Year'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_year',
       ],
       'years' => [
        'title' => t('Years'),
        'required' => TRUE,
        'variable_name' => 'timeago_js_strings_years',
       ],
      'word-separator' => [
          'title' => t('Word Separator'),
          'required' => FALSE,
          'variable_name' => 'timeago_word_separator',
         ],
      ];
  }

/**
 * Detect Timeago library language files if present.
 */
  public function timeago_library_detect_languages() {
    // Figure out where the Timeago library is, and then work out if translation
    // files are present.
    $library_path = \Drupal::moduleHandler()->moduleExists('libraries') ? libraries_get_path('timeago') : drupal_get_path('module', 'timeago');
    $library_languages_found = FALSE;
    $languages = \Drupal::languageManager()->getStandardLanguageList();
    //Unset Langugae Engish
    unset($languages['en']);
    foreach ($languages as $lang_code => $language) {
      if (file_exists($library_path . '/jquery.timeago.' . $lang_code . '.js')) {
        $library_languages_found = TRUE;
        break;
      }
    }
  }
}
?>