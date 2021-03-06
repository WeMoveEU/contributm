<?php

class CRM_Contributm_Model_UtmRecur {

  const CUSTOM_GROUP_NAME = 'recur_utm';
  const CUSTOM_FIELD_PREFIX = 'custom_';

  public static $keys = [
    'utm_source',
    'utm_medium',
    'utm_campaign',
    'utm_content',
  ];

  /**
   * @param $name
   *
   * @return string
   * @throws \CiviCRM_API3_Exception
   */
  private static function getCustomFieldRecurUtm($name) {
    $result = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => self::CUSTOM_GROUP_NAME,
      'name' => $name,
    ]);
    return self::CUSTOM_FIELD_PREFIX . $result['id'];
  }

  /**
   * Get custom field name for utm_source.
   *
   * @return string
   * @throws \CiviCRM_API3_Exception
   */
  public static function utmSource() {
    $key = __CLASS__ . __FUNCTION__;
    $cache = Civi::cache()->get($key);
    if (!isset($cache)) {
      $id = self::getCustomFieldRecurUtm('utm_source');
      Civi::cache()->set($key, $id);
      return $id;
    }
    return $cache;
  }

  /**
   * Get custom field name for utm_medium.
   *
   * @return string
   * @throws \CiviCRM_API3_Exception
   */
  public static function utmMedium() {
    $key = __CLASS__ . __FUNCTION__;
    $cache = Civi::cache()->get($key);
    if (!isset($cache)) {
      $id = self::getCustomFieldRecurUtm('utm_medium');
      Civi::cache()->set($key, $id);
      return $id;
    }
    return $cache;
  }

  /**
   * Get custom field name for utm_campaign.
   *
   * @return string
   * @throws \CiviCRM_API3_Exception
   */
  public static function utmCampaign() {
    $key = __CLASS__ . __FUNCTION__;
    $cache = Civi::cache()->get($key);
    if (!isset($cache)) {
      $id = self::getCustomFieldRecurUtm('utm_campaign');
      Civi::cache()->set($key, $id);
      return $id;
    }
    return $cache;
  }

  /**
   * Get custom field name for utm_content.
   *
   * @return string
   * @throws \CiviCRM_API3_Exception
   */
  public static function utmContent() {
    $key = __CLASS__ . __FUNCTION__;
    $cache = Civi::cache()->get($key);
    if (!isset($cache)) {
      $id = self::getCustomFieldRecurUtm('utm_content');
      Civi::cache()->set($key, $id);
      return $id;
    }
    return $cache;
  }

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
   * Get utms for this recurring contribution saved in db.
   *
   * @param $recurringId
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public static function getDb($recurringId) {
    $return  = [
      'utm_source' => self::utmSource(),
      'utm_medium' => self::utmMedium(),
      'utm_campaign' => self::utmCampaign(),
      'utm_content' => self::utmContent(),
    ];
    $params = [
      'sequential' => 1,
      'id' => $recurringId,
      'return' => implode(',', $return),
    ];
    $result = civicrm_api3('ContributionRecur', 'get', $params);
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
      'entity_table' => 'contributionRecur',
    );
    if (CRM_Utils_Array::value('utm_source', $fields)) {
      $params[self::utmSource()] = $fields['utm_source'];
    }
    if (CRM_Utils_Array::value('utm_medium', $fields)) {
      $params[self::utmMedium()] = $fields['utm_medium'];
    }
    if (CRM_Utils_Array::value('utm_campaign', $fields)) {
      $params[self::utmCampaign()] = $fields['utm_campaign'];
    }
    if (CRM_Utils_Array::value('utm_content', $fields)) {
      $params[self::utmContent()] = $fields['utm_content'];
    }
    if (count($params) > 3) {
      $result = civicrm_api3('CustomValue', 'create', $params);
    }
  }

}
