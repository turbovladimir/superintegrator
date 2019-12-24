<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 24.12.2019
 * Time: 16:30
 */

namespace App\Controller;


use App\Repository\PostRepository;
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
    
    public function new()
    {
        /* todo create new post*/
    }
}