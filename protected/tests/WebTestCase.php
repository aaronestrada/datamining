<?php

/**
 * Change the following URL based on your server configuration
 * Make sure the URL ends with a slash so that we can use relative URLs in lab3 cases
 */
define('TEST_BASE_URL','http://localhost/testdrive/index-lab3.php/');

/**
 * The base class for functional lab3 cases.
 * In this class, we set the base URL for the lab3 application.
 * We also provide some common methods to be used by concrete lab3 classes.
 */
class WebTestCase extends CWebTestCase
{
	/**
	 * Sets up before each lab3 method runs.
	 * This mainly sets the base URL for the lab3 application.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->setBrowserUrl(TEST_BASE_URL);
	}
}
