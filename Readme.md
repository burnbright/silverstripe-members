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
```

This will enable registering at `mysite.com/profile`.

## Member Profile Page

Becasue the member profile page does not have (or need) a Page model. To add a profile page to your site, add the following to your _config.php:

```yaml
Director:
  rules:
    'profile//$Action/$ID': 'MemberRegistrationPage_Controller'
```

Once configured, you can edit your profile at `mysite.com/profile`.

### Update Notifications

You can configure front-end member profile updates to be notified to the administrator via email.

```yaml
Member:
    send_update_notifications: true
```

## Temporary Password Email

This is enabled by default.

## Maintainer

Jeremy Shipman, Jedateach, jeremy [at] burnbright.net
