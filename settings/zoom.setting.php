<?php

return [
  'civizoom_api_key' => [
    'name' => 'civizoom_api_key',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'title' => ts('Zoom JWT API Key'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 10]],
  ],
  'civizoom_api_secret' => [
    'name' => 'civizoom_api_secret',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'title' => ts('Zoom JWT API Secret'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 10]],
  ],
  'civizoom_status_register' => [
    'name' => 'civizoom_status_register',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'String',
    'html_type' => 'checkboxes',
    'serialize' => CRM_Core_DAO::SERIALIZE_SEPARATOR_BOOKEND,
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => ts('Participant Register Statuses'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 10]],
  ],
  'civizoom_status_cancel' => [
    'name' => 'civizoom_status_cancel',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'String',
    'html_type' => 'checkboxes',
    'serialize' => CRM_Core_DAO::SERIALIZE_SEPARATOR_BOOKEND,
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => ts('Participant Cancel Statuses'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 10]],
  ],
  'civizoom_role_register' => [
    'name' => 'civizoom_role_register',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'String',
    'html_type' => 'checkboxes',
    'serialize' => CRM_Core_DAO::SERIALIZE_SEPARATOR_BOOKEND,
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantRole'
    ],
    'title' => ts('Participant Register Roles'),
    'description' => ts('Indicate which participant roles Zoom registration should be processed for.'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 10]],
  ],
];
