<?php

class CRM_Contributm_Model_Utm {

  const CUSTOM_GROUP_NAME = 'contributm';

  public static $keys = [
    'utm_source',
    'utm_medium',
    'utm_campaign',
    'utm_content',
  ];


  /**
   * Get utm values from session.
   *
   * @return array
   */
  public static function get() {
    $utm = [];
    $session = CRM_Core_Session::singleton();
    foreach (self::$keys as $key) {
      $v = $session->get($key, self::CUSTOM_GROUP_NAME);
      if ($v) {
        $utm[$key] = $v;
      }
    }
    return $utm;
  }

  /**
   * Clear utm values from session.
   */
  public static function clear() {
    $session = CRM_Core_Session::singleton();
    foreach (self::$keys as $key) {
      $session->set($key, NULL, self::CUSTOM_GROUP_NAME);
    }
  }

  /**
   * Set utm custom fields for given recurring contribution.
   *
   * @param int $recurringId
   * @param array $fields
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function set($recurringId, $fields = []) {
    $params = array(
      'sequential' => 1,
      'entity_id' => $recurringId,
      'entity_table' => 'civicrm_contribution',
    );
    if (CRM_Utils_Array::value('utm_source', $fields)) {
      $params[CRM_Core_BAO_Setting::getItem('Speakcivi API Preferences', 'field_contribution_source')] = $fields['utm_source'];
    }
    if (CRM_Utils_Array::value('utm_medium', $fields)) {
      $params[CRM_Core_BAO_Setting::getItem('Speakcivi API Preferences', 'field_contribution_medium')] = $fields['utm_medium'];
    }
    if (CRM_Utils_Array::value('utm_content', $fields)) {
      $params[CRM_Core_BAO_Setting::getItem('Speakcivi API Preferences', 'field_contribution_content')] = $fields['utm_content'];
    }
    if (CRM_Utils_Array::value('utm_campaign', $fields)) {
      $params[CRM_Core_BAO_Setting::getItem('Speakcivi API Preferences', 'field_contribution_campaign')] = $fields['utm_campaign'];
    }
    if (count($params) > 3) {
      civicrm_api3('CustomValue', 'create', $params);
    }
  }

}
