<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArtistRequestRepository;

class AdminHomeController extends AbstractController
{
    #[Route('/admin', name: 'admin_home')]
    public function index(ArtistRequestRepository $artistRequestRepository)
    {
        $artistRequest = $artistRequestRepository->findBy(['status' => 'PENDING']);

        return $this->render('admin_response/currentList.html.twig', [
            'artistRequest' => $artistRequest,
        ]);
    }
}
