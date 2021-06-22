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

    /**
     * @param EntityManagerInterface the manager who will interact with the database
     * @param ParameterBagInterface
     * @param FileManager
     */
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

    /**
     * @param InputInterface data inserted using shell
     * @param OutputInterface data returned to the user
     * @return int return 0 to kill the process
     */
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

        foreach($plantsFileData as $key => $onePlant) {
            
            // If none has been found
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
                $plant->setName($onePlant[3]);
                $plant->setCommonName(!empty($onePlant[4]) ? $onePlant[4] : $onePlant[3]);
                // $plant->setDomain($onePlant[2]);
                $plant->setKingdom($onePlant[17]);
                $plant->setSubKingdom($onePlant[16]);
                // $plant->setInfraKingdom($onePlant[5]);
                // $plant->setSuperBranch($onePlant[6]);
                // $plant->setBranch($onePlant[7]);
                // $plant->setSubBranch($onePlant[8]);
                // $plant->setInfraBranch($onePlant[9]);
                // $plant->setSuperDivision($onePlant[14]);
                $plant->setDivision($onePlant[14]);
                // $plant->setSubDivision($onePlant[13]);
                // $plant->setSuperClass($onePlant[13]);
                $plant->setClass($onePlant[12]);
                $plant->setSubClass($onePlant[11]);
                // $plant->setInfraClass($onePlant[14]);
                // $plant->setSuperOrder($onePlant[15]);
                $plant->setNormalOrder($onePlant[10]);
                // $plant->setSubOrder($onePlant[17]);
                // $plant->setInfraOrder($onePlant[18]);
                // $plant->setMicroOrder($onePlant[19]);
                // $plant->setSuperFamily($onePlant[20]);
                $plant->setFamily($onePlant[7]);
                // $plant->setSubFamily($onePlant[22]);
                $plant->setGenus($onePlant[6]);
                // $plant->setSubGenus($onePlant[26]);
                // $plant->setSpecies($onePlant[27]);
                // $plant->setSubSpecies($onePlant[28]);

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
