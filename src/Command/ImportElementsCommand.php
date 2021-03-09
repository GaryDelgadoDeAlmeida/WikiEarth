<?php

namespace App\Command;

use App\Entity\Country;
use App\Entity\Element;
use App\Manager\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImportElementsCommand extends Command
{
    protected static $defaultName = 'app:import:elements';
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
            ->setDescription('Import elements from a csv file to the database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        set_time_limit(0);
        ini_set("memory_limit", "-1");
        $io = new SymfonyStyle($input, $output);
        // $country = $this->manager->getRepository(Country::class);
        $wikiearthElementsDir = $this->params->get("project_natural_elements_atomes_dir");
        $elementsFilePath = $this->params->get("project_import_dir") . "natural-elements/atomes/atomes.csv";
        $elementsImgDir = scandir($this->params->get("project_import_dir") . "natural-elements/atomes/image/");
        $elementsFileContaint = file_get_contents($elementsFilePath);
        $elementsFileData = $this->fileManager->explodeFileToArray($elementsFileContaint);
        $elementsFileColownName = array_flip(array_shift($elementsFileData));
        $current_date = new \DateTime();
        $imgFileName = null;
        $nbrInsertedElements = 0;

        dd($elementsFileData);

        if(!file_exists($wikiearthElementsDir) || !is_dir($wikiearthElementsDir)) {
            mkdir($wikiearthElementsDir, 0777, true);
        }

        foreach($elementsFileData as $key => $oneElementData) {
            $foundedElement = $this->manager->getRepository(Element::class)->findOneBy(["name" => $oneElementData[1]]);

            if(!empty($foundedElement)) {
                continue;
            }

            $element = new Element();
            $element->setName($oneElementData[$elementsFileColownName["name"]]);
            $element->setScientificName($oneElementData[$elementsFileColownName["scientificName"]]);
            $element->setRadioisotope($oneElementData[$elementsFileColownName["radioisotope"]]);
            $element->setAtomicNumber(intval($oneElementData[$elementsFileColownName["atomicNumber"]]));
            $element->setSymbole($oneElementData[$elementsFileColownName["symbole"]]);
            $element->setAtomeGroup($oneElementData[$elementsFileColownName["group"]]);
            $element->setAtomePeriod($oneElementData[$elementsFileColownName["periode"]]);
            $element->setAtomeBlock($oneElementData[$elementsFileColownName["block"]]);
            $element->setVolumicMass(explode("\n", $oneElementData[$elementsFileColownName["volumicMass"]]));
            $element->setNumCAS($oneElementData[$elementsFileColownName["numCAS"]]);
            $element->setNumCE($oneElementData[$elementsFileColownName["numCE"]]);
            $element->setAtomicMass($oneElementData[$elementsFileColownName["atomicMass"]]);
            $element->setAtomicRadius($oneElementData[$elementsFileColownName["atomicRadius"]]);
            $element->setCovalentRadius($oneElementData[$elementsFileColownName["covalentRadius"]]);
            $element->setVanDerWaalsRadius($oneElementData[$elementsFileColownName["vanDerWaalsRadius"]]);
            $element->setElectroniqueConfiguration($oneElementData[$elementsFileColownName["electroniqueConfiguration"]]);
            $element->setOxidationState($oneElementData[$elementsFileColownName["oxidationState"]]);
            $element->setElectronegativity($oneElementData[$elementsFileColownName["electronegativity"]]);
            $element->setFusionPoint($oneElementData[$elementsFileColownName["fusionPoint"]]);
            $element->setBoilingPoint($oneElementData[$elementsFileColownName["boilingPoint"]]);
            // $element->setRadioactivity($oneElementData[$elementsFileColownName["radioactivity"]]);
            $element->setCreatedAt($current_date);

            $this->manager->persist($element);

            if($key % 200 == 0) {
                $this->manager->flush();
                $this->manager->clear();
            }

            $nbrInsertedElements++;
        }

        $this->manager->flush();
        $this->manager->clear();

        $io->success("All the insertion terminated ! {$nbrInsertedElements} / " . count($elementsFileData) ." elements inserted.");

        return 0;
    }
}
