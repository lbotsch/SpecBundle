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
            ->setName('init:spec')
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
                	$output->writeln(sprintf("<info>Spec already exists!<info> (<comment>%s</comment>)", $path.$specName."Spec.php"));
                } else {
                    $filesystem->copy(__DIR__.'/../Resources/skeleton/Spec.php.sk', $path.$specName.'Spec.php');
                    $filesystem->copy(__DIR__.'/../Resources/skeleton/SpecBase.php.sk', $path.'/Base/'.$specName.'SpecBase.php');
                    
                    $output->writeln(sprintf('<info>Generating Spec <comment>%s</comment> in bundle <comment>%s</comment></info>', $specName, $bundleName));
                    $output->writeln(sprintf('   <info>+File</info> <comment>%sBase/%sSpecBase.php</comment>', $path, $specName));
                    $output->writeln(sprintf('   <info>+File</info> <comment>%s%sSpec.php</comment>', $path, $specName));
                    $args = array(
                        'Namespace' => $bundle->getNamespace().'\Tests\Spec',
                        'Name' => $specName,
                        'name' => strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $specName)),
                    );
                    Mustache::renderFile($path.$specName.'Spec.php', $args);
                    Mustache::renderFile($path.'/Base/'.$specName.'SpecBase.php', $args);
                }
            }
        }
        
        if (!$found) {
            throw new \InvalidArgumentException(sprintf('The bundle <info>%s</info> does not exist.', $bundleName));
        }
    }
}
