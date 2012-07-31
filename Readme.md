# SilverStripe Members Module

Jeremy Shipman, Jedateach, jeremy [at] burnbright.net

Adds various extra member features

 * Registration page
 * Profile page
 * Send temp password
 
Profile and Registration pages

To add a profile page to your site, without creating an actual page,
add the following to your _config.php:

	Director::addRules("10", array(
		MemberProfile::$url_segment.'/$Action/$ID' => 'MemberProfile'	
	));


And likewise for the registration page:

	Director::addRules("10", array(
		MemberRegistrationPage_Controller::$url_segment.'/$Action/$ID' => 'MemberRegistrationPage_Controller'	
	));
