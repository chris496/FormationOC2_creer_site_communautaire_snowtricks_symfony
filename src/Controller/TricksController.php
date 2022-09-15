<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Tricks;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\EditTricksType;
use App\Form\CreateTricksType;
use App\Services\VideoService;
use App\Form\CommentTricksType;
use App\Services\PagingService;
use Doctrine\ORM\EntityManager;
use App\Services\GravatarService;
use App\Repository\UserRepository;
use App\Repository\MediaRepository;
use App\Repository\TricksRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TricksController extends AbstractController
{
    public $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * @Route("/", name="home")
     */
    public function index(TricksRepository $repository, MediaRepository $mediaRepo, Request $request): Response
    {
        $limit = 15;
        $page = (int)$request->query->get("page", 1);
        $allTricks = $repository->getPaginatedTricks($page, $limit);
        //$allTricks = $pagingservice->paging(1, 15);
        //dd($allTricks);
        foreach ($allTricks as $tricks) {
            $media = ($mediaRepo->findOneBy([ 'tricks' => $tricks,
            'favorite' => true ]));
        }

        return $this->render('tricks/index.html.twig', [
            'allTricks' => $allTricks,
        ]);
    }

    /**
     * @Route("/paging/{page}", name="see_more")
     */
    public function pagingTricks(int $page, MediaRepository $mediaRepo, TricksRepository $repository, Request $request): Response
    {
        $limit = 5;
        $page = (int)$request->query->get("page", $page);
        $allTricks = $repository->getPaginatedTricks($page, $limit);
        $array = [];
        foreach ($allTricks as $tricks) {
            $media = ($mediaRepo->findOneBy([
                'tricks' => $tricks,
                'favorite' => true
            ]));
            $array[]=[
                'user' => $this->getUser() ? '1' : '0',
                'id' => $tricks->getId(),
                'title' => $tricks->getTitle(),
                'content' => $tricks->getContent(),
                'slug' => $tricks->getSlug(),
                'medias' => $media ? $media->getName() : "default.jpg",
                'tricks' => $this->generateUrl('oneTricks', ['slug' => $tricks->getSlug()]),
                'editTricks' => $this->generateUrl('editTricks', ['id' => $tricks->getId()]),
                'delete' => $tricks->getId()
            ];
        }

        return $this->json($array, 200, [], ['groups' => 'tricks:read']);
    }

    /**
     * @Route("/tricks", name="allTricks")
     */
    public function allTricks(TricksRepository $repository): Response
    {
        $allTricks = $repository->findAll();

        return $this->render('tricks/allTricks.html.twig', [
            'allTricks' => $allTricks
        ]);
    }

    /**
     * @Route("/newTricks", name="newTricks")
     */
    public function newTricks(Request $request, SluggerInterface $slugger, VideoService $multiVideo)
    {
        $newTricks = new Tricks();
        $form = $this->createForm(CreateTricksType::class, $newTricks);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('medias')->getData();
            $i = 0;
            foreach ($images as $image) {
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Media();
                if ($i == 0) {
                    $img->setFavorite(true);
                    dump($fichier);
                }
                $i++;
                $img->setName($fichier);
                $newTricks->addMedia($img);
            }
            if($urlVideo = $form->get('urls')->getData()){
                $mvideos = $multiVideo->multi_video($urlVideo);
                foreach ($mvideos as $mvideo) {
                    $video = new Media();
                    $video->setName('video')
                        ->setUrl($mvideo);
                    $newTricks->addUrl($video);
                }
            }
            $newTricks->setSlug(strtolower($slugger->slug($form->get('title')->getData())))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setUser($this->getUser());
            $this->em->persist($newTricks);
            $this->em->flush();
            $this->addFlash('success', 'Votre nouveau tricks est crée !!');

            return $this->redirectToRoute('home');
        }

        return $this->render('tricks/newTricks.html.twig', [
            'formNewTricks' => $form->createView()
        ]);
    }

    /**
     * @Route("/editTricks/{id<\d+>}", name="editTricks")
     */
    public function editTricks(Request $request, Tricks $tricks, MediaRepository $mediaRepository, VideoService $multiVideo)
    {
        $favorite = $mediaRepository->findOneBy(['tricks' => $tricks, 'favorite' => 1]);
        $form = $this->createForm(EditTricksType::class, $tricks);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('medias')->getData();
            foreach ($images as $image) {
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Media();
                $img->setName($fichier);
                $tricks->addMedia($img);
            }
            if($urlVideo = $form->get('urls')->getData()){
                $mvideos = $multiVideo->multi_video($urlVideo);
                foreach ($mvideos as $mvideo) {
                    $video = new Media();
                    $video->setName('video')
                        ->setUrl($mvideo);
                    $tricks->addUrl($video);
                }
            }
            $tricks->setUpdatedAt(new DateTimeImmutable());
            $this->em->flush();
            $this->addFlash('success', 'Votre tricks a été modifié !!');

            return $this->redirectToRoute('oneTricks', ['slug'=> $tricks->getSlug()]);
        }

        return $this->render('tricks/editTricks.html.twig', [
            'formEditTricks' => $form->createView(),
            'tricks' => $tricks,
            'favorite' => $favorite
        ]);
    }

    /**
     * @Route("/deleteTricks/{id<\d+>}", name="deleteTricks")
     */
    public function deleteTricks(Tricks $tricks)
    {
        $medias = $tricks->getMedias();
        if ($medias) {
            foreach ($medias as $media) {
                $name = $this->getParameter("images_directory") . '/' .$media->getName();
                if (file_exists($name)) {
                    unlink($name);
                }
            }
        }
        $this->em->remove($tricks);
        $this->em->flush();

        return $this->redirectToRoute(('home'));
    }

    /**
     * @Route("/tricks/{slug}/favoriteMedia/{id<\d+>}", name="favoriteMedia")
     */
    public function favoriteMedia(string $slug, MediaRepository $mediaRepository, TricksRepository $tricksRepository, int $id)
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);
        $med = $mediaRepository->findOneBy(['tricks' => $trick, 'id' => $id]);
        foreach ($trick->getMedias() as $media) {
            $media->setFavorite(false);
            $this->em->persist($media);
        }
        $med->setFavorite(true);
        $this->em->persist($med);
        $this->em->flush();

        return $this->redirectToRoute('oneTricks', ['slug'=> $trick->getSlug()]);
    }

    /**
     * @Route("/deleteMedia/{id}", name="editDeleteMedia")
     */
    public function deleteMedia(Media $media)
    {
        $name = $this->getParameter("images_directory") . '/' .$media->getName();
        if (file_exists($name)) {
            unlink($name);
        }
        $this->em->remove($media);
        $this->em->flush();

        return $this->redirectToRoute(('home'));
    }

    /**
     * @Route("/tricks/{slug}", name="oneTricks")
     */
    public function oneTricks(string $slug, Request $request, CommentRepository $repository, TricksRepository $tricksRepository, MediaRepository $mediaRepository, GravatarService $gravatar, UserRepository $userRepository)
    {
        $user = $this->getUser();
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);
        $favorite = $mediaRepository->findOneBy(['tricks' => $trick, 'favorite' => 1]);
        $newComment = new Comment();
        $form = $this->createForm(CommentTricksType::class, $newComment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment->setTricks($trick);
            $newComment->setCreatedAt(new DateTimeImmutable());
            $newComment->setUser($user);
            $trick->addComment($newComment);
            $this->em->persist($newComment);
            $this->em->flush();
            $this->addFlash('success', 'Votre nouveau commentaire est crée !!');

            return $this->redirectToRoute('oneTricks', ['slug'=> $trick->getSlug()]);
        }
        $limit = 4;
        $page = (int)$request->query->get("page", 1);
        $id = $trick->getId();
        $allComments = $repository->getPaginatedComment($id, $page, $limit);
        $avatar = $gravatar->get_gravatar('form', 80, 'mp', 'g', false, array());

        return $this->render('tricks/oneTricks.html.twig', [
            'formNewComment' => $form->createView(),
            'tricks' => $trick,
            'allcomments' => $allComments,
            'favorite' => $favorite,
            'avatar' => $avatar
        ]);
    }

    /**
     * @Route("/tricks/{slug}/pagingComment/{page}", name="see_more_comment")
     */
    public function pagingComment(string $slug, int $page, CommentRepository $repository, Request $request, TricksRepository $tricksRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);
        $limit = 5;
        $page = (int)$request->query->get("page", $page);
        $id = $trick->getId();
        $allComments = $repository->getPaginatedComment($id, $page, $limit);

        return $this->json($allComments, 200, [], ['groups' => 'comment:read']);
    }
}
