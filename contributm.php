<?php

require_once 'contributm.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function contributm_civicrm_config(&$config) {
  _contributm_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function contributm_civicrm_xmlMenu(&$files) {
  _contributm_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function contributm_civicrm_install() {
  _contributm_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function contributm_civicrm_uninstall() {
  _contributm_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function contributm_civicrm_enable() {
  _contributm_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function contributm_civicrm_disable() {
  _contributm_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function contributm_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _contributm_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function contributm_civicrm_managed(&$entities) {
  _contributm_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function contributm_civicrm_caseTypes(&$caseTypes) {
  _contributm_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function contributm_civicrm_angularModules(&$angularModules) {
_contributm_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function contributm_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _contributm_civix_civicrm_alterSettingsFolders($metaDataFolders);
}


/**
 * Implements hook_civicrm_preProcess().
 */
function contributm_civicrm_preProcess($formName, &$form) {
  if (in_array($formName, array('CRM_Contribute_Form_Contribution_Main', 'CRM_Contribute_Form_Contribution'))) {
    $page = new CRM_Core_Page();
    $utmSource = CRM_Utils_Request::retrieve('utm_source', 'String', $page, false);
    $utmMedium = CRM_Utils_Request::retrieve('utm_medium', 'String', $page, false);
    $utmContent = CRM_Utils_Request::retrieve('utm_content', 'String', $page, false);
    $utmCampaign = CRM_Utils_Request::retrieve('utm_campaign', 'String', $page, false);
    $utm = array(
      'utm_source' => $utmSource,
      'utm_medium' => $utmMedium,
      'utm_content' => $utmContent,
      'utm_campaign' => $utmCampaign
    );
    $session = CRM_Core_Session::singleton();
    foreach ($utm as $item => $value) {
      $session->set($item, $value, 'contributm');
    }
  }
}


/**
 * Implements hook_civicrm_postProcess().
 */
function contributm_civicrm_postProcess($formName, &$form) {
  // Contribution
  if ($formName == 'CRM_Contribute_Form_Contribution') {
    $contributionId = $form->_id;
  }
  // Contribution Page
  else if ($formName == 'CRM_Contribute_Form_Contribution_Confirm') {
    $contributionId = $form->_contributionID;
  }
  else {
    $contributionId = 0;
  }

  if ($contributionId) {
    $utm = array(
      'utm_source' => '',
      'utm_medium' => '',
      'utm_content' => '',
      'utm_campaign' => ''
    );
    $session = CRM_Core_Session::singleton();
    foreach ($utm as $item => $value) {
      $utm[$item] = $session->get($item, 'contributm');
    }
    setUtm($contributionId, $utm);
    foreach ($utm as $item => $value) {
      $session->set($item, null, 'contributm');
    }
  }
}


/**
 * Helper function for set up utm fields.
 */
function setUtm($contributionId, $fields) {
  $params = array(
    'sequential' => 1,
    'id' => $contributionId,
  );
  $fields = (array)$fields;
  if (array_key_exists('utm_source', $fields) && $fields['utm_source']) {
    $params['custom_30'] = $fields['utm_source'];
  }
  if (array_key_exists('utm_medium', $fields) && $fields['utm_medium']) {
    $params['custom_31'] = $fields['utm_medium'];
  }
  if (array_key_exists('utm_content', $fields) && $fields['utm_content']) {
    $params['custom_32'] = $fields['utm_content'];
  }
  if (array_key_exists('utm_campaign', $fields) && $fields['utm_campaign']) {
    $params['custom_33'] = $fields['utm_campaign'];
  }
  if (count($params) > 2) {
    civicrm_api3('Contribution', 'create', $params);
  }
}
