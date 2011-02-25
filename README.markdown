
SpecBundle &copy; Lukas Botsch
==============================

<h2>Content</h2>

*  [Introduction](#introduction)
*  [Install](#install)
  *  [Prerequisits](#prerequisits)
  *  [Getting the code](#getting-the-code)
  *  [Manual installation](#manual-installation)
*  [Writing Specs](#writing-specs)
  *  [Using the console](#using-the-console)
  *  [Example Spec class](#example-spec-class)
*  [Predefined actions](#predefined-actions)
  *  [GIVEN](#predefined-given)
  *  [WHEN](#predefined-when)
  *  [THEN](#predefined-then)
*  [Writing custom actions](#writing-custom-actions)


<h2 id="introduction">Introduction</h2>



<h2 id="install">Install</h2>

<h3 id="prerequisits">Prerequisits<h3>

SpecBundle builds on PHPUnit and requires PHPUnit (3.5)
The recommended way to install PHPUnit is by using PEAR
Read the excelent documentation:
<http://www.phpunit.de/manual/current/en/installation.html>

<h3 id="getting-the-code">Getting the code</h3>


<h3 id="manual-installation">Manual installation</h3>

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

<h2 id="writing-specs">Writing Specs</h2>

<h3 id="using-the-console">Using the console</h3>

SpecBundle adds the command *init:spec* to the symfony command line tool.
You can use it to initialize a new Spec definition for your bundle. It will
generate a Spec for a simple CRUD interface.

Read the manual page for instructions:

    app/console --help init:spec


<h3 id="example-spec-class">Example Spec class</h3>

    class SymfonySpec extends SymfonySpecBase
    {
        /**
         * @scenario
         */
        public function masterSymfony() {
            $this->given('You don\'t know Symfony')
                 ->when('You get started with it')
                 ->then('You will never want anything else and eventually become MASTER OF SYMFONY');
        }
    }


The *SymfonySpec* class extends *SymfonySpecBase*. You can find the base class in the *Tests/Spec/Base/*
subdirectory of your bundle. In there you can add your custom actions. Read more about custom
actions in the [Write custom actions](#write-custom-actions) section.

Inside the SymfonySpec class, you define scenarios as methods. You have to annotate your scenario
methods with **@scenario**. Inside your scenario, you can use the simple DSL exposed by the spec
API.

As SpecBundle uses the [PHPUnit Story extension][], you can have a look at the documentation to get
an overview of its functionality.

  [PHPUnit Story extension]: <http://www.phpunit.de/manual/current/en/behaviour-driven-development.html> "PHPUnit documentation"

From the [documentation][PHPUnit Story extension]:

> The PHPUnit_Extensions_Story_TestCase class adds a story framework that faciliates the definition of a Domain-Specific Language
> for Behaviour-Driven Development.
> Inside a scenario, **given()**, **when()**, and **then()** each represent a *step*. **and()** is the same kind as the previous step.
> The PHPUnit_Extensions_Story_TestCase class adds a story framework that faciliates the definition of a Domain-Specific Language
> for Behaviour-Driven Development. Inside a scenario, given(), when(), and then() each represent a step. and() is the same kind as the previous step. The following methods are declared abstract in PHPUnit_Extensions_Story_TestCase and need to be implemented:
>
>   *  runGiven(&$world, $action, $arguments)
>   *  runWhen(&$world, $action, $arguments)
>   *  runThen(&$world, $action, $arguments)
 
As your Specs inherit from PHPUnit_Extensions_Story_TestCase, you can follow the instructions from the PHPUnit documentation and
implement the above methods to declare your DSL. But SpecBundle already implements its own DSL that you can use and extend
as you need. You will learn more about the built-in actions in the [Predefined Actions](#predefined-actions) section. 

The *masterSymfony* scenario uses three types of steps: *given*, *when* and *then*.
 *  _given_ describes the initial world: *You don't know Symfony*
 *  _when_ describes the action that is made to the world: You learn Symfony
 *  _then_ describes the final state of the world: You master Symfony

Now read about the built-in actions to be able to write some *useful* specs ;)

<h2 id="predefined-actions">Predefined Actions</h2>

SpecBundle has some built-in actions you can use to define your specs. Each *step type* has a number of useful built-in actions:

<h3 id="predefined-given">GIVEN: The initialization<h3>

 *    __Default context__:
      Initializes your application kernel and a web client instance
      is saved to $world["client"].
      
      Arguments:
      
      -    _array_ Options
      
          The following options are passed to the kernel
          
          -    'env'      => The environment to run in
          -    'debug'    => Enable debugging
          
          Anything else is passed to the client as SERVER options (_see $\_SERVER_)
      
      Example:
      
          ->given('Default context', array(
              'env' => 'test',
              'HTTP_SERVER' => 'localhost',
              'HTTP_USER_AGENT' => 'spectest client v1.0',
          ))


<h3 id="predefined-when">WHEN: The interaction</h3>

 *    __Go to page__:
      Uses the client if initialized in the GIVEN section to make
      a request to a given url and saves a dom crawler for the requested
      page in $world["crawler"].
      
      Arguments:
      
      -  _string_ URL to navigate to
      -  _string_ HTTP method (defaults to 'GET')
      -  _array_ (_optional_) POST data in *name => value* format
      
      Example:
      
          ->when('Go to page', '/home')

 *    __Click link__:
      Finds a link in the current page and clicks on it
      Arguments:
      
      -  _array_ Locator options for the link
         -  'label'    => (_regex_) Tries to find a link by its content
         -  'id'       => (_string_) Find a link by its id
         -  'css'      => (_string_) Find a link using a css locator
         -  'xpath'    => (_string_) Find a link using xpath
      
      Example:
      
          ->when('Click link', array(
              'css' => 'a#my-link',
          ))

 *    __Fill form__:
      Fills in a form and submits it
      
      Arguments:
      
      -  _array_ Locator options for the form
         -  'label'    => (_regex_) Tries to find a form by its content
         -  'id'       => (_string_) Find a form by its id
         -  'css'      => (_string_) Find a form using a css locator
         -  'xpath'    => (_string_) Find a form using xpath
      -  _array_ Fields
         -  'name'     => (_string_) Tries to find a form field by its name
         -  'label'    => (_regex_) Tries to find a form field by its label
         -  'id'       => (_string_) Find a form field by its id
         -  'css'      => (_string_) Find a form field using a css locator
         -  'xpath'    => (_string_) Find a form field using xpath
         -  'value'    => (_string_ or _bool_) The fields value (use bool for checkbox)
      -  _bool_ Submit the form (Defaults to true)
      
      Example:
          
          ->when('Fill form', array(
              'css' => 'form#my-form',
          ), array(
              array('name' => 'name', 'value' => 'Lukas'),
              array('name' => 'email', 'value' => 'lukas{DOT}botsch[AT]gmail{DOT}com'),
          ), true)
      
<h3 id="predefined-then">THEN: The checking</h3>



<h2 id="writing-custom-actions">Writing custom actions</h2>

The built-in actions are useful to get you started in writing specs for your applications and bundles. However, you will
most certainly want to write your own actions at some point. Thankfully, this is very easy with SpecBundle.

