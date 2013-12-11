<?php

namespace NS\UtilBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Description of NewArrayChoiceCommand
 *
 * @author gnat
 */
class NewArrayChoiceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('nsutil:new:choice')
            ->setDescription('Create new choice')
            ->addArgument(
                'className',
                InputArgument::REQUIRED,
                'What is the new class named?'
            )
            ->addArgument(
                'targetBundle',
                InputArgument::REQUIRED,
                'Target Bundle?'
            )
        ; 
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $class = $input->getArgument('className');
        $target = $input->getArgument('targetBundle');
        $entityClass = "<?php

namespace NMSPACE\Entity\Types;
use NS\UtilBundle\Entity\Types\ArrayChoice;

class CLASSNAME extends ArrayChoice
{
    protected \$convert_class = 'NMSPACE\Form\Types\CLASSNAME';

    public function getName()
    {
        return 'CLASSNAME';
    }   
}\n\n";
        
$formClass = "<?php

namespace NMSPACE\Form\Types;

use NS\UtilBundle\Form\Types\ArrayChoice;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Description of CLASSNAME
 *
 */
class CLASSNAME extends ArrayChoice
{
    const FIRST_VALUE = 1;

    protected \$values = array(
                                self::FIRST_VALUE => 'First Value',
                             );

    public function getName()
    {
        return 'CLASSNAME_STRTOLOWER';
    }
}\n";
         
        $o1 = str_replace(array('NMSPACE','CLASSNAME'), array($target,$class), $entityClass);
        $o2 = str_replace(array('NMSPACE','CLASSNAME_STRTOLOWER','CLASSNAME'),array($target, strtolower($class),$class),$formClass);
        
        $kernel = $this->getContainer()->get('kernel');
        $path = $kernel->locateResource("@".  str_replace('\\', '', $target));

        if(!is_dir($path."Entity/Types"))
            mkdir($path."Entity/Types");
        
        file_put_contents($path."Entity/Types/$class.php",$o1);
        
        if(!is_dir($path."Form/Types"))
            mkdir($path."Form/Types");
        
        file_put_contents($path."Form/Types/$class.php",$o2);
        
        $output->writeln("Class Created");
        $output->writeln("");
        $output->writeln("Add to services.yml");
        $output->writeln("  ns.sentinel.form.type.gavi:");
        $output->writeln("    class: NS\SentinelBundle\Form\Types\GAVIEligible");
        $output->writeln("    tags:");
        $output->writeln("      - { name: form.type, alias: GAVIEligible }");
        $output->writeln("");
        
        $output->writeln("Add to app/config/config.yml");
        $output->writeln("");
        $output->writeln("doctrine:");
        $output->writeln("  dbal:");
        $output->writeln("    types:");
        $output->writeln("      $class: $target\\Entity\\Types\\$class");
        $output->writeln("    mapping_types:");
        $output->writeln("      $class: $class");
    }
} 