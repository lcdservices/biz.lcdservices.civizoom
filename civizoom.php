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

      if ($params['context'] == 'create') {
        $options['n0'] = '<- Create new meeting ->';
      }

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

  // Creation of a recurring set for an event
  if ($op == 'create' && $objectName == 'ActionSchedule' && (strcmp($objectRef->repetition_frequency_unit, 'hour') !== 0 && strcmp($objectRef->repetition_frequency_unit, 'year') !== 0)) {

    $zoomId = CRM_Civizoom_Zoom::getEventZoomMeetingId($objectRef->entity_value);

    // Check parent event for Zoom meeting
    if ((substr($zoomId, 0, 1) == 'm' || substr($zoomId, 0, 1) == 'w' ) && CRM_Civizoom_Zoom::getZoomObject()) {

      // Obtain day from full date
      $start_action_day = date('d', strtotime(strval($objectRef->start_action_date)));

      // Set recurring meeting parameters
      $recurring_parameters = array (
        "repetition_frequency_unit" => array(
          "day" => 1,
          "week" => 2,
          "month" => 3,
        ),
        "repetition_frequency_interval" => intval($objectRef->repetition_frequency_interval),
        "start_action_condition" => array(
          "sunday" => 1,
          "monday" => 2,
          "tuesday" => 3,
          "wednesday" => 4,
          "thursday" => 5,
          "friday" => 6,
          "saturday" => 7,
        ),
        "limit_to" => intval($objectRef->limit_to),
        "start_action_date" => strval($start_action_day),
        "entity_status_1" => array(
          "last" => -1,
          "first" => 1,
          "second" => 2,
          "third" => 3,
          "fourth" => 4,
        ),
        "entity_status_2" => array(
          "sunday" => 1,
          "monday" => 2,
          "tuesday" => 3,
          "wednesday" => 4,
          "thursday" => 5,
          "friday" => 6,
          "saturday" => 7,
        ),
        "start_action_offset" => intval($objectRef->start_action_offset) + 1,
        "absolute_date" =>  intval($objectRef->absolute_date),
      );

      $weekly_days_imploded = array();
      $weekly_days_converted = array();

      // Retrieve days of the week from string
      $entity_status_exploded = explode(" ", $objectRef->entity_status);

      // Retrieve day & week information for recurring event
      $weekly_days_imploded = explode(",", $objectRef->start_action_condition);
      foreach ($weekly_days_imploded as $day) {
        array_push($weekly_days_converted, $recurring_parameters['start_action_condition'][$day]);
      }

      $params = [
        "type" => 8,
        "recurrence" => [
          "type" => $recurring_parameters['repetition_frequency_unit'][$objectRef->repetition_frequency_unit],
          "repeat_interval" => $recurring_parameters['repetition_frequency_interval'],
        ],
      ];

      // Set interval specific fields.
      if ($params['recurrence']['type'] == 2) {
        $params['recurrence']['weekly_days'] = implode(",", $weekly_days_converted);
      } else if ($params['recurrence']['type'] == 3) {
        if ($objectRef->entity_status) {
          $params['recurrence']['monthly_day'] = $recurring_parameters['start_action_date'];
          $params['recurrence']['monthly_week'] = $recurring_parameters['entity_status_1'][$entity_status_exploded[0]];
          $params['recurrence']['monthly_week_day'] = $recurring_parameters['entity_status_2'][$entity_status_exploded[1]];
        } else if ($objectRef->limit_to) {
          $params['recurrence']['monthly_day'] = $recurring_parameters['limit_to'];
        }
      }

      // Set number of repetition specific fields.
      if ($recurring_parameters['start_action_offset']) {
        $params['recurrence']['end_times'] = $recurring_parameters['start_action_offset'];
      } else {
        $params['recurrence']['end_date_time'] = $recurring_parameters['absolute_date'];
      }

      //Civi::log()->debug(__FUNCTION__, ['$params' => $params]);

      //Update recurrent meeting portion
      $outcome = CRM_Civizoom_Zoom::updateZoomMeeting($zoomId, $params);
    }

  }

  if ($op == 'create' && $objectName == 'Event') {

    $zoomId = CRM_Civizoom_Zoom::getEventZoomMeetingId($objectId);

    // Custom field starting with 'n' create new Zoom Meeting
    if (substr($zoomId, 0, 1) == 'n' && CRM_Civizoom_Zoom::getZoomObject()) {

      try {

        //Pseudocode
        //if retrieved meeting is not n0 && id start / end time is changed
        //update meeting using PATCH
        //update custom field value if needed

        $contactId = civicrm_api3('Contact', 'getsingle', [
          'return' => ["email"],
          'id' => "user_contact_id",
        ]);

        $start_time = date('Y-m-d\TH:i:s', strtotime(strval($objectRef->start_date)));
        $start_time_date = strtotime(strval($objectRef->start_date));
        $end_time_date = strtotime(strval($objectRef->end_date));
        $duration = round(abs($end_time_date - $start_time_date) / 60, 2);

        $params = [
          'topic' => strval($objectRef->title),
          'type' => 2,
          'pre_schedule' => 0,
          'start_time' => strval($start_time),
          'duration' => intval($duration),
          'schedule_for' => strval($contactId['email']),
          'agenda' => strval($objectRef->description),
          'settings' => [
            'host_video' => 0,
            'participant_video' => 0,
            'approval_type' => 0,
            'alternative_hosts'=> $contactId['email'], // TODO: Have to add multiple hosts, based on org relationships
            'waiting_room' => 1,
            'registrants_email_notification' => 1,
            'registrants_confirmation_email' => 1,

          ],
        ];

        // Create meeting with params & logged in user email
        $zoomReg = CRM_Civizoom_Zoom::createZoomMeeting($zoomId, $params, $contactId['email']);

        if (empty($zoomReg['code'])) {
          $zoomMtg = CRM_Core_BAO_CustomField::getCustomFieldID('zoom_meeting', 'civizoom', TRUE);

          $eventParams = [
            'id' => $objectId,
            $zoomMtg => 'm'.strval($zoomReg['id']),
          ];

          //Civi::log()->debug(__FUNCTION__, ['$eventParams' => $eventParams]);
          civicrm_api3('Event', 'create', $eventParams);
        }

      }
      catch (CiviCRM_API3_Exception $e) {
        Civi::log()->debug(__FUNCTION__, ['$e' => $e]);
      }

    }

  }

  if ($op == 'edit' && $objectName == 'Event') {

    $zoomId = CRM_Civizoom_Zoom::getEventZoomMeetingId($objectId);

    // Custom field starting with 'n' create new Zoom Meeting
    if (substr($zoomId, 0, 1) == 'n' && CRM_Civizoom_Zoom::getZoomObject()) {

      try {

        //Pseudocode
        //if retrieved meeting is not n0 && id start / end time is changed
        //update meeting using PATCH
        //update custom field value if needed

        $contactId = civicrm_api3('Contact', 'getsingle', [
          'return' => ["email"],
          'id' => "user_contact_id",
        ]);

        $start_time = date('Y-m-d\TH:i:s', strtotime(strval($objectRef->start_date)));
        $start_time_date = strtotime(strval($objectRef->start_date));
        $end_time_date = strtotime(strval($objectRef->end_date));
        $duration = round(abs($end_time_date - $start_time_date) / 60, 2);

        $params = [
          'topic' => strval($objectRef->title),
          'type' => 2,
          'pre_schedule' => 0,
          'start_time' => strval($start_time),
          'duration' => intval($duration),
          'schedule_for' => strval($contactId['email']),
          'agenda' => strval($objectRef->description),
          'settings' => [
            'host_video' => 0,
            'participant_video' => 0,
            'approval_type' => 0,
            'alternative_hosts'=> $contactId['email'], // TODO: Have to add multiple hosts, based on org relationships
            'waiting_room' => 1,
            'registrants_email_notification' => 1,
            'registrants_confirmation_email' => 1,

          ],
        ];

        // Create meeting with params & logged in user email
        $zoomReg = CRM_Civizoom_Zoom::createZoomMeeting($zoomId, $params, $contactId['email']);

        if (empty($zoomReg['code'])) {
          $zoomMtg = CRM_Core_BAO_CustomField::getCustomFieldID('zoom_meeting', 'civizoom', TRUE);

          $eventParams = [
            'id' => $objectId,
            $zoomMtg => 'm'.strval($zoomReg['id']),
          ];

          //Civi::log()->debug(__FUNCTION__, ['$eventParams' => $eventParams]);
          civicrm_api3('Event', 'create', $eventParams);
        }

      }
      catch (CiviCRM_API3_Exception $e) {
        Civi::log()->debug(__FUNCTION__, ['$e' => $e]);
      }

    }

  }

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