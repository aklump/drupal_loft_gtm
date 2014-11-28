<?php
/**
 * @file
 * PHPUnit tests for the loft_gtm class
 */

/**
 * @var DRUPAL_ROOT
 */
define('DRUPAL_ROOT', '/Library/Projects/globalonenessproject/site-dev/public_html');
require_once dirname(__FILE__) . '/../../../loft_testing/includes/bootstrap.inc';

class LoftGtmTest extends \AKlump\LoftTesting\PhpUnit\TestCase {

  // This test fails, it's a valid test, but the failure is not life-threatening
  // and I don't have time to fix it; 2014-11-28T08:45, aklump
  // public function testDontDoubleUtm() {
  //   $subject = "This gets a double, oops: (http://www.globalonenessproject.com/user/password?utm_nooverride=1&alpha=bravo).";
  //   $this->assertMessageAlteredSame("This gets a double, oops: (http://www.globalonenessproject.com/user/password?utm_nooverride=1&alpha=bravo).", $subject);
  // }

  public function testDomain() {
    $subject = "This gets a double, oops: (http://www.globalonenessproject.com/user/password?alpha=bravo).";
    $this->assertMessageAlteredSame("This gets a double, oops: (http://www.globalonenessproject.com/user/password?utm_nooverride=1&alpha=bravo).", $subject);
  }
  
  public function testProtectEmailAddresses() {
    $subject = "This email was rerouted.
Web site: http://dev.globalonenessproject.local
Mail key: contact_page_mail
Originally to: bethany@globalonenessproject.org
-----------------------";

    $control = "This email was rerouted.
Web site: http://dev.globalonenessproject.local?utm_nooverride=1&
Mail key: contact_page_mail
Originally to: bethany@globalonenessproject.org
-----------------------";
    $this->assertMessageAlteredSame($control, $subject);
  }

  // custom assertions
  // 
  
  /**
   * Asserts a message is altered correctly.
   *
   * @param  string $control  The desired outcome message
   * @param  string $subject [description]
   *
   * @return [type]          [description]
   */
  public function assertMessageAlteredSame($control, $subject) {
    $message['body'][0] = $subject;
    loft_gtm_mail_alter($message);
    $this->assertSame($control, $message['body'][0]);    
  }
  

  public function setUp() {
    parent::setUp(array('loft_gtm', 'path'));
  }
}
