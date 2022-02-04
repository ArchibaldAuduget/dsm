<?php

namespace App\Controller;

use App\Entity\Music;
use App\Entity\Artist;
use App\Entity\Category;
use App\Form\AlbumFormType;
use App\Form\ImgUploadType;
use App\Form\MusicFormType;
use App\Service\FileUploader;
use App\Form\MusicEditFormType;
use App\Form\AddToAlbumFormType;
use App\Repository\AlbumRepository;
use App\Repository\MusicRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArtistController extends AbstractController
{
    #[Route('/artist/profile', name: 'artist_profile')]
    public function profile(Request $request, FileUploader $file_uploader, EntityManagerInterface $em)
    {
        $artist = $this->getUser()->getArtist();

        $form = $this->createForm(ImgUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['img']->getData();

            if ($file) {
                $file_name = $file_uploader->upload($file);

                if (null !== $file_name) {

                    $this->getUser()->getArtist()->SetImg('/img/artistImg/' . $file_name);
                    $em->flush();
                } else {
                    $this->addFlash("danger", "Une erreur s'est produite, la photo n'a pas pu être mise à jour");
                    return $this->redirectToRoute('artist_profile');
                }

            }
        }


        return $this->render('artist/profile.html.twig', [
            'artist' => $artist,
            'formview' => $form->createView()
        ]);
    }

    #[Route('/artist/music/add', name: 'add_music')]
    public function addMusic(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, FileUploader $file_uploader)
    {
        $form = $this->createForm(MusicFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['url']->getData();
            $file_name = $file_uploader->upload($file);

            $music = $form->getData();
            $music
                ->SetViews(0)
                ->setWeeklyViews(0)
                ->SetArtist($this->getUSer()->getArtist())
                ->SetSlug(strtolower($slugger->slug($music->getName())))
                ->SetUrl('/musics/' . $file_name);

            $em->persist($music);
            $em->flush();

            $this->addFlash("success", "La musique a bien été uploadée !");
            return $this->redirectToRoute('artist_musicList');

        }

        $formview = $form->createView();

        return $this->render('artist/musicForm.html.twig', [
            'formview' => $formview,
        ]);
    }

    #[Route('/artist/music/list', name: 'artist_musicList')]
    public function musicList()
    {
        $musics = $this->getUser()->getArtist()->getMusic();
        return $this->render('artist/musicList.html.twig', [
            'musics' => $musics,
        ]);

    }

    #[Route('/artist/music/delete/{id}', name: 'delete_music')]
    public function delete($id, MusicRepository $musicRepository, EntityManagerInterface $em)
    {
        $music = $musicRepository->find($id);

        if (!$music) {
            $this->addFlash("danger", "La musique en question n'a pas été trouvée");
            return $this->redirectToRoute('artist_musicList');
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->getArtist() !== $music->getArtist()) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $em->remove($music);
        $em->flush();

        $this->addFlash("success", "La musique à bien été supprimée !");
        return $this->redirectToRoute('artist_musicList');
    }

    #[Route('/artist/music/edit/{id}', name: 'delete_music')]
    public function edit($id, MusicRepository $musicRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        /** @var Music */
        $music = $musicRepository->find($id);

        if (!$music) {
            $this->addFlash("danger", "La musique n'a pas été trouvée");
            return $this->redirectToRoute('artist_profile');
        }
        
        $user = $this->getUser();

        if ($user->getArtist() !== $music->getArtist()) {
            $this->addFlash("danger", "Vous n'avez pas accès à cette page");
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(MusicEditFormType::class, $music);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $music = $form->getData();
            $music
                ->SetSlug(strtolower($slugger->slug($music->getName())));

            $em->flush();

            $this->addFlash("success", "La musique a bien été modifiée !");
            return $this->redirectToRoute('artist_musicList');

        }

        $formview = $form->createView();

        return $this->render('artist/musicEdit.html.twig', [
            'formview' => $formview,
        ]);
    }
}
