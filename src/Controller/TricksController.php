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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TricksController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TricksRepository $repository): Response
    {
        $allTricks = $repository->findAll();
        return $this->render('tricks/index.html.twig', [
            'allTricks' => $allTricks
        ]);
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
    public function newTricks(Request $request)
    {
        $newTricks = new Tricks();
        $form = $this->createForm(CreateTricksType::class, $newTricks);
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
                $newTricks->addMedia($img);
            }
            $newTricks->setCreatedAt(new DateTimeImmutable());
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
     * @Route("/tricks/{id<\d+>}", name="oneTricks")
     */
    public function oneTricks(Request $request, TricksRepository $repository, $id)
    {
        $tricks = $repository->find($id);
        
        $newComment = new Comment();
        $form = $this->createForm(CommentTricksType::class, $newComment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            dd("test");
            dd($tricks);
            $newComment->setTricks($tricks);
            $newComment->setCreatedAt(new DateTimeImmutable());
            $tricks->addComment($newComment);
            dd($tricks);
            $em = $this->getDoctrine()->getManager();
            $em->persist($newComment);
            $em->flush();

            $this->addFlash('success', 'Votre nouveau tricks est crée !!');
            return $this->redirectToRoute('home');
        }

        return $this->render('tricks/oneTricks.html.twig', [
            'formNewComment' => $form->createView(),
            'tricks' => $tricks
        ]);
    }

    
}
