<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ArtistRequestFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArtistRequestController extends AbstractController
{
    #[Route('/profile/art/request', name: 'artist_request')]
    public function index(Request $request, EntityManagerInterface $em)
    {
        if ($this->getUser()->getArtistRequest()) {
            $this->addFlash("danger", "Vous avez déjà envoyé votre candidature. ");
            return $this->redirectToRoute('profile');
        }

        $form = $this->createForm(ArtistRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $artistRequest = $form->getData();
            $artistRequest->SetStatus('PENDING');
            $artistRequest->SetUser($this->getUser());

            $em->persist($artistRequest);
            $em->flush();

            $this->addFlash("success", "Votre demande à bien été envoyée ! Un retour vous sera transmis dans les plus brefs délais.");
            return $this->redirectToRoute('profile');

        }
        $formview = $form->createView();

        return $this->render('artist_request/form.html.twig', [
            'formview' => $formview
        ]);
    }
}
