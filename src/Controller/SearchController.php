<?php

namespace App\Controller;

use App\Repository\ArtistRepository;
use App\Repository\MusicRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class SearchController extends AbstractController
{
    #[Route('/', name: 'search')]
    public function index(Request $request, SluggerInterface $slugger, MusicRepository $musicRepository, ArtistRepository $artistRepository): Response
    {
        if ($request->query->get('search')) {
            $search = strtolower($slugger->slug($request->query->get('search')));
            $artist = $artistRepository->findBy(['slug' => $search]);
            $music = $musicRepository->findBy(['slug' => $search]);

            return $this->render('search/search.html.twig', [
                'artist' => $artist,
                'music' => $music
            ]);
        }

        return $this->render('home/index.html.twig');
        
    }
}
