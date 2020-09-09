<?php

namespace App\Command;

use App\Entity\LivingThing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportAnimalsCommand extends Command
{
    protected static $defaultName = 'app:import:animals';
    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        parent::__construct();
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import animals')
            ->addArgument('filePath', InputArgument::REQUIRED, 'The full path of the file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('filePath');
        $fileContent = file_get_contents($filePath);
        $tabRow = \explode("\r\n", $fileContent);
        $tabColownName = $tabCell = $tabLiv = [];
        $limitBeforeInsert = 50;
        
        foreach($tabRow as $key => $value) {
            array_push($tabCell, explode("\t", trim(utf8_encode($value))));
        }

        $tabColownName = $tabCell[0];
        unset($tabCell[0]);
        array_pop($tabCell);

        $keyScientificName = array_search("\"ScientificName\"", $tabColownName, true);
        $keyKingdom = array_search("\"Kingdom\"", $tabColownName, true);
        $keyPhylum = array_search("\"Phylum\"", $tabColownName, true);
        $keyClass = array_search("\"Class\"", $tabColownName, true);
        $keyOrder = array_search("\"Order\"", $tabColownName, true);
        $keyFamily = array_search("\"Family\"", $tabColownName, true);
        $keyGenus = array_search("\"Genus\"", $tabColownName, true);
        $keySubgenus = array_search("\"Subgenus\"", $tabColownName, true);
        $keySpecies = array_search("\"Species\"", $tabColownName, true);
        $keySubspecies = array_search("\"Subspecies\"", $tabColownName, true);

        if(
            !empty($keyScientificName) &&
            !empty($keyKingdom) &&
            !empty($keyPhylum) &&
            !empty($keyClass) &&
            !empty($keyOrder) &&
            !empty($keyFamily) &&
            !empty($keyGenus) &&
            !empty($keySubgenus) &&
            !empty($keySpecies) &&
            !empty($keySubspecies)
        ) {
            foreach($tabCell as $key => $oneRow) {
                $name = str_replace("\"", "", $oneRow[$keyScientificName]);
                $livingThing = new LivingThing();
                $livingThing->setCommonName($name);
                $livingThing->setName($name);
                $livingThing->setKingdom(str_replace("\"", "", $oneRow[$keyKingdom]));
                $livingThing->setBranch(str_replace("\"", "", $oneRow[$keyPhylum]));
                $livingThing->setClass(str_replace("\"", "", $oneRow[$keyClass]));
                $livingThing->setNormalOrder(str_replace("\"", "", $oneRow[$keyOrder]));
                $livingThing->setFamily(str_replace("\"", "", $oneRow[$keyFamily]));
                $livingThing->setGenus(str_replace("\"", "", $oneRow[$keyGenus]));
                $livingThing->setSubGenus(str_replace("\"", "", $oneRow[$keySubgenus]));
                $livingThing->setSpecies(str_replace("\"", "", $oneRow[$keySpecies]));
                $livingThing->setSubSpecies(str_replace("\"", "", $oneRow[$keySubspecies]));
                $this->manager->persist($livingThing);
    
                if($key % $limitBeforeInsert === 0) {
                    $this->manager->flush();
                    $this->manager->clear();
                }
            }
            $this->manager->flush();
            $this->manager->clear();
    
            $io->success('The living thing of this file was successfully inserted into the database.');
        } else {
            $io->error('An error occurred.');
        }
        return 0;
    }
}
