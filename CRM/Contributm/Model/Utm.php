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
   * Get utms for this contribution saved in db.
   *
   * @param $contributionId
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public static function getDb($contributionId) {
    $return  = [
      'utm_source' => Civi::settings()->get('field_contribution_source'),
      'utm_medium' => Civi::settings()->get('field_contribution_medium'),
      'utm_campaign' => Civi::settings()->get('field_contribution_campaign'),
      'utm_content' => Civi::settings()->get('field_contribution_content'),
    ];
    $params = [
      'sequential' => 1,
      'id' => $contributionId,
      'return' => implode(',', $return),
    ];
    $result = civicrm_api3('Contribution', 'get', $params);
    if ($result['count']) {
      $values = $result['values'][0];
      $utms = [];
      foreach ($return as $key => $column) {
        if (CRM_Utils_Array::value($column, $values, NULL)) {
          $utms[$key] = CRM_Utils_Array::value($column, $values, NULL);
        }
      }
      return $utms;
    }
    return [];
  }

  /**
   * Set utm custom fields for given single contribution.
   *
   * @param int $contributionId
   * @param array $fields
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function set($contributionId, $fields = []) {
    $params = array(
      'sequential' => 1,
      'entity_id' => $contributionId,
      'entity_table' => 'civicrm_contribution',
    );
    $mapping = [
      'utm_source' => 'field_contribution_source',
      'utm_medium' => 'field_contribution_medium',
      'utm_campaign' => 'field_contribution_campaign',
      'utm_content' => 'field_contribution_content',
    ];
    foreach ($mapping as $field => $setting) {
      if (CRM_Utils_Array::value($field, $fields)) {
        $params[Civi::settings()->get($setting)] = CRM_Utils_Array::value($field, $fields);
      }
    }
    if (count($params) > 3) {
      # civicrm_api3('Contribution', 'create', $params);
      civicrm_api3('CustomValue', 'create', $params);
    }
  }

}
