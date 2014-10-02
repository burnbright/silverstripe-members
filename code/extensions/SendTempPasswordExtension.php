<?php
/**
 * Provides a to generate and send a password to a user.
 */
class TemporaryPasswordExtension extends DataExtension{
	
	private $called = false;

	/**
	 * Add 'Send temp password' action to cms
	 */
	public function updateCMSFields(FieldList $fields) {
		//requirements: ajax link
		if(!$this->called && $this->owner->hasMethod("Link")) //hack because member getCMSFields calls parent, which also has the extend->('updateCMSFields')
			$fields->addFieldToTab('Root.Actions', 
				new LiteralField('TempPasswordLink',
					'<a href="'.$this->owner->Link('sendnewpassword').'" target="new">send temp password</a>'
				)
			);
		$this->called = true;
	}

	/**
	 * Create the temporary password, and set it.
	 */
	public function setupTempPassword($expires = false){
		if($expires){
			$this->owner->PassworExpiry = date($expires); //TODO: not working yet
		}
		$password = substr(md5(microtime()),0,6);
		$this->owner->changePassword($password);
		return $password;
	}
	
	/**
	 * Send temporary password to user via email.
	 */
	public function sendTempPasswordEmail($template = null, $subject = null, $extradata = null){
		//set expiry
		$template = ($template) ? $template : 'TempPasswordEmail';
		$subject = ($subject) ? $subject : "Temporary Password";
		
		$data = array(
			'CleartextTempPassword' => $this->owner->setupTempPassword()
		);
		if($extradata){
			$data = array_merge($data, $extradata);
		}
		$body = $this->owner->customise($data)->renderWith($template);
		
		if(Email::validEmailAddress($this->owner->Email)){
			$email = new Email(
				Email::getAdminEmail(),
				$this->owner->Email,
				$subject,
				$body
			);
			if($email->send()){
				return true;
			}
			return false;
		}
		return false;
	}
	
}
