<?php

namespace App\Controller;

use App\Form\AlbumFormType;
use App\Repository\AlbumRepository;
use App\Repository\MusicRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AlbumController extends AbstractController
{
    #[Route('/artist/album/create', name: 'album_create')]
    public function albumCreate(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, FileUploader $file_uploader)
    {
        $form = $this->createForm(AlbumFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['img']->getData();
            $file_name = $file_uploader->upload($file);

            $album = $form->getData();
            $album
                ->SetArtist($this->getUSer()->getArtist())
                ->SetSlug(strtolower($slugger->slug($album->getName())))
                ->SetImg('/img/artistImg/' . $file_name);

            $em->persist($album);
            $em->flush();

            $this->addFlash("success", "L'album à été crée avec succès !");
            return $this->redirectToRoute('artist_albumList');

        }

        $formview = $form->createView();

        return $this->render('album/create.html.twig', [
            'formview' => $formview,
        ]);
    }

    #[Route('/artist/album/list', name: 'artist_albumList')]
    public function albumList() {

        $albums = $this->getUser()->getArtist()->getAlbums();
        return $this->render('album/list.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/artist/album/add/{id}', name: 'add_to_album')]
    public function addToAlbum($id, AlbumRepository $albumRepository, MusicRepository $musicRepository)
    {
        $album = $albumRepository->find($id);
        $artist = $this->getUser()->getArtist();

        if (!$album) {
            $this->addFlash("danger", "L'album n'a pas été trouvé'");
            return $this->redirectToRoute('artist_albumList');
        }

        if ($artist !== $album->getArtist()) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $musics = $musicRepository->findBy(['artist' => $artist]);

        return $this->render('album/addToAlbum.html.twig', [
            'album' => $album,
            'musics' => $musics
        ]);
    }

    #[Route('/artist/album/Addscript/{idAlbum}/{idMusic}', name: 'adding_script')]
    public function addingScript($idAlbum, $idMusic, AlbumRepository $albumRepository, MusicRepository $musicRepository, EntityManagerInterface $em)
    {
        $album = $albumRepository->find($idAlbum);
        $music = $musicRepository->find($idMusic);
        $artist = $this->getUser()->getArtist();

        if (!$album) {
            $this->addFlash("danger", "L'album n'a pas été trouvé'");
            return $this->redirectToRoute('artist_albumList');
        }

        if (!$music) {
            $this->addFlash("danger", "La musique n'a pas été trouvé'");
            return $this->redirectToRoute('artist_albumList');
        }

        if ($artist !== $album->getArtist()) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $music->setAlbum($album);
        $em->flush();

        $this->addFlash("success", "Musique bien ajoutée à l'album !");
        return $this->redirectToRoute('add_to_album', ['id' => $idAlbum]);
    }

    #[Route('/artist/album/delete/{id}', name: 'delete_to_album')]
    public function deleteToAlbum($id, AlbumRepository $albumRepository, MusicRepository $musicRepository)
    {

        $album = $albumRepository->find($id);
        $artist = $this->getUser()->getArtist();

        if (!$album) {
            $this->addFlash("danger", "L'album n'a pas été trouvé'");
            return $this->redirectToRoute('artist_albumList');
        }

        if ($artist !== $album->getArtist()) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $musics = $musicRepository->findBy(['artist' => $artist]);

        return $this->render('album/deleteToAlbum.html.twig', [
            'album' => $album,
            'musics' => $musics
        ]);
    }

    #[Route('/artist/album/deleteScript/{idAlbum}/{idMusic}', name: 'deleting_script')]
    public function deletingScript($idAlbum, $idMusic, AlbumRepository $albumRepository, MusicRepository $musicRepository, EntityManagerInterface $em)
    {

        $album = $albumRepository->find($idAlbum);
        $music = $musicRepository->find($idMusic);
        $artist = $this->getUser()->getArtist();

        if (!$album) {
            $this->addFlash("danger", "L'album n'a pas été trouvé'");
            return $this->redirectToRoute('artist_albumList');
        }

        if (!$music) {
            $this->addFlash("danger", "La musique n'a pas été trouvé'");
            return $this->redirectToRoute('artist_albumList');
        }

        if ($artist !== $album->getArtist()) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $music->setAlbum(null);
        $em->flush();

        $this->addFlash("success", "Musique bien supprimé de l'album !");
        return $this->redirectToRoute('delete_to_album', ['id' => $idAlbum]);
    }

    #[Route('/artist/album/edit/{id}', name: 'edit_album')]
    public function edit($id, AlbumRepository $albumRepository, EntityManagerInterface $em, Request $request, SluggerInterface $slugger, FileUploader $file_uploader)
    {
        $album = $albumRepository->find($id);
        $artist = $this->getUser()->getArtist();

        if (!$album) {
            $this->addFlash("danger", "L'album n'a pas été trouvé'");
            return $this->redirectToRoute('artist_albumList');
        }

        if ($artist !== $album->getArtist()) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(AlbumFormType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['img']->getData();
            $file_name = $file_uploader->upload($file);

            $album = $form->getData();
            $album
                ->SetArtist($this->getUSer()->getArtist())
                ->SetSlug(strtolower($slugger->slug($album->getName())))
                ->SetImg('/img/artistImg/' . $file_name);

            $em->persist($album);
            $em->flush();

            $this->addFlash("success", "L'album à été modifié !");
            return $this->redirectToRoute('artist_albumList');

        }

        $formview = $form->createView();

        return $this->render('album/edit.html.twig', [
            'formview' => $formview,
            'album' => $album
        ]);
    }

    #[Route('/artist/album/delete/{id}', name: 'delete_album')]
    public function delete($id, AlbumRepository $albumRepository, EntityManagerInterface $em)
    {
        $album = $albumRepository->find($id);
        $artist = $this->getUser()->getArtist();

        if (!$album) {
            $this->addFlash("danger", "L'album n'a pas été trouvé'");
            return $this->redirectToRoute('artist_albumList');
        }

        if ($artist !== $album->getArtist()) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $em->remove($album);
        $em->flush();

        $this->addFlash("success", "L'album à été supprimé !");
        return $this->redirectToRoute('artist_albumList');
    }
}
