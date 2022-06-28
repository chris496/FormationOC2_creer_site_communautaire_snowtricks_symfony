<?php

namespace App\Controller;

use DateTime;
use App\Entity\Media;
use App\Entity\Tricks;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\EditTricksType;
use App\Form\CreateTricksType;
use App\Form\CommentTricksType;
use App\Repository\TricksRepository;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TricksController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TricksRepository $repository, Request $request): Response
    {
        $limit = 15;
        $page = (int)$request->query->get("page", 1);
        $allTricks = $repository->getPaginatedTricks($page, $limit);
//dd($allTricks);
        return $this->render('tricks/index.html.twig', [
            'allTricks' => $allTricks
        ]);
    }

    /**
     * @Route("/paging/{page}", name="see_more")
     */
    public function pagingTricks(int $page,MediaRepository $mediaRepo, TricksRepository $repository, Request $request): Response
    {
        $limit = 5;
        $page = (int)$request->query->get("page", $page);
        $allTricks = $repository->getPaginatedTricks($page, $limit);
        //dd($allTricks);
        //$test = $repository->getMediasTest(277);
        //dd($test);
        $array = [];
        foreach($allTricks as $tricks){
            $media = ($mediaRepo->findOneBy([
                'tricks' => $tricks,
                'favorite' => true
            ]));
            $array[]=[
                'user' => $this->getUser()?'1':'0',
                'id' => $tricks->getId(),
                'title' => $tricks->getTitle(),
                'content' => $tricks->getContent(),
                'slug' => $tricks->getSlug(),
                'medias' => $media ?$media->getName():"/default.jpg",
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
    public function newTricks(Request $request, SluggerInterface $slugger)
    {
        $newTricks = new Tricks();
        $form = $this->createForm(CreateTricksType::class, $newTricks);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('medias')->getData();
            $urlVideo = $form->get('urls')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Media();
                $img->setName($fichier);
                $img->setFavorite(1);
                $newTricks->addMedia($img);
            }

            $video = new Media();
            $video->setName('video');
            $video->setUrl($urlVideo);

            //$slug = $form->get('title')->getData();;
            //dd($slug);
            $newTricks->addUrl($video)
                    ->setSlug(strtolower($slugger->slug($form->get('title')->getData())))
                    ->setCreatedAt(new DateTimeImmutable());
            $em = $this->getDoctrine()->getManager();
            $em->persist($newTricks);
            $em->flush();

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
    public function editTricks(Request $request, Tricks $tricks)
    {
        $form = $this->createForm(EditTricksType::class, $tricks);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $images = $form->get('medias')->getData();
            foreach($images as $image){
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $img = new Media();
                $img->setName($fichier);
                $tricks->addMedia($img);
            }
            $tricks->setUpdatedAt(new DateTimeImmutable());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Votre tricks a été modifié !!');
            return $this->redirectToRoute('home');
        }

        return $this->render('tricks/editTricks.html.twig', [
            'formEditTricks' => $form->createView(),
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/deleteTricks/{id<\d+>}", name="deleteTricks")
     */
    public function deleteTricks(Request $request, Tricks $tricks)
    {
        $medias = $tricks->getMedias();
        if($medias){
            foreach($medias as $media){
                $name = $this->getParameter("images_directory") . '/' .$media->getName();
                if(file_exists($name)){
                    unlink($name);
                }
            }
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($tricks);
        $em->flush();

        return $this->redirectToRoute(('home'));
    }

    /**
     * @Route("/deleteMedia/{id}", name="editDeleteMedia")
     */
    public function deleteMedia(Request $request, Media $media)
    {
        $name = $this->getParameter("images_directory") . '/' .$media->getName();
        if(file_exists($name)){
            unlink($name);
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($media);
        $em->flush();

        return $this->redirectToRoute(('home'));
    }

    /**
     * @Route("/tricks/{slug}", name="oneTricks")
     */
    public function oneTricks(string $slug, Request $request, CommentRepository $repository, TricksRepository $tricksRepository)
    {
        $trick = $tricksRepository->findOneBy(['slug' => $slug]);
        $newComment = new Comment();
        $form = $this->createForm(CommentTricksType::class, $newComment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newComment->setTricks($trick);
            $newComment->setCreatedAt(new DateTimeImmutable());
            $trick->addComment($newComment);
            $em = $this->getDoctrine()->getManager();
            $em->persist($newComment);
            $em->flush();

            $this->addFlash('success', 'Votre nouveau tricks est crée !!');
            return $this->redirectToRoute('home');
        }
        $limit = 4;
        $page = (int)$request->query->get("page", 1);
        $id = $trick->getId();
        $allComments = $repository->getPaginatedComment($page, $limit, $id);
        return $this->render('tricks/oneTricks.html.twig', [
            'formNewComment' => $form->createView(),
            'tricks' => $trick,
            'all' => $allComments
        ]);
    }

    /**
     * @Route("/tricks/{slug}/pagingComment/{page}", name="see_more_comment")
     */
    public function pagingComment(string $slug, int $page, CommentRepository $repository, Request $request, TricksRepository $tricksRepository): Response
    {
        $trick = $tricksRepository->findOneBy(['id' => $slug]);
        dd($trick);
        $limit = 5;
        $page = (int)$request->query->get("page", $page);
        $allComments = $repository->getPaginatedComment($page, $limit, $id);
        //dd($allComments);
        return $this->json($allComments, 200, [], ['groups' => 'comment:read']);
    }
}
