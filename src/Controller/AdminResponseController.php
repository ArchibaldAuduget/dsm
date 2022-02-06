<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Artist;
use App\Repository\UserRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArtistRequestRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminResponseController extends AbstractController
{
    #[Route('/admin/response/list', name: 'response_list')]
    public function list(ArtistRequestRepository $artistRequestRepository): Response
    {
        $artistRequest = $artistRequestRepository->findBy(['status' => 'PENDING']);

        return $this->render('admin_response/currentList.html.twig', [
            'artistRequest' => $artistRequest,
        ]);
    }

    #[Route('/admin/response/{id}', name: 'response_read', priority: -1)]
    public function read($id, ArtistRequestRepository $artistRequestRepository)
    {
        $artistRequest = $artistRequestRepository->find($id);

        if (!$artistRequest) {
            $this->addFlash("danger", "La demande en question n'a pas été trouvée");
            return $this->redirectToRoute('response_list');
        }

        return $this->render('admin_response/read.html.twig', [
            'artistRequest' => $artistRequest,
        ]);
    }

    #[Route('/admin/response/{id}/accept', name: 'response_accept')]
    public function accept($id, ArtistRequestRepository $artistRequestRepository, SluggerInterface $slugger, EntityManagerInterface $em, Security $security, UserRepository $userRepository)
    {
        $artistRequest = $artistRequestRepository->find($id);

        if (!$artistRequest) {
            $this->addFlash("danger", "La demande en question n'a pas été trouvée");
            return $this->redirectToRoute('response_list');
        }

        $artistRequest
            ->setStatus('ACCEPTED')
            ->setAdmin($this->getUser());
        // $user = new User;
        $user = $userRepository->findBy(['id' => $artistRequest->getUser()]);
        $user[0]->setRoles(["ROLE_ARTIST"]);
        // dd($this->getUser());
        $artist = new Artist;
        $artist
            ->setUser($user[0])
            ->setRequest($artistRequest)
            ->setName($artistRequest->getArtistName())
            ->setSlug(strtolower($slugger->slug($artist->getName())))
            ->setImg('/img/dessin_micro.jpg');

        $em->persist($artist);
        $em->flush();

        $this->addFlash("success", "La demande a bien été acceptée");
        return $this->render('admin_response/read.html.twig', [
            'artistRequest' => $artistRequest,
        ]);
        
    }

    #[Route('/admin/response/{id}/deny', name: 'response_deny')]
    public function deny($id, ArtistRequestRepository $artistRequestRepository, EntityManagerInterface $em)
    {
        $artistRequest = $artistRequestRepository->find($id);

        if (!$artistRequest) {
            $this->addFlash("danger", "La demande en question n'a pas été trouvée");
            return $this->redirectToRoute('response_list');
        }

        $artistRequest->setStatus('DENIED');
        $em->flush();

        $this->addFlash("success", "La demande a bien été refusée");

        return $this->render('admin_response/read.html.twig', [
            'artistRequest' => $artistRequest,
        ]);
    }

    #[Route('/admin/response/accepted', name: 'response_acceptedList')]
    public function acceptedList(ArtistRequestRepository $artistRequestRepository): Response
    {
        $artistRequest = $artistRequestRepository->findBy(['status' => 'ACCEPTED']);

        return $this->render('admin_response/acceptedList.html.twig', [
            'artistRequest' => $artistRequest,
        ]);
    }

    #[Route('/admin/response/denied', name: 'response_deniedList')]
    public function deniedList(ArtistRequestRepository $artistRequestRepository): Response
    {
        $artistRequest = $artistRequestRepository->findBy(['status' => 'DENIED']);

        return $this->render('admin_response/deniedList.html.twig', [
            'artistRequest' => $artistRequest,
        ]);
    }

    #[Route('/admin/response/{id}/remove', name: 'remove_request')]
    public function remove($id, ArtistRequestRepository $artistRequestRepository, EntityManagerInterface $em)
    {
        $artistRequest = $artistRequestRepository->find($id);

        if (!$artistRequest) {
            $this->addFlash("danger", "La demande en question n'a pas été trouvée");
            return $this->redirectToRoute('response_list');
        }


        if ($artistRequest->getStatus() !== 'DENIED') {
            $this->addFlash("danger", "Une erreur s'est produite. Cette demande ne peut pas être supprimée");
            return $this->redirectToRoute('response_list');
        } 

        $em->remove($artistRequest);
        $em->flush();

        $this->addFlash("success", "La demande d'artiste a bien été supprimée");
        return $this->redirectToRoute('response_list');
    }
}
