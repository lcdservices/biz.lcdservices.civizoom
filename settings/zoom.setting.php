<?php

use CRM_Civizoom_ExtensionUtil as E;

return [
  'civizoom_account_id' => [
    'name' => 'civizoom_account_id',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'html_attributes' => [
      'class' => 'huge',
    ],
    'title' => E::ts('Zoom Account ID'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 1]],
  ],
  'civizoom_client_key' => [
    'name' => 'civizoom_client_key',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'html_attributes' => [
      'class' => 'huge',
    ],
    'title' => E::ts('Zoom OAuth Client ID'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 2]],
  ],
  'civizoom_client_secret' => [
    'name' => 'civizoom_client_secret',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'String',
    'html_type' => 'text',
    'html_attributes' => [
      'class' => 'huge',
    ],
    'title' => E::ts('Zoom OAuth Client Secret'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 3]],
    'description' => E::ts('Zoom OAuth API credentials to use for this CiviCRM integration. Create a Sever-to-Server OAuth app in the <a href="https://marketplace.zoom.us/develop/create" target="_blank">Zoom Marketplace</a>. For more details read, <a href="https://developers.zoom.us/docs/internal-apps/create/" target="_blank">Create a Server-to-Server OAuth app</a>.'),
  ],
  'civizoom_status_register' => [
    'name' => 'civizoom_status_register',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'Array',
    'html_type' => 'select',
    'html_attributes' => [
      'class' => 'crm-select2',
      'multiple' => TRUE,
    ],
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => E::ts('Participant Register Statuses'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 10]],
    'description' => E::ts('Select participant statuses that should trigger registering the contact for the linked Zoome event.'),
  ],
  'civizoom_status_cancel' => [
    'name' => 'civizoom_status_cancel',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'Array',
    'html_type' => 'select',
    'html_attributes' => [
      'class' => 'crm-select2',
      'multiple' => TRUE,
    ],
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantStatus'
    ],
    'title' => E::ts('Participant Cancel Statuses'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 10]],
    'description' => E::ts('Select participant statuses that should cancel the contact\'s registration for the linked Zoome event.'),
  ],
  'civizoom_role_register' => [
    'name' => 'civizoom_role_register',
    'group' => 'civizoom',
    'group_name' => 'CiviZoom Settings',
    'type' => 'Array',
    'html_type' => 'select',
    'html_attributes' => [
      'class' => 'crm-select2',
      'multiple' => TRUE,
    ],
    'pseudoconstant' => [
      'callback' => 'CRM_Event_PseudoConstant::participantRole'
    ],
    'title' => E::ts('Participant Register Roles'),
    'description' => E::ts('Indicate which participant roles Zoom registration should be processed for.'),
    'is_domain' => 1,
    'is_contact' => 0,
    'settings_pages' => ['civizoom' => ['weight' => 10]],
  ],
];
