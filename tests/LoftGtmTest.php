<?php

class LoftGtmTest extends PHPUnit_Framework_TestCase
{

    public function testEventMethod()
    {
        $obj = $this->dataLayer;
        $obj->set('cartValue', 100.99);
        $obj->event('shopping', 'checkout complete');
        $build = $obj->build();
        $this->assertCount(2, $build);
        $this->assertSame('dataLayer = [{"cartValue":100.99}];', $build[0]);
        $this->assertSame('Drupal.loftGTM.dataLayer.push({"event":"eventTracker","eventCat":"shopping","eventAct":"checkout complete"});', $build[1]);
    }

    public function testDataLayerPushNoRepeatByUuid()
    {
        $obj = $this->dataLayer;
        $obj->push(['event' => 'click'], 'carrot');
        $obj->push(['event' => 'click_again'], 'carrot');
        $obj->push(['event' => 'click'], true);
        $build = $obj->build();
        $this->assertCount(3, $build);
        $this->assertSame('dataLayer = [];', $build[0]);
        $this->assertSame('Drupal.loftGTM.dataLayer.push({"event":"click"});', $build[1]);
        $this->assertSame('Drupal.loftGTM.dataLayer.push({"event":"click"});', $build[2]);
    }

    public function testDataLayerPushNoRepeat()
    {
        $obj = $this->dataLayer;
        $obj->push(['event' => 'click'], true);
        $obj->push(['event' => 'click'], true);
        $build = $obj->build();
        $this->assertCount(2, $build);
        $this->assertSame('dataLayer = [];', $build[0]);
        $this->assertSame('Drupal.loftGTM.dataLayer.push({"event":"click"});', $build[1]);
    }

    public function testDataLayerPush()
    {
        $obj = $this->dataLayer;
        $obj->push(['event' => 'click']);
        $obj->push(['event' => 'click_again']);
        $build = $obj->build();
        $this->assertCount(3, $build);
        $this->assertSame('dataLayer = [];', $build[0]);
        $this->assertSame('Drupal.loftGTM.dataLayer.push({"event":"click"});', $build[1]);
        $this->assertSame('Drupal.loftGTM.dataLayer.push({"event":"click_again"});', $build[2]);
    }

    public function testDataLayerSet()
    {
        $obj = $this->dataLayer;
        $obj->set('cartTotal', 10.99);
        $obj->set('cartTotal', 45.78);
        $build = $obj->build();
        $this->assertCount(1, $build);
        $this->assertSame('dataLayer = [{"cartTotal":45.78}];', $build[0]);
    }

    /**
     * Provides data for testGetDomain.
     */
    function DataForTestGetDomainProvider()
    {
        $tests = array();
        $tests[] = array(
            'http://www.mysite.com/',
            'www.mysite.com',
        );

        return $tests;
    }

    /**
     * @dataProvider DataForTestGetDomainProvider
     */
    public function testGetDomain($url, $control)
    {
        global $mock_url;
        $mock_url = $url;
        $this->assertSame($control, _loft_gtm_get_domain());
    }

    public function testModuleImplementsAlter()
    {
        $group = true;
        $stack = array();
        $stack['loft_gtm'] = $group;
        $stack['views'] = array();
        $stack['block'] = array();

        $control = $stack;
        array_shift($control);
        $control['loft_gtm'] = $group;

        loft_gtm_module_implements_alter($stack, 'mail_alter');
        $this->assertSame($control, $stack);
    }

    /**
     * Provides data for testPreprocessHtml.
     */
    function DataForTestPreprocessHtmlProvider()
    {
        $g = data_api();
        $tests = array();

        $tests[] = array(array(), -1);

        $vars = array();
        $g->set($vars, 'page.page_top.0', array(
            '#markup' => 'do',
            '#weight' => 10,
        ));
        $g->set($vars, 'page.page_top.1', array(
            '#markup' => 're',
            '#weight' => 12,
        ));
        $tests[] = array($vars, -9);

        return $tests;
    }

    /**
     * @dataProvider DataForTestPreprocessHtmlProvider
     */
    public function _testPreprocessHtml($vars, $weight)
    {
        $g = data_api();

        $control = $vars;
        $g->set($control, "page.page_top.loft_gtm_code_noscript.#markup", $this->gtmCodeNoscript, array());
        $g->set($control, "page.page_top.loft_gtm_code_noscript.#weight", -2, array());
        $g->set($control, "page.page_top.loft_gtm_code.#markup", $this->gtmCode, array());
        $g->set($control, "page.page_top.loft_gtm_code.#weight", -1, array());

        loft_gtm_preprocess_html($vars);
        $this->assertSame($control, $vars);
    }

    /**
     * Provides data for testAssertFuncsReturnArrays.
     */
    function DataForTestAssertFuncsReturnArraysProvider()
    {
        $tests = array();
        $tests[] = array('loft_gtm_menu', array(), array());
        $tests[] = array('loft_gtm_permission', array(), array());

        return $tests;
    }

    /**
     * @dataProvider DataForTestAssertFuncsReturnArraysProvider
     */
    public function testAssertFuncsReturnArrays($func, $args, $keys)
    {
        $return = call_user_func_array($func, $args);
        $this->assertInternalType('array', $return);

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $return);
        }
    }

    public function testGtmIsActive()
    {
        $this->assertTrue(loft_gtm_is_active());
        variable_set('loft_gtm_code', null);
        $this->assertFalse(loft_gtm_is_active());
    }

    /**
     * Provides data for testProtectEmailAddresses.
     */
    function DataForTestProtectEmailAddressesProvider()
    {
        $tests = array();
        $tests[] = array(
            "This email was rerouted.
    Web site: http://dev.mysite.local
    Mail key: contact_page_mail
    Originally to: joansey@mysite.org
    -----------------------",
            "This email was rerouted.
    Web site: http://dev.mysite.local?utm_nooverride=1&
    Mail key: contact_page_mail
    Originally to: joansey@mysite.org
    -----------------------",
        );

        $tests[] = array(
            "This gets a double, oops: (http://www.globalonenessproject.com/user/password?alpha=bravo).",
            "This gets a double, oops: (http://www.globalonenessproject.com/user/password?utm_nooverride=1&alpha=bravo).",
        );

        return $tests;
    }

    /**
     * @dataProvider DataForTestProtectEmailAddressesProvider
     */
    public function testProtectEmailAddresses($subject, $control)
    {
        $message['body'][0] = $subject;
        loft_gtm_mail_alter($message);
        $this->assertSame($control, $message['body'][0]);
    }

    public function testMailAlterEmptyArrayBody()
    {
        $message = array();
        $this->assertFalse(loft_gtm_mail_alter($message));
    }

    public function testMailAlterNullBody()
    {
        $message = null;
        $this->assertFalse(loft_gtm_mail_alter($message));
    }

    public function setUp()
    {
        $this->gtmCode = '...';
        $this->gtmCodeNoscript = '...';
        variable_set('loft_gtm_code', $this->gtmCode);
        variable_set('loft_gtm_code_noscript', $this->gtmCodeNoscript);
        $this->dataLayer = new DataLayer('eventTracker');
    }
}
