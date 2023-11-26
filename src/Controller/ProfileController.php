<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Form\UserType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/profil', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/profil/modif', name: 'app_profile_modif')]
    public function modif(EntityManagerInterface $entityManager,#[CurrentUser] User $user,Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $cvFile */
            $cvFile = $form->get('cv')->getData();
            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();

                try {
                    $cvFile->move(
                        $this->getParameter('PDF_files'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $user->setCv($newFilename);
            }

            /** @var UploadedFile $motiFile */
            $motiFile = $form->get('lettreMotiv')->getData();
            if ($motiFile) {
                $originalFilename = pathinfo($motiFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$motiFile->guessExtension();

                try {
                    $motiFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $user->setLettreMotiv($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profil/modif.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/newUser', name: 'app_profile_new')]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $cvFile */
            $cvFile = $form->get('cv')->getData();
            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();

                try {
                    $cvFile->move(
                        $this->getParameter('PDF_files'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $user->setCv($newFilename);
            }

            /** @var UploadedFile $motiFile */
            $motiFile = $form->get('lettreMotiv')->getData();
            if ($motiFile) {
                $originalFilename = pathinfo($motiFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$motiFile->guessExtension();

                try {
                    $motiFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $user->setLettreMotiv($newFilename);
            }

            try{
                UserFactory::createOne(['lastName'=>$user->getLastName(),'firstName'=>$user->getFirstName(),'email'=>$user->getEmail(),'dateNais'=>$user->getDateNais(),'phone'=>$user->getPhone(),'cv'=>$user->getCv(),'lettreMotiv'=>$user->getLettreMotiv()]);
            } catch (UniqueConstraintViolationException) {
                return $this->redirectToRoute('app_profile_new');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil/new.html.twig', [
            'form' => $form,
        ]);
    }
}
