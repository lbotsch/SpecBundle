
SpecBundle &copy; Lukas Botsch
==============================




Introduction
============


Install
=======

Prerequisits
------------

SpecBundle builds on PHPUnit and requires PHPUnit (3.5)
The recommended way to install PHPUnit is by using PEAR
Read the excelent documentation:
<http://www.phpunit.de/manual/current/en/installation.html>

Getting the code
----------------


Manual installation
-------------------

*   Copy the *SpecBundle/* directory into your projects *src/Bundle*
    directory.
*   Edit your *app/AppKernel.php* file and register the bundle
        public function registerBundles()
        {
            $bundles = array(
                ...
                // register your bundles
                new Bundle\SpecBundle\SpecBundle(), // Register the SpecBundle
                ...
            );
            ...
        }
*   
Writing Specs
=============

Example:

    class MySpec extends MySpecBase
    {
        /**
         * @scenario
         */
        public function myScenario() {
            $this->given('Default context')
                 ->when('Go to page', '/greeting/NAME')
                 ->then('Page should contain', array('type' => 'regex', 'pattern' => 'Hello NAME'));
        }
    }


Predefined Actions
==================

GIVEN: The initialization
-------------------------

 *    Default context:
      Initializes your application kernel and a web client instance
      is saved to $world["client"].
      Arguments:
      -    array The following options are passed to the kernel
           'env'    => The environment to run in
           'debug'  => Enable debugging
           Anything else is passed to the client as SERVER options (see $_SERVER)

WHEN: The interaction
---------------------

 *    Go to page:
      Uses the client if initialized in the GIVEN section to make
      a request to a given url and saves a dom crawler for the requested
      page in $world["crawler"].
      Arguments:
      -    string URL to navigate to
      -    string HTTP method (defaults to 'GET')


THEN: The checking
------------------



Writing custom actions
======================

