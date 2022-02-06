<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAlbumController extends AbstractController
{
    #[Route('/admin/album/list', name: 'admin_album_list')]
    public function index(AlbumRepository $albumRepository)
    {
        $albums = $albumRepository->findAll();

        return $this->render('admin_album/list.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/admin/album/read/{id}', name: 'admin_album_read')]
    public function read($id, AlbumRepository $albumRepository)
    {

        $album = $albumRepository->find($id);

        if (!$album) {
            $this->addFlash("danger", "L'album n'a pas été trouvée");
            return $this->redirectToRoute('admin_album_list');
        }

        return $this->render('admin_artist/read.html.twig', [
            'album' => $album,
        ]);
    }

}
