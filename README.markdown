
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

 *    __Default context__:
      Initializes your application kernel and a web client instance
      is saved to $world["client"].
      Arguments:
      -    _array_ Options
      
           The following options are passed to the kernel
           -    _'env'_    => The environment to run in
           -    _'debug'_  => Enable debugging
           Anything else is passed to the client as SERVER options (_see $\_SERVER_)

WHEN: The interaction
---------------------

 *    __Go to page__:
      Uses the client if initialized in the GIVEN section to make
      a request to a given url and saves a dom crawler for the requested
      page in $world["crawler"].
      Arguments:
      -    _string_ URL to navigate to
      -    _string_ HTTP method (defaults to 'GET')
      -    _array_ (_optional_) POST data in *name => value* format
      Example:
      
          ->when('Go to page', '/home')

 *    __Click link__:
      Finds a link in the current page and clicks on it
      Arguments:
      -    _array_ Locator options for the link
           -    'label'    => (_regex_) Tries to find a link by its content
           -    'id'       => (_string_) Find a link by its id
           -    'css'      => (_string_) Find a link using a css locator
           -    'xpath'    => (_string_) Find a link using xpath
      Example:
      
          ->when('Click link', array(
              'css' => 'a#my-link',
          ))

 *    __Fill form__:
      Fills in a form and submits it
      Arguments:
      -    _array_ Locator options for the form
           -    'label'    => (_regex_) Tries to find a form by its content
           -    'id'       => (_string_) Find a form by its id
           -    'css'      => (_string_) Find a form using a css locator
           -    'xpath'    => (_string_) Find a form using xpath
      -    _array_ Fields
           -    'name'     => (_string_) Tries to find a form field by its name
           -    'label'    => (_regex_) Tries to find a form field by its label
           -    'id'       => (_string_) Find a form field by its id
           -    'css'      => (_string_) Find a form field using a css locator
           -    'xpath'    => (_string_) Find a form field using xpath
           -    'value'    => (_string_ or _bool_) The fields value (use bool for checkbox)
      -    _bool_ Submit the form (Defaults to true)
      Example:
          
          ->when('Fill form', array(
              'css' => 'form#my-form',
          ), array(
              array('name' => 'name', 'value' => 'Lukas'),
              array('name' => 'email', 'value' => 'lukas{DOT}botsch[AT]gmail{DOT}com'),
          ), true)
      
THEN: The checking
------------------



Writing custom actions
======================

