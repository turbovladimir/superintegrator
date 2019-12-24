<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 24.12.2019
 * Time: 16:30
 */

namespace App\Controller;


use App\Entity\Blog\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends BaseController
{
    private $postRepository;
    
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    
    public function list($page)
    {
        return $this->render('blog/posts.html.twig', ['posts' => $this->postRepository->findAll()]);
    }
    
    public function show_post(Request $request)
    {
        if ($slug = $request->query->get('slug')) {
            return $this->render('blog/post.html.twig',
                ['post' => $this->postRepository->findOneBy(['slug' => $slug])]);
        }
        
        return $this->mainPage();
    }
    
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function new(Request $request)
    {
        $slugify = new Slugify();
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($slugify->slugify($post->getTitle()));
            $em = $this->postRepository->getEntityManager();
            $em->persist($post);
            $em->flush();
        
            return $this->redirectToRoute('blog.list');
        }
        
        return $this->render('blog/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}