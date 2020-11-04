<?php

//https://marketplace.zoom.us/docs/api-reference/zoom-api/

class CRM_Civizoom_Zoom {
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

  static function getUsers() {
    $zoom = self::getZoomObject();
    $users = $zoom->doRequest('GET', '/users', ['status'=>'active']);

    return $users['users'];
  }

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
      $meetings += $meeting['meetings'];
    }
    //Civi::log()->debug(__FUNCTION__, ['$meetings' => $meetings]);

    return $meetings;
  }

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

  static function createMeetingRegistration($meetingId, $params) {
    $zoom = self::getZoomObject();
    $json = json_encode($params);

    $response = $zoom->doRequest('POST','/meetings/{meetingId}/registrants', [],
      ['meetingId' => $meetingId], $json);
    //Civi::log()->debug(__FUNCTION__, ['$response' => $response]);

    return $response;
  }

  static function cancelMeetingRegistration($meetingId, $registrantId, $email) {
    $zoom = self::getZoomObject();
    $params = [
      'action' => 'cancel',
      'registrants' => [
        [
          'id' => $registrantId,
          'email' => $email,
        ],
      ],
    ];
    $json = json_encode($params);
    //Civi::log()->debug(__FUNCTION__, ['$params' => $params]);

    $response = $zoom->doRequest('PUT','/meetings/{meetingId}/registrants/status', [],
      ['meetingId' => $meetingId], $json);
    //Civi::log()->debug(__FUNCTION__, ['$response' => $response]);

    return $response;
  }

  static function getConfiguredStatuses($type = 'register') {
    $statuses = Civi::settings()->get("civizoom_status_{$type}");
    $statuses = CRM_Utils_Array::explodePadded($statuses);
    //Civi::log()->debug(__FUNCTION__, ['$statuses' => $statuses]);

    return $statuses;
  }
}
