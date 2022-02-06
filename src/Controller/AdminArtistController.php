<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArtistController extends AbstractController
{
    #[Route('/admin/artist/list', name: 'admin_artist_list')]
    public function list(ArtistRepository $artistRepository)
    {
        $artists = $artistRepository->findAll();

        return $this->render('admin_artist/list.html.twig', [
            'artists' => $artists,
        ]);
    }

    #[Route('/admin/artist/read/{id}', name: 'admin_artist_read')]
    public function read($id, ArtistRepository $artistRepository)
    {

        $artist = $artistRepository->find($id);

        if (!$artist) {
            $this->addFlash("danger", "L'artiste n'a pas été trouvée");
            return $this->redirectToRoute('admin_artist_list');
        }

        return $this->render('admin_artist/read.html.twig', [
            'artist' => $artist,
        ]);
    }
}
