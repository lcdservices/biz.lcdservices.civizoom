# CiviZoom

This extension integrates CiviCRM events with Zoom meetings. As people register for events in CiviCRM they will be added as registrants to the linked Zoom meeting. 

# Configuration

After installing the extension you must configure your API connection to Zoom.

1. Login to your Zoom account. Note that you must have a paid account. The free Zoom account does not allow API create actions.
2. From the left sidebar blick Advanced > App Marketplace.
3. From the top menu, select the Develop dropdown and choose Build App.
4. Select JWT. Complete the form and note the API Key and API Secret.
5. In CiviCRM, navigate to Administer >  CiviZoom Settings. Fill in the API Key and API Secret fields with the values generated in the previous step.
6. On the settings screen, select which participant statuses will result in the user being registered for the event and which statuses will result in a cancellation.
7. Save the settings.
8. Create an event. Toward the bottom of the Info and Settings screen you will see a CiviZoom fieldset where you will select the Zoom meeting to connect to this event.
 
Once configured, new registrations will be added to the linked Zoom meeting and canceled registrations will be removed. The user will receive an email from Zoom notifying them that they have been added to the meeting.

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
