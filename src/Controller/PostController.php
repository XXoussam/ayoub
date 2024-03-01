<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\services\FileUploader;
use App\services\SendNotif;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/post', name: 'app_post')]
class PostController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();
        dump($posts);
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }
    #[Route('/create', name: 'create')]
    public function create(Request $request,FileUploader $fileUploader,/*SendNotif $notification*/): Response
    {


        $post = new Post();

        $form = $this->createForm(PostType::class,$post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            /** @var UploadedFile $file */
            $file = $request->files->get('post')['image'];
            if($file){
                $filename = $fileUploader->uploadFile($file);
                $post->setImage($filename);
            }


            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Post was created');

            return $this->redirectToRoute('app_postindex');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/show/{id}', name: 'show')]
    public function show(Post $post){
        //$post = $postRepository->find($id);
        dump($post);
        // create show view
        return $this->render('post/show.html.twig',[
            'post' => $post
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function remove(Post $post){
        $fileName = $post->getImage();

        // If you're using annotation routing, you can access the container using $this->container
        $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
        // Full path to the image file
        $filePath = $uploadsDirectory . '/' . $fileName;
        // Check if the file exists
        if (file_exists($filePath)) {
            // Attempt to delete the file
            if (unlink($filePath)) {
                // File deleted successfully, proceed to remove the entity from the database
                $em = $this->getDoctrine()->getManager();
                $em->remove($post);
                $em->flush();

                $this->addFlash('success', 'Post was removed');
            } else {
                // Failed to delete the file
                $this->addFlash('error', 'Failed to delete the image file');
            }
        } else {
            // File does not exist
            $this->addFlash('error', 'Image file not found');
        }



        return $this->redirectToRoute('app_postindex');
    }
}
