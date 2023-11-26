<?php

namespace App\Controller;

use App\Repository\OffreRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class OffreController extends AbstractController
{
    #[Route('/offre', name: 'app_offre_index')]
    public function index(OffreRepository $offreRepository, Request $request, TypeRepository $typeRepository): Response
    {
        $textRechercher = $request->query->get('textRecherche', '');
        $typeRechercher = $request->query->get('type', 0);

        $Offres = $offreRepository->findByTypeAndText($typeRechercher, $textRechercher);

        $types = $typeRepository->findAll();

        return $this->render('offre/index.html.twig', [
            'Offres'=>$Offres,
            'textRecherche' => $textRechercher,
            'types' => $types,
        ]);
    }

    #[Route('/entreprise/offre', name: 'app_offre_OffreEntreprise', requirements: ['entrepriseId' => '\d+'])]
    public function OffreEntreprise(OffreRepository $offreRepository, Request $request, TypeRepository $typeRepository): Response
    {
        $textRechercher = $request->query->get('textRecherche', '');
        $typeRechercher = $request->query->get('type', 0);
        $entrepriseId = $request->query->get('entrepriseId');

        $Offres = $offreRepository->findByTypeAndTextByEntrepriseId($entrepriseId,$typeRechercher, $textRechercher);
        $types = $typeRepository->findAll();


        return $this->render('entreprise/offre/index.html.twig', [
            'types' => $types,
            'textRecherche' => $textRechercher,
            'Offres'=>$Offres,
            'entrepriseId' => $entrepriseId
        ]);
    }

    #[Route('/offre/{offreId}', name: 'app_offre_show', requirements: ['offreId' => '\d+'])]
    public function show(OffreRepository $offreRepository, int $offreId){
        $Offre = $offreRepository->find($offreId);

        return $this->render('offre/show.html.twig',[
            'Offre'=>$Offre
        ]);
    }
}
