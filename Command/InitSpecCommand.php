<?php

/**
 * 
 */
 
namespace Bundle\SpecBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;

/**
 * Initializes a new Spec
 * 
 * @author Lukas Botsch <lukas.botsch@gmail.com>
 */
class InitSpecCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('bundleName', InputArgument::REQUIRED,
                    'The bundle namespace to create a spec for'),
                new InputArgument('name', InputArgument::REQUIRED, 'The name of the spec (CamelCase)'),
            ))
            ->setHelp(<<<EOT
The <info>init:spec</info> command generates a new spec with a basic skeleton.

<info>./app/console init:spec HelloBundle greeting</info>
EOT
            )
            ->setName('spec:create')
            ->setDescription('Creates a Spec')
        ;
    }
    
        /**
         * @see Command
         *
         * @throws \InvalidArgumentException When bundle doesn't exist
         * @throws \RuntimeException When Spec can't be created
         */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundleName = $input->getArgument('bundleName');
        
        $found = false;
        foreach ($this->container->get('kernel')->getBundles() as $bundle) {
        	if ($bundle->getNamespace() == $bundleName) {
        		$found = true;
                $specName = $input->getArgument('name');
                $filesystem = $this->container->get('filesystem');
                
                // Copy spec skeleton file to the specs directory
                // (the specs directory is created automatically)
                $path = $bundle->getPath().'/Tests/Spec/';
                
                if (file_exists($path.$specName."Spec.php")) {
                	$output->writeln(sprintf("<error>Spec already exists!</error> (%s)", $path.$specName."Spec.php"));
                    return;
                }
                if (file_exists($path.'Actions/'.$specName.'ActionCollection.php')) {
                    $output->writeln(sprintf("<error>ActionCollection already exists!</error> (%s)", $path.'Actions/'.$specName."ActionCollection.php"));
                    return;
                }
                $filesystem->copy(__DIR__.'/../Resources/skeleton/Spec.php.sk', $path.$specName.'Spec.php');
                $filesystem->copy(__DIR__.'/../Resources/skeleton/ActionCollection.php.sk', $path.'Actions/'.$specName.'ActionCollection.php');
                
                $output->writeln(sprintf('Generating Spec <info>%s</info> in bundle %s', $specName, $bundleName));
                $output->writeln(sprintf('   <info>+File</info> %sActions/%sActionCollection.php', $path, $specName));
                $output->writeln(sprintf('   <info>+File</info> %s%sSpec.php', $path, $specName));
                $args = array(
                    'Namespace' => $bundle->getNamespace().'\Tests\Spec',
                    'Name' => $specName,
                    'name' => strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $specName)),
                );
                Mustache::renderFile($path.$specName.'Spec.php', $args);
                Mustache::renderFile($path.'Actions/'.$specName.'ActionCollection.php', $args);
            }
        }
        
        if (!$found) {
            throw new \InvalidArgumentException(sprintf('The bundle <info>%s</info> does not exist.', $bundleName));
        }
    }
}
