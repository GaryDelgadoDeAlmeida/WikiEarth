<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;

class FileExistExtension extends AbstractExtension
{
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('file_exist', [$this, 'fileExist']),
        ];
    }

    public function fileExist($value, $alternativePath = true)
    {
        if(empty($value) || !file_exists($this->container->getParameter("project_public_dir") . $value)) {
            if($alternativePath) {
                $value = "content/img/thumbnail.jpg";
            } else {
                $value = "";
            }
        }

        return $value;
    }
}
