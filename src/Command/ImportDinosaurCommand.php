<?php

namespace App\Command;

use App\Entity\LivingThing;
use App\Manager\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImportDinosaurCommand extends Command
{
    protected static $defaultName = 'app:import:dinosaur';
    private $fileManager;
    private $params;

    public function __construct(EntityManagerInterface $manager, ParameterBagInterface $params, FileManager $fileManager)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->fileManager = $fileManager;
        $this->params = $params;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import all dinosaur from a csv file to the database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $wikiearthDinosaurDir = $this->params->get("project_living_thing_dinosaur_dir");
        $dinosaurFilePath = $this->params->get("project_public_dir") . "content/file/imports/living-thing/dinosauria/dinosauria.csv";
        $dinosaurImgDir = scandir($this->params->get("project_public_dir") . "content/file/imports/living-thing/dinosauria/image/");
        $dinosaurFileContaint = file_get_contents($dinosaurFilePath);
        $dinosaurFileData = $this->fileManager->explodeFileToArray($dinosaurFileContaint);
        $dinosaurFileColownName = array_shift($dinosaurFileData);
        $imgFileName = null;
        $nbrInsertedDino = 0;

        if(!file_exists($wikiearthDinosaurDir) || !is_dir($wikiearthDinosaurDir)) {
            mkdir($wikiearthDinosaurDir, 0777, true);
        }

        foreach($dinosaurFileData as $key => $oneDinosaur) {
            if(empty($this->manager->getRepository(LivingThing::class)->getLivingThingByName($oneDinosaur[0]))) {
                foreach($dinosaurImgDir as $oneDinosaurImg) {
                    if(!in_array($oneDinosaurImg, [".", ".."])) {
                        if(strpos($oneDinosaurImg, $oneDinosaur[0]) !== false) {
                            $imgFileName = $oneDinosaurImg;
                            copy(
                                $this->params->get("project_import_dir") . "living-thing/dinosauria/image/{$oneDinosaurImg}",
                                $wikiearthDinosaurDir . $oneDinosaurImg
                            );
                            break;
                        }
                    }
                }
    
                $livingThing = new LivingThing();
                $livingThing->setName($oneDinosaur[0]);
                $livingThing->setCommonName($oneDinosaur[1]);
                $livingThing->setDomain($oneDinosaur[2]);
                $livingThing->setKingdom($oneDinosaur[3]);
                $livingThing->setSubKingdom($oneDinosaur[4]);
                $livingThing->setInfraKingdom($oneDinosaur[5]);
                $livingThing->setSuperBranch($oneDinosaur[6]);
                $livingThing->setBranch($oneDinosaur[7]);
                $livingThing->setSubBranch($oneDinosaur[8]);
                $livingThing->setInfraBranch($oneDinosaur[9]);
                $livingThing->setDivision($oneDinosaur[10]);
                $livingThing->setSuperClass($oneDinosaur[11]);
                $livingThing->setClass($oneDinosaur[12]);
                $livingThing->setSubClass($oneDinosaur[13]);
                $livingThing->setInfraClass($oneDinosaur[14]);
                $livingThing->setSuperOrder($oneDinosaur[15]);
                $livingThing->setNormalOrder($oneDinosaur[16]);
                $livingThing->setSubOrder($oneDinosaur[17]);
                $livingThing->setInfraOrder($oneDinosaur[18]);
                $livingThing->setMicroOrder($oneDinosaur[19]);
                $livingThing->setSuperFamily($oneDinosaur[20]);
                $livingThing->setFamily($oneDinosaur[21]);
                $livingThing->setSubFamily($oneDinosaur[22]);
                $livingThing->setGenus($oneDinosaur[25]);
                $livingThing->setSubGenus($oneDinosaur[26]);
                $livingThing->setSpecies($oneDinosaur[27]);
                $livingThing->setSubSpecies($oneDinosaur[28]);
                
                if(!empty($imgFileName)) {
                    $livingThing->setImgPath("content/wikiearth/living-thing/dinosaurs/{$imgFileName}");
                    $imgFileName = null;
                }
    
                $this->manager->persist($livingThing);
                $nbrInsertedDino++;
    
                if($key % 200 == 0) { 
                    $this->manager->flush();
                    $this->manager->clear();
                }
            }
        }

        $this->manager->flush();
        $this->manager->clear();

        $io->success("All the insertion terminated ! {$nbrInsertedDino} / " . count($dinosaurFileData) ." dinosaurs inserted.");
        return 0;
    }
}
