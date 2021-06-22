<?php

namespace App\Command;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCountryCommand extends Command
{
    protected static $defaultName = 'app:import:country';
    private $url = "https://restcountries.eu/rest/v2/all";
    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        parent::__construct();
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import all existing country of the world to the database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limitBeforeInsertion = 50;
        $curl = \curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0,
        ]);
        $execCurl = curl_exec($curl);
        $countrys = json_decode($execCurl);
        curl_close($curl);

        foreach($countrys as $key => $oneCountry) {
            $country = new Country();
            $country->setName($oneCountry->name);
            $country->setTreatedCountryName(strtolower(str_replace([" "], "-", str_replace([",", "(", ")"], "", $oneCountry->name))));
            $country->setAlpha2Code($oneCountry->alpha2Code);
            $country->setAlpha3Code($oneCountry->alpha3Code);
            $country->setRegion($oneCountry->region);
            $country->setSubRegion($oneCountry->subregion);
            $country->setNativeName($oneCountry->nativeName);
            $this->manager->persist($country);

            if($key % $limitBeforeInsertion === 0) {
                $this->manager->flush();
                $this->manager->clear();
            }
        }
        $this->manager->flush();
        $this->manager->clear();

        $io->success("Les insertions des pays en base de données s'est correctement effectuées");

        return 0;
    }
}
