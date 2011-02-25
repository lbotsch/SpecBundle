<?php

namespace Bundle\SpecBundle\Spec;

use Bundle\SpecBundle\Spec\ActionCollection;
use Bundle\SpecBundle\Spec\DefaultActionCollection;
use Symfony\Component\Finder\Finder;

/**
 * 
 */
abstract class SpecTestCase extends \PHPUnit_Extensions_Story_TestCase
{
    protected $kernel;
    
    private $actionCollections = array();

    
    public function __construct() {
        $this->init();
        $this->registerActionCollection(new DefaultActionCollection());
    }

    /**
     * Overwrite init() to add your own ActionCollections
     */
    public function init() {}
    
    public function registerActionCollection(ActionCollection $actionCollection) {
        $this->actionCollections[] = $actionCollection;
    }
    
    private function runAction(&$world, $type, $action, $arguments) {
        foreach ($this->actionCollections as $actionCollection) {
            if (false !== $result = $actionCollection->execute($world, $type, $action, $arguments))
                return $result;
        }
        return $this->notImplemented($action);
    }
    
    /**
	 * Implementation for "Given" steps.
     * @param array $world
	 * @param string $action
	 * @param array $arguments
     */
    public function runGiven(&$world, $action, $arguments) {
        return $this->runAction($world, "given", $action, $arguments);
    }
    
    /**
	 * Implementation for "When" steps.
     * @param array $world
	 * @param string $action
	 * @param array $arguments
     */
    public function runWhen(&$world, $action, $arguments) {
        return $this->runAction($world, "when", $action, $arguments);
    }
    
    /**
	 * Implementation for "Then" steps.
     * @param array $world
	 * @param string $action
	 * @param array $arguments
     */
    public function runThen(&$world, $action, $arguments) {
        return $this->runAction($world, "then", $action, $arguments);
    }

    /**
     * Creates a Client.
     *
     * @param array   $options An array of options to pass to the createKernel class
     * @param array   $server  An array of server parameters
     *
     * @return Client A Client instance
     */
    public function createClient(array $options = array(), array $server = array())
    {
        $this->kernel = $this->createKernel($options);
        $this->kernel->boot();

        $client = $this->kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    /**
     * Finds the directory where the phpunit.xml(.dist) is stored.
     *
     * If you run tests with the PHPUnit CLI tool, everything will work as expected.
     * If not, override this method in your test classes.
     *
     * @return string The directory where phpunit.xml(.dist) is stored
     */
    protected function getPhpUnitXmlDir()
    {
        $dir = getcwd();
        if (!isset($_SERVER['argv']) || false === strpos($_SERVER['argv'][0], 'phpunit')) {
            throw new \RuntimeException('You must override the WebTestCase::createKernel() method.');
        }

        // find the --configuration flag from PHPUnit
        $cli = implode(' ', $_SERVER['argv']);
        if (preg_match('/\-\-configuration[= ]+([^ ]+)/', $cli, $matches)) {
            $dir = $dir.'/'.$matches[1];
        } elseif (preg_match('/\-c +([^ ]+)/', $cli, $matches)) {
            $dir = $dir.'/'.$matches[1];
        } elseif (file_exists(getcwd().'/phpunit.xml') || file_exists(getcwd().'/phpunit.xml.dist')) {
            $dir = getcwd();
        } else {
            throw new \RuntimeException('Unable to guess the Kernel directory.');
        }

        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        return $dir;
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @return string The Kernel class name
     */
    protected function getKernelClass()
    {
        $dir = isset($_SERVER['KERNEL_DIR']) ? $_SERVER['KERNEL_DIR'] : $this->getPhpUnitXmlDir();

        $finder = new Finder();
        $finder->name('*Kernel.php')->in($dir);
        if (!count($finder)) {
            throw new \RuntimeException('You must override the WebTestCase::createKernel() method.');
        }

        $file = current(iterator_to_array($finder));
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }

    /**
     * Creates a Kernel.
     *
     * Available options:
     *
     *  * environment
     *  * debug
     *
     * @param array $options An array of options
     *
     * @return HttpKernelInterface A HttpKernelInterface instance
     */
    protected function createKernel(array $options = array())
    {
        $class = $this->getKernelClass();

        return new $class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }
}
