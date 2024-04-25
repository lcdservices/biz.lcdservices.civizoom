# CiviZoom

This extension integrates CiviCRM events with Zoom meetings and webinars. As people register for events in CiviCRM they will be added as registrants to the linked Zoom meeting or webinar. 

# Configuration

After installing the extension you must configure your API connection to Zoom and configure your participant settings.

1. Login to your Zoom account. Note that you must have a paid account. The free Zoom account does not allow API create actions.
2. From the left sidebar click Advanced > App Marketplace.
3. From the top menu, select the Develop dropdown and choose Build Server-to-Server App.
4. Fill in the App details
5. Select the following scopes (Note: you likely need to be logged in as the Owner user to assign these scopes): 
* **View all user meetings** /meeting:read:admin
* **View and manage all user meetings** /meeting:write:admin
* **View all user information** /user:read:admin
* **View all user Webinars** /webinar:read:admin
* **View and manage all user Webinars** /webinar:write:admin
6. Continue to the activation step and Activate the app.
7. Load the App Credentials tab to retrieve the **Account ID**, **Client ID**, and **Client Secret**.
7. In CiviCRM, navigate to Administer > CiviEvent > CiviZoom Settings. Fill in the authentication fields with the values retrieved in the previous step.
7. On the settings screen, select which participant statuses will result in the user being registered for the event and which statuses will result in a cancellation. Select which roles will be registered in Zoom.
8. Save the settings.
9. Create an event. Toward the bottom of the Info and Settings screen you will see a CiviZoom fieldset where you will select the Zoom meeting to connect to this event.
 
Once configured, new registrations will be added to the linked Zoom meeting and cancelled registrations will be removed. The user will receive an email from Zoom notifying them that they have been added to the meeting.

In addition, the user-specific join URL is stored in the event registration and can be referenced in emails and scheduled reminders through tokens.

Note: Your Zoom meeting must have registration enabled or no syncing will be triggered.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7+
* CiviCRM 5.x

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl biz.lcdservices.civizoom@https://github.com/lcdservices/biz.lcdservices.civizoom/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

## Release Notes

* 3.0: JWT authentication removed (no longer supported by Zoom). OAuth authentication implemented. If upgrading you *must* create new authentication credentials and update the configuration settings for the extension. Special thanks to Agileware (https://github.com/agileware/au.com.agileware.zoomzoom) whose OAuth implementation I used.
* 2.0: Now supports Zoom webinars as well as meetings. From the CiviCRM interface no distinction is made between a meeting or webinar other than the suffix appended to the event name. Registrations and cancellations work identically.
* 2.1: Added role-based setting.
* 2.2: Fix bug impacting what meetings are available for selection. Changed setting so that only upcoming meetings are available for selection. This presents a problem, as past Zoom meetings tied to past events will not display the selection as they are no longer available, but the alternative (retrieving all meetings) is not sustainable as the list of meetings grows. Future work will attempt to address this more fully. 