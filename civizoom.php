<?php

require_once 'civizoom.civix.php';
use CRM_Civizoom_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civizoom_civicrm_config(&$config) {
  _civizoom_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civizoom_civicrm_xmlMenu(&$files) {
  _civizoom_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civizoom_civicrm_install() {
  _civizoom_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function civizoom_civicrm_postInstall() {
  _civizoom_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civizoom_civicrm_uninstall() {
  _civizoom_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civizoom_civicrm_enable() {
  _civizoom_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civizoom_civicrm_disable() {
  _civizoom_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civizoom_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civizoom_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civizoom_civicrm_managed(&$entities) {
  _civizoom_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civizoom_civicrm_caseTypes(&$caseTypes) {
  _civizoom_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function civizoom_civicrm_angularModules(&$angularModules) {
  _civizoom_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civizoom_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civizoom_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function civizoom_civicrm_entityTypes(&$entityTypes) {
  _civizoom_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function civizoom_civicrm_navigationMenu(&$menu) {
  _civizoom_civix_insert_navigation_menu($menu, 'Administer', [
    'label' => E::ts('CiviZoom Settings'),
    'name' => 'civizoom_settings',
    'url' => 'civicrm/admin/setting/civizoom',
    'permission' => 'access CiviEvent',
    'operator' => 'OR',
    'separator' => 0,
  ]);
  _civizoom_civix_navigationMenu($menu);
}

function civizoom_civicrm_fieldOptions($entity, $field, &$options, $params) {
  /*Civi::log()->debug(__FUNCTION__, [
    'entity' => $entity,
    'field' => $field,
  ]);*/

  if ($entity == 'Event') {
    $zoomMtg = CRM_Core_BAO_CustomField::getCustomFieldID('zoom_meeting', 'civizoom', TRUE);
    if ($field == $zoomMtg && CRM_Civizoom_Zoom::getZoomObject()) {
      $meetings = CRM_Civizoom_Zoom::getMeetingIds(FALSE);
      $webinars = CRM_Civizoom_Zoom::getWebinarIds();

      foreach ($meetings as $meeting) {
        $options['m'.$meeting['id']] = $meeting['topic'].' (meeting)';
      }

      foreach ($webinars as $webinar) {
        $options['w'.$webinar['id']] = $webinar['topic'].' (webinar)';
      }
    }
  }
}

function civizoom_civicrm_postCommit($op, $objectName, $objectId, &$objectRef) {
  /*Civi::log()->debug(__FUNCTION__, [
    '$op' => $op,
    '$objectName' => $objectName,
    '$objectId' => $objectId,
    '$objectRef' => $objectRef,
  ]);*/

  if ($op == 'create' && $objectName == 'Participant') {
    $zoomId = CRM_Civizoom_Zoom::getEventZoomMeetingId($objectRef->event_id);
    $statusReg = CRM_Civizoom_Zoom::getConfiguredStatuses('register');
    $rolesConfigured = CRM_Civizoom_Zoom::getConfiguredRoles();
    $rolesSelected = CRM_Utils_Array::explodePadded($objectRef->role_id);

    if ($zoomId &&
      in_array($objectRef->status_id, $statusReg) &&
      CRM_Civizoom_Zoom::getZoomObject() &&
      !empty(array_intersect($rolesConfigured, $rolesSelected))
    ) {
      try {
        $contact = civicrm_api3('Contact', 'getsingle', ['id' => $objectRef->contact_id]);

        $params = [
          'email' => $contact['email'],
          'first_name' => $contact['first_name'],
          'last_name' => $contact['last_name'],
          'address' => $contact['street_address'],
          'city' => $contact['city'],
          'country' => $contact['country'],
          'state' => $contact['state_province'],
          'zip' => $contact['postal_code'] ?? NULL,
          'phone' => $contact['phone'],
          'org' => $contact['current_employer'],
          'job_title' => $contact['job_title'],
          'custom_questions' => [
            [
              'title' => 'participant_id',
              'value' => $objectRef->id,
            ],
            [
              'title' => 'contact_id',
              'value' => $objectRef->contact_id,
            ],
          ],
        ];

        $zoomReg = CRM_Civizoom_Zoom::createZoomRegistration($zoomId, $params);
        //Civi::log()->debug(__FUNCTION__, ['$zoomReg' => $zoomReg]);

        //presence of a code in the response indicates a problem
        if (empty($zoomReg['code'])) {
          $registrant_id = CRM_Core_BAO_CustomField::getCustomFieldID('registrant_id', 'civizoom_registrant', TRUE);
          $join_url = CRM_Core_BAO_CustomField::getCustomFieldID('join_url', 'civizoom_registrant', TRUE);

          $participantParams = [
            'id' => $objectRef->id,
            $registrant_id => $zoomReg['registrant_id'],
            $join_url => $zoomReg['join_url'],
          ];
          //Civi::log()->debug(__FUNCTION__, ['$participantParams' => $participantParams]);

          civicrm_api3('Participant', 'create', $participantParams);
        }
      }
      catch (CiviCRM_API3_Exception $e) {
        Civi::log()->debug(__FUNCTION__, ['$e' => $e]);
      }
    }
  }

  if ($op == 'edit' && $objectName == 'Participant') {
    $zoomId = CRM_Civizoom_Zoom::getEventZoomMeetingId($objectRef->event_id);
    $statusCancel = CRM_Civizoom_Zoom::getConfiguredStatuses('cancel');

    if ($zoomId && in_array($objectRef->status_id, $statusCancel) && CRM_Civizoom_Zoom::getZoomObject()) {
      $registrant_id = CRM_Core_BAO_CustomField::getCustomFieldID('registrant_id', 'civizoom_registrant', TRUE);

      if ($registrant_id) {
        try {
          $participant = civicrm_api3('Participant', 'getsingle', [
            'id' => $objectRef->id,
            'api.Contact.getsingle' => [],
            'return' => [$registrant_id],
          ]);

          CRM_Civizoom_Zoom::cancelZoomRegistration($zoomId, $participant[$registrant_id],
            $participant['api.Contact.getsingle']['email']);
        }
        catch (CiviCRM_API3_Exception $e) {
          Civi::log()->debug(__FUNCTION__, ['$e' => $e]);
        }
      }
    }
  }
}
