<?php

namespace App\Controller;

use App\Entity\Music;
use App\Form\PlaylistFormType;
use App\Repository\MusicRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class PlaylistController extends AbstractController
{
    #[Route('/profile/playlist/create', name: 'create_playlist')]
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $form = $this->createForm(PlaylistFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlist = $form->getData();
            $playlist->setSlug(strtolower($slugger->slug($playlist->getName())));
            $playlist->setUser($this->getUser());

            $em->persist($playlist);
            $em->flush();

            $this->addFlash("success", "La playlist à bien été ajoutée !");

            return $this->redirectToRoute('list_playlist');

        }

        $formview = $form->createView();

        return $this->render('playlist/create.html.twig', [
            'formview' => $formview,
        ]);
    }

    #[Route('/profile/playlist/list', name: 'list_playlist')]
    public function list()
    {
        $playlists = $this->getUser()->getPlaylist();

        return $this->render('playlist/list.html.twig', [
            'playlists' => $playlists
        ]);
    }

    #[Route('/profile/playlist/{id}', name: 'read_playlist')]
    public function read($id, PlaylistRepository $playlistRepository)
    {
        $playlist = $playlistRepository->find($id);
        $user = $this->getUser();

        if (!$playlist) {
            $this->addFlash("danger", "La playlist n'a pas été trouvé'");
            return $this->redirectToRoute('list_playlist');
        }

        if ($playlist->getUser() !== $user) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        return $this->render('playlist/read.html.twig', [
            'playlist' => $playlist
        ]);

    }

    #[Route('/profile/playlist/delete/{id}', name: 'delete_playlist')]
    public function delete($id, PlaylistRepository $playlistRepository, EntityManagerInterface $em)
    {
        $playlist = $playlistRepository->find($id);
        $user = $this->getUser();

        if (!$playlist) {
            $this->addFlash("danger", "La playlist n'a pas été trouvé'");
            return $this->redirectToRoute('list_playlist');
        }

        if ($playlist->getUser() !== $user) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $em->remove($playlist);
        $em->flush();

        $this->addFlash("success", "La playlist a bien été supprimée'");
        return $this->redirectToRoute('list_playlist');

    }

    #[Route('/profile/playlist/add/{idMusic}/{idPlaylist}/{route}', name: 'add_to_playlist')]
    public function addToPlaylist($idMusic, $idPlaylist, $route, PlaylistRepository $playlistRepository, MusicRepository $musicRepository, EntityManagerInterface $em)
    {
        $music = $musicRepository->find($idMusic);
        $playlist = $playlistRepository->find($idPlaylist);
        $user = $this->getUser();

        if (!$music) {
            $this->addFlash("danger", "La musique n'a pas été trouvé'");
            return $this->redirectToRoute('home');
        }

        if (!$playlist) {
            $this->addFlash("danger", "La playlist n'a pas été trouvé'");
            return $this->redirectToRoute('list_playlist');
        }

        if (!$route) {
            $this->addFlash("danger", "Une erreur s'est produite");
            return $this->redirectToRoute('home');
        }

        if ($playlist->getUser() !== $user) {
            $this->addFlash("danger", "Une erreur s'est produite");
            return $this->redirectToRoute('home');
        }

        $playlist->addMusic($music);
        $em->persist($playlist);
        $em->flush();

        $url = '/' . $route;
        $this->addFlash("success", "Musique ajoutée à la playlist");
        return $this->redirect($url);

    }

    #[Route('/profile/playlist/delete/{idMusic}/{idPlaylist}', name: 'delete_from_playlist')]
    public function deleteFromPlaylist($idMusic, $idPlaylist, MusicRepository $musicRepository, PlaylistRepository $playlistRepository, EntityManagerInterface $em)
    {
        $music = $musicRepository->find($idMusic);
        $playlist = $playlistRepository->find($idPlaylist);
        $user = $this->getUser();

        if (!$music) {
            $this->addFlash("danger", "La musique n'a pas été trouvé'");
            return $this->redirectToRoute('home');
        }

        if (!$playlist) {
            $this->addFlash("danger", "La playlist n'a pas été trouvé'");
            return $this->redirectToRoute('list_playlist');
        }

        if ($playlist->getUser() !== $user) {
            $this->addFlash("danger", "Une erreur s'est produite");
            return $this->redirectToRoute('home');
        }

        // securité si la musique est bien dans playlist
        $count = 0;
        foreach ($playlist->getMusic() as $check) {
            if ($check == $music) {
                $count++;
            }
        }

        if ($count < 1) {
            $this->addFlash("danger", "Cette musique n'a pas été trouvée dans cette playlist");
            return $this->redirectToRoute('list_playlist');
        }

        $playlist->removeMusic($music);
        $em->flush();

        $this->addFlash("success", "La musique a été supprimée de la playlist");
        return $this->redirectToRoute('list_playlist');
    }
}
