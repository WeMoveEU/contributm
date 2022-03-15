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
    $utm = [];
    $page = new CRM_Core_Page();
    foreach (CRM_Contributm_Model_Utm::$keys as $key) {
      $utm[$key] = CRM_Utils_Request::retrieve($key, 'String', $page, FALSE);
    }
    if ($form->_flagSubmitted) {
      $session = CRM_Core_Session::singleton();
      $isRecur = (bool) CRM_Utils_Array::value('is_recur', $form->_submitValues);
      CRM_Contributm_Model_UtmRecur::clear();
      CRM_Contributm_Model_Utm::clear();
      foreach ($utm as $key => $value) {
        if ($value) {
          if ($isRecur) {
            $session->set($key, $value, CRM_Contributm_Model_UtmRecur::CUSTOM_GROUP_NAME);
          }
          else {
            $session->set($key, $value, CRM_Contributm_Model_Utm::CUSTOM_GROUP_NAME);
          }
        }
      }
    }
  }
}

/**
 * @param $dao
 *
 * @throws \CiviCRM_API3_Exception
 */
function contributm_civicrm_postSave_civicrm_contribution($dao) {
  $utm = CRM_Contributm_Model_Utm::get();
  if ($utm) {
    CRM_Contributm_Model_Utm::set($dao->id, $utm);
    CRM_Contributm_Model_Utm::clear();
  }
  #
  #  Setting the UTMs here is conflicting with repeatTransaction and copyCustomValues. I do 
  #  not know what changed, but since there is a duplicate key error, it seems safe to drop it.
  #
  #  if ($dao->contribution_recur_id && !$utm) {
  #    $utmRecur = CRM_Contributm_Model_UtmRecur::getDb($dao->contribution_recur_id);
  #    if ($utmRecur) {
  #      $utmSingle = CRM_Contributm_Model_Utm::getDb($dao->id);
  #      if (!$utmSingle) {
  #        CRM_Contributm_Model_Utm::set($dao->id, $utmRecur);
  #      }
  #    }
  #  }
}

/**
 * @param $dao
 *
 * @throws \CiviCRM_API3_Exception
 */
function contributm_civicrm_postSave_civicrm_contribution_recur($dao) {
  $utm = CRM_Contributm_Model_UtmRecur::get();
  if ($utm) {
    CRM_Contributm_Model_UtmRecur::set($dao->id, $utm);
    CRM_Contributm_Model_UtmRecur::clear();
  }
}
