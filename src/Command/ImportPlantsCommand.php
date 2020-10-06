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

class ImportPlantsCommand extends Command
{
    protected static $defaultName = 'app:import:plants';
    private $fileManager;
    private $params;
    private $manager;

    public function __construct(EntityManagerInterface $manager, ParameterBagInterface $params, FileManager $fileManager)
    {
        parent::__construct();
        $this->fileManager = $fileManager;
        $this->manager = $manager;
        $this->params = $params;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import plants from a csv file to the database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $io = new SymfonyStyle($input, $output);
        $wikiearthPlantsDir = $this->params->get("project_living_thing_plants_dir");
        $plantsFilePath = $this->params->get("project_import_dir") . "living-thing/plantae/plantae.csv";
        $plantsImgDir = scandir($this->params->get("project_import_dir") . "living-thing/plantae/image/");
        $plantsFileContaint = file_get_contents($plantsFilePath);
        $plantsFileData = $this->fileManager->explodeFileToArray($plantsFileContaint);
        $plantsFileColownName = array_shift($plantsFileData);
        $imgFileName = null;
        $nbrInsertedPlants = 0;

        if(!file_exists($wikiearthPlantsDir) || !is_dir($wikiearthPlantsDir)) {
            mkdir($wikiearthPlantsDir, 0777, true);
        }

        dd($plantsFileColownName);

        foreach($plantsFileData as $key => $onePlant) {
            if(empty($this->manager->getRepository(LivingThing::class)->getLivingThingByName($onePlant[3]))) {
                foreach($plantsImgDir as $onePlantImg) {
                    if(!in_array($onePlantImg, [".", ".."])) {
                        if(strpos($onePlantImg, $onePlant[0]) !== false) {
                            copy(
                                $this->params->get("project_import_dir") . "living-thing/plantea/image/{$onePlantImg}",
                                $wikiearthPlantDir . $onePlantImg
                            );
                            $imgFileName = $onePlantImg;
                            break;
                        }
                    }
                }

                $plant = new LivingThing();
                $plant->setName($oneDinosaur[3]);
                $plant->setCommonName(!empty($oneDinosaur[4]) ? $oneDinosaur[4] : $oneDinosaur[3]);
                // $plant->setDomain($oneDinosaur[2]);
                $plant->setKingdom($oneDinosaur[17]);
                $plant->setSubKingdom($oneDinosaur[16]);
                // $plant->setInfraKingdom($oneDinosaur[5]);
                // $plant->setSuperBranch($oneDinosaur[6]);
                // $plant->setBranch($oneDinosaur[7]);
                // $plant->setSubBranch($oneDinosaur[8]);
                // $plant->setInfraBranch($oneDinosaur[9]);
                // $plant->setSuperDivision($oneDinosaur[14]);
                $plant->setDivision($oneDinosaur[14]);
                // $plant->setSubDivision($oneDinosaur[13]);
                // $plant->setSuperClass($oneDinosaur[13]);
                $plant->setClass($oneDinosaur[12]);
                $plant->setSubClass($oneDinosaur[11]);
                // $plant->setInfraClass($oneDinosaur[14]);
                // $plant->setSuperOrder($oneDinosaur[15]);
                $plant->setNormalOrder($oneDinosaur[10]);
                // $plant->setSubOrder($oneDinosaur[17]);
                // $plant->setInfraOrder($oneDinosaur[18]);
                // $plant->setMicroOrder($oneDinosaur[19]);
                // $plant->setSuperFamily($oneDinosaur[20]);
                $plant->setFamily($oneDinosaur[7]);
                // $plant->setSubFamily($oneDinosaur[22]);
                $plant->setGenus($oneDinosaur[6]);
                // $plant->setSubGenus($oneDinosaur[26]);
                // $plant->setSpecies($oneDinosaur[27]);
                // $plant->setSubSpecies($oneDinosaur[28]);

                if(!empty($imgFileName)) {
                    $plant->setImgPath("content/wikiearth/living-thing/dinosaurs/{$imgFileName}");
                    $imgFileName = null;
                }

                $this->manager->persist($plant);
                $nbrInsertedPlants++;

                if($key % 200 == 0) {
                    $this->manager->flush();
                    $this->manager->clear();
                }
            }
        }
        
        $this->manager->flush();
        $this->manager->clear();

        $io->success("All the insertion terminated ! {$nbrInsertedPlants} / " . count($plantsFileData) ." plants inserted.");

        return 0;
    }
}
