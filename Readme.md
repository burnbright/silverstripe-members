# SilverStripe Members Module

A simplified light-weight alternative to the [member profiles module](https://github.com/ajshort/silverstripe-memberprofiles). All configuration is handled by developer, rather than in CMS.

Adds various (optional) extra member features. They will not all be enabled by default.

 * Registration page
 * Profile page for updating details.
 * Send temporary password via email.
 
## Registration Page

Becasue the registration page page does not have (or need) a Page model. Add the following director rules to your `_config/config.yml` file:

```yaml
Director:
  rules:
    'register//$Action/$ID': 'MemberRegistrationPage_Controller'
MemberRegistrationPage_Controller:
    enabled: true
```

This will enable registering at `mysite.com/profile`.

## Member Profile Page

Becasue the member profile page does not have (or need) a Page model. To add a profile page to your site, add the following to your _config.php:

```yaml
Director:
  rules:
    'profile//$Action/$ID': 'MemberProfilePage_Controller'
MemberProfilePage_Controller:
    enabled: true
```

Once configured, you can edit your profile at `mysite.com/profile`.

### Update Notifications

You can configure front-end member profile updates to be notified to the administrator via email.

```yaml
Member:
    send_frontend_update_notifications: true
```

You can optionally restrict these notifications to only be sent when specific fields are changed.

```yaml
Member:
    frontend_update_notification_fields: 
      - Email
      - Phone
```

## Temporary Password Email

This is enabled by default.

## CSV Export Fields

This module introduces a way to define `export_fields` to for CSV exporting in yaml:

```yaml
Member:
  export_fields:
    FirstName: 'First Name'
    Surname: 'Last Name'
    Organisation.Name: 'Business Name'
    Email: 'Email Address'
```

## Maintainer

Jeremy Shipman, Jedateach, jeremy [at] burnbright.net
