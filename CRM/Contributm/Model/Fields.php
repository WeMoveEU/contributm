<?php

class CRM_Contributm_Model_Fields {

  const CUSTOM_GROUP_NAME_RECUR_UTM = 'recur_utm';
  const CUSTOM_FIELD_PREFIX = 'custom_';

  /**
   * @param $name
   *
   * @return string
   * @throws \CiviCRM_API3_Exception
   */
  private static function getCustomFieldRecurUtm($name) {
    $result = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'custom_group_id' => self::CUSTOM_GROUP_NAME_RECUR_UTM,
      'name' => $name,
    ]);
    return self::CUSTOM_FIELD_PREFIX . $result['id'];
  }

  /**
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

}