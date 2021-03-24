<?php

namespace App\Manager;

use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfGeneratorManager extends AbstractController {

    function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }

    public function generatePdf(object $article, string $articleType)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $html = null;
        $articleName = $article->getTitle();
        
        // Retrieve the HTML generated in our twig file
        if($articleType == "living-thing") {
            $html = $this->renderView('pdf/living-thing.html.twig', [
                'livingThing' => $article,
                'references' => []
            ]);
        } elseif($articleType == "natural-element") {
            $html = $this->renderView('pdf/natural-element.html.twig', [
                'naturalElement' => $article
            ]);
        } elseif($articleType == "mineral") {
            $html = $this->renderView('pdf/mineral.html.twig', [
                'mineral' => $article
            ]);
        } else {
            throw $this->createNotFoundException("This type isn't allowed");
        }
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        return $dompdf->stream("{$articleName}.pdf", [
            "Attachment" => false
        ]);
    }
}