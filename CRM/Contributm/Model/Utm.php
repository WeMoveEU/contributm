<?php

class CRM_Contributm_Model_Utm {

  const CUSTOM_GROUP_NAME = 'utm';
  const CUSTOM_FIELD_PREFIX = 'custom_';

  public static $keys = [
    'utm_source',
    'utm_medium',
    'utm_campaign',
    'utm_content',
  ];

  private static function getCustomField($name) {
    $result = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => self::CUSTOM_GROUP_NAME,
      'name' => $name,
    ]);
    return self::CUSTOM_FIELD_PREFIX . $result['id'];
  }

  private static function utmField($name) {
    $key = __CLASS__ . __FUNCTION__ . $name;
    $cache = Civi::cache()->get($key);
    if (!isset($cache)) {
      $id = self::getCustomField("utm_$name");
      Civi::cache()->set($key, $id);
      return $id;
    }
    return $cache;
  }

  public static function utmSource() {
    return self::utmField('source');
  }

  public static function utmMedium() {
    return self::utmField('medium');
  }

  public static function utmCampaign() {
    return self::utmField('campaign');
  }

  public static function utmContent() {
    return self::utmField('content');
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
   * Get utms for this contribution saved in db.
   *
   * @param $contributionId
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public static function getDb($contributionId) {
    $return  = [
      'utm_source' => self::utmSource(),
      'utm_medium' => self::utmMedium(),
      'utm_campaign' => self::utmCampaign(),
      'utm_content' => self::utmContent(),
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
    foreach (['source', 'medium', 'campaign', 'content'] as $field) {
      if (CRM_Utils_Array::value('utm_' . $field, $fields)) {
        $params[self::utmField($field)] = $fields['utm_' . $field];
      }
    }
    if (count($params) > 3) {
      # civicrm_api3('Contribution', 'create', $params);
      civicrm_api3('CustomValue', 'create', $params);
    }
  }

}
