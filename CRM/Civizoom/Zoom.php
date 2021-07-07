<?php

//https://marketplace.zoom.us/docs/api-reference/zoom-api/

class CRM_Civizoom_Zoom {
  /**
   * @return ZoomAPIWrapper|null
   */
  static function getZoomObject() {
    $apiKey = Civi::settings()->get('civizoom_api_key');
    $apiSecret = Civi::settings()->get('civizoom_api_secret');

    if (empty($apiKey) || empty($apiSecret)) {
      return NULL;
    }

    $extPath = Civi::resources()->getPath(CRM_Civizoom_ExtensionUtil::LONG_NAME);
    require_once $extPath.'/packages/ZoomAPIWrapper/ZoomAPIWrapper.php';

    $zoom = new ZoomAPIWrapper($apiKey, $apiSecret);
    //Civi::log()->debug(__FUNCTION__, ['$zoom' => $zoom]);

    return $zoom;
  }

  /**
   * @return mixed
   */
  static function getUsers() {
    $zoom = self::getZoomObject();
    $users = $zoom->doRequest('GET', '/users', ['status'=>'active']);

    return $users['users'];
  }

  /**
   * @param bool $current
   * @return array|mixed
   */
  static function getMeetingIds($current = TRUE) {
    $users = self::getUsers();
    $zoom = self::getZoomObject();
    $meetings = [];
    //Civi::log()->debug(__FUNCTION__, ['users' => $users]);

    $params = [
      'page_size' => 300,
      'type' => ($current) ? 'upcoming' : 'scheduled',
    ];

    foreach ($users as $user) {
      $meeting = $zoom->doRequest('GET', '/users/{userId}/meetings', $params, ['userId' => $user['id']]);

      if (!empty($meeting['meetings'])) {
        //get full details about each webinar so we determine if registration is enabled
        foreach ($meeting['meetings'] as $key => $meetingSummary) {
          $details = $zoom->doRequest('GET', '/meetings/{meetingId}',
            $params, ['meetingId' => $meetingSummary['id']]);
          //Civi::log()->debug(__FUNCTION__, ['$details' => $details]);

          if ($details['settings']['approval_type'] == 2) {
            unset($meeting['meetings'][$key]);
          }
        }

        $meetings += $meeting['meetings'];
      }
    }
    //Civi::log()->debug(__FUNCTION__, ['$meetings' => $meetings]);

    return $meetings;
  }

  /**
   * @param bool $current
   * @return array|mixed
   */
  static function getWebinarIds() {
    $users = self::getUsers();
    $zoom = self::getZoomObject();
    $webinars = [];

    $params = [
      'page_size' => 300,
    ];

    foreach ($users as $user) {
      $webinar = $zoom->doRequest('GET', '/users/{userId}/webinars', $params, ['userId' => $user['id']]);
      //Civi::log()->debug(__FUNCTION__, ['$webinar' => $webinar]);

      if (!empty($webinar['webinars'])) {
        //get full details about each webinar so we determine if registration is enabled
        foreach ($webinar['webinars'] as $key => $webinarSummary) {
          $details = $zoom->doRequest('GET', '/webinars/{webinarId}',
            $params, ['webinarId' => $webinarSummary['id']]);
          //Civi::log()->debug(__FUNCTION__, ['$details' => $details]);

          if ($details['settings']['approval_type'] == 2) {
            unset($webinar['webinars'][$key]);
          }
        }

        $webinars += $webinar['webinars'];
      }
    }
    //Civi::log()->debug(__FUNCTION__, ['$webinars' => $webinars]);

    return $webinars;
  }

  /**
   * @param $eventId
   * @return array|null
   */
  static function getEventZoomMeetingId($eventId) {
    try {
      $zoomMtg = CRM_Core_BAO_CustomField::getCustomFieldID('zoom_meeting', 'civizoom', TRUE);
      $result = civicrm_api3('Event', 'getvalue', [
        'return' => $zoomMtg,
        'id' => $eventId,
      ]);
    }
    catch (CiviCRM_API3_Exception $e) {}

    return $result ?? NULL;
  }

  /**
   * @param $zoomId
   * @param $params
   * @return false|mixed
   */
  static function createZoomRegistration($zoomId, $params) {
    //Civi::log()->debug(__FUNCTION__, ['$zoomId' => $zoomId, '$params' => $params]);

    $zoom = self::getZoomObject();
    $json = json_encode($params);

    $api = 'meetings';
    if (substr($zoomId, 0, 1) == 'w') {
      $api = 'webinars';
    }

    $zoomId = substr($zoomId, 1);
    //Civi::log()->debug(__FUNCTION__, ['$zoomId' => $zoomId, '$api' => $api]);

    $response = $zoom->doRequest('POST',"/{$api}/{zoomId}/registrants", [],
      ['zoomId' => $zoomId], $json);
    //Civi::log()->debug(__FUNCTION__, ['$response' => $response]);

    return $response;
  }

  /**
   * @param $zoomId
   * @param $registrantId
   * @param $email
   * @return false|mixed
   */
  static function cancelZoomRegistration($zoomId, $registrantId, $email) {
    $zoom = self::getZoomObject();

    $api = 'meetings';
    if (substr($zoomId, 0, 1) == 'w') {
      $api = 'webinars';
    }

    $zoomId = substr($zoomId, 1);

    $params = [
      'action' => 'cancel',
      'registrants' => [
        [
          'id' => $zoomId,
          'email' => $email,
        ],
      ],
    ];
    $json = json_encode($params);
    //Civi::log()->debug(__FUNCTION__, ['$params' => $params]);

    $response = $zoom->doRequest('PUT',"/{$api}/{zoomId}/registrants/status", [],
      ['zoomId' => $zoomId], $json);
    //Civi::log()->debug(__FUNCTION__, ['$response' => $response]);

    return $response;
  }

  /**
   * @param string $type
   * @return array|string|null
   */
  static function getConfiguredStatuses($type = 'register') {
    $statuses = Civi::settings()->get("civizoom_status_{$type}");
    $statuses = CRM_Utils_Array::explodePadded($statuses);
    //Civi::log()->debug(__FUNCTION__, ['$statuses' => $statuses]);

    return $statuses;
  }

  static function getConfiguredRoles() {
    $roles = Civi::settings()->get('civizoom_role_register');
    $roles = CRM_Utils_Array::explodePadded($roles);
    //Civi::log()->debug(__FUNCTION__, ['$roles' => $roles]);

    return $roles;
  }
}
