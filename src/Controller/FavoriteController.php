<?php

namespace App\Controller;

use App\Repository\MusicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{

    #[Route('/favorite/list', name: 'list_favorite')]
    public function list()
    {
        $musics = $this->getUser()->getFavorites();

        return $this->render('favorite/list.html.twig', [
            'musics' => $musics
        ]);
    }

    #[Route('/favorite/add/{id}/{route}', name: 'add_favorite')]
    public function add($id, $route, MusicRepository $musicRepository, EntityManagerInterface $em)
    {
        $music = $musicRepository->find($id);
        $user = $this->getUser();

        if (!$music) {
            $this->addFlash("danger", "La musique n'a pas été trouvé'");
            return $this->redirectToRoute('home');
        }

        if (!$route) {
            $this->addFlash("danger", "Une erreur s'est produite");
            return $this->redirectToRoute('home');
        }

        $user->addFavorite($music);
        $em->persist($user);
        $em->flush();

        $url = '/' . $route;
        $this->addFlash("success", "Musique ajoutée aux favoris");
        return $this->redirect($url);

    }

    #[Route('/favorite/delete/{id}', name: 'delete_favorite')]
    public function delete($id, MusicRepository $musicRepository, EntityManagerInterface $em)
    {
        $music = $musicRepository->find($id);
        $user = $this->getUser();

        if (!$music) {
            $this->addFlash("danger", "La musique n'a pas été trouvé'");
            return $this->redirectToRoute('home');
        }

        // securité si la musique est bien dans favoris
        $count = 0;
        foreach ($user->getFavorites() as $check) {
            if ($check == $music) {
                $count++;
            }
        }

        if ($count < 1) {
            $this->addFlash("danger", "Cette musique n'a pas été trouvée dans vos favoris");
        return $this->redirectToRoute('list_favorite');
        }

        $user->removeFavorite($music);
        $em->flush();

        $this->addFlash("success", "Musique supprimée des favoris");
        return $this->redirectToRoute('list_favorite');
    }

}
