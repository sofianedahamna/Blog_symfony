<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Entity\Comment;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class BlogController extends AbstractController
{
    #[Route('/', name: 'app_blog' ,defaults: ['page' => '1'],methods:['GET'])]
    #[Route('/page/{page<[1-9]\d-{0,8}>}', name: 'app_blog_page', methods:['GET'])]
    public function index(int $page,Request $request ,PostRepository $postRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $posts = $postRepository->getPostPaginator($offset);

        return $this->render('blog/index.html.twig', [
           
            'publications' => $posts,
            'previous' => $offset - PostRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($posts),$offset + PostRepository::PAGINATOR_PER_PAGE),

        ]);
     
       
    }



    private EntityManagerInterface $entityManager;

    public function  __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    } 
    #[Route('/publication/{id}', name:'publication')]
    public function show(Request $request ,Post $post,CommentRepository $commentRepository,PostRepository $postRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($post,$offset);
        $paginator = $postRepository->getPostPaginator($offset);
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $comment->setPublishedAt(new \DateTimeImmutable());
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('publication', ['id' => $post->getId()]);
        }
        
        return $this->render('blog/show.html.twig', [
            'publication' => $post,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator),$offset + CommentRepository::PAGINATOR_PER_PAGE),
            'comment_form'=> $form->createView(),

        ]);

    }
}
