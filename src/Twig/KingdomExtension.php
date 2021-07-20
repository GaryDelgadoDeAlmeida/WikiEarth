<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class KingdomExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('kingdom', [$this, 'convertKingdomClassification']),
        ];
    }

    public function convertKingdomClassification($kingdomClassification)
    {
        return ucfirst(strtolower($kingdomClassification));
    }
}
