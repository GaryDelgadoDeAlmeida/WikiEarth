<?php

namespace App\Command;

use App\Entity\Country;
use App\Entity\Mineral;
use App\Manager\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImportMineralsCommand extends Command
{
    protected static $defaultName = 'app:import:minerals';
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
            ->setDescription('Import minerals from a csv file to the database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $io = new SymfonyStyle($input, $output);
        // $country = $this->manager->getRepository(Country::class);
        $wikiearthMineralsDir = $this->params->get("project_natural_elements_minerals_dir");
        $mineralsFilePath = $this->params->get("project_import_dir") . "natural-elements/minerals/minerals.csv";
        $mineralsImgDir = scandir($this->params->get("project_import_dir") . "natural-elements/minerals/image/");
        $mineralsFileContaint = file_get_contents($mineralsFilePath);
        $mineralsFileData = $this->fileManager->explodeFileToArray($mineralsFileContaint);
        $mineralsFileColownName = array_shift($mineralsFileData);
        $imgFileName = null;
        $nbrInsertedMinerals = 0;

        if(!file_exists($wikiearthMineralsDir) || !is_dir($wikiearthMineralsDir)) {
            mkdir($wikiearthMineralsDir, 0777, true);
        }

        foreach($mineralsFileData as $key => $oneMineralData) {
            $foundedMineral = $this->manager->getRepository(Mineral::class)->findOneBy(["name" => $oneMineralData[1]]);
            
            if(empty($foundedMineral)) {
                $mineral = new Mineral();
                $mineral->setName($oneMineralData[1]);
                $mineral->setRruffChemistry($oneMineralData[2]);
                $mineral->setImaChemistry($oneMineralData[3]);
                $mineral->setChemistryElements($oneMineralData[4]);
                $mineral->setImaNumber($oneMineralData[5]);
                $mineral->setImaStatus($oneMineralData[8]);
                $mineral->setStructuralGroupname($oneMineralData[9]);
                $mineral->setCrystalSystem($oneMineralData[10]);
                $mineral->setValenceElements($oneMineralData[11]);

                if(!empty($country->findOneBy(["alpha3_code" => $oneMineralData[7]]))) {
                    $mineral->addCountry($oneMineralData[7]);
                }

                if($oneMineralData[12] == 1) {
                    $mineral->setImgPath($oneMineralData[12]);
                }

                $this->manager->persist($mineral);
                $nbrInsertedMinerals++;

                if($key % 200 == 0) {
                    $this->manager->flush();
                    $this->manager->clear();
                }
            }
        }

        $this->manager->flush();
        $this->manager->clear();

        $io->success("All the insertion terminated ! {$nbrInsertedMinerals} / " . count($mineralsFileData) ." minerals inserted.");

        return 0;
    }
}
