<?php

/**
 * Collection of upgrade steps.
 */
class CRM_Contributm_Upgrader extends CRM_Contributm_Upgrader_Base {
  /**
   * @return bool
   */
  public function upgrade_110_install_custom_recur_utm() {
    $this->executeCustomDataFileByAbsPath($this->extensionDir . '/xml/recur_utm.xml');
    return TRUE;
  }

}
