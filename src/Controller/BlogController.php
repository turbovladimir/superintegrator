<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 24.12.2019
 * Time: 16:30
 */

namespace App\Controller;

use App\Entity\Blog\Post;
use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    private $postRepository;
    
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }
    
    public function list($page)
    {
        return $this->render('blog/index.html.twig', ['posts' => $this->postRepository->findAll()]);
    }
    
    public function show_post($slug)
    {
            return $this->render('blog/post/index.html.twig', ['post' => $this->postRepository->findOneBy(['slug' => $slug])]);
    }
    
    /**
     * @param null $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function new($id = null)
    {
        $options = [];
        
        if ($id) {
            $post = $this->postRepository->findOneBy(['id' => $id]);
            $options = ['post' => $post];
        }
        
        return $this->render('blog/post/new.html.twig', $options);
    }
    
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Request $request)
    {
        $em = $this->postRepository->getEntityManager();
        
        if ($id = $request->get('id')) {
            $post = $this->postRepository->findOneBy(['id' => $id]);
        } else {
            $post = new Post();
        }
    
        $title = $request->get('title');
        $post->setTitle($title);
        $post->setSlug((new Slugify())->slugify($post->getTitle()));
        $post->setBody($request->get('body'));
        $em->persist($post);
        $em->flush();
        
        return $this->redirectToRoute('blog');
    }
    
    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($id)
    {
        $em = $this->postRepository->getEntityManager();
        $entity = $this->postRepository->findOneBy(['id' => $id]);
        $em->remove($entity);
        $em->flush();
        
        return $this->redirectToRoute('blog');
    }
}