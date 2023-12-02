<?php

namespace App\Controller\Anonymous;

use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CountryController extends AbstractController
{
    private CountryRepository $countryRepository;
    
    function __construct(CountryRepository $countryRepository) {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @Route("/{country}/articles", name="countryArticle")
     */
    public function country_article(Request $request, string $country) : Response
    {
        $limit = 10;
        $offset = !empty($request->get('offset')) && preg_match('/^[0-9]*$/', $request->get('offset')) ? $request->get('offset') : 1;
        $oneCountry = $this->countryRepository->getCountryByName($country);
        $articles = [];

        foreach($oneCountry->getLivingThing()->getValues() as $oneLivingThing) {
            if(!empty($oneLivingThing->getArticleLivingThing())) {
                $articles[] = $oneLivingThing->getArticleLivingThing();
            }
        }

        return $this->render('anonymous/article/country.html.twig', [
            "country" => $country,
            "articles" => $articles,
            "offset" => $offset,
            "nbrOffset" => $offset
        ]);
    }
}
