<?php 

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog")
 */

class BlogController extends AbstractController
{
    private const POSTS = [
        [
            'id'=> 1,
            'slug'=>'Hello world',
            'title'=>'Hello world!'
        ],
        [
            'id'=>2,
            'slug'=>'athor-post',
            'title'=> 'This is athor post!'
        ],
        [
            'id'=>3,
            'slug'=> 'last-example',
            'title'=>'This is the last example'
        ],
    ];
    /**
     * @Route("/{page}",name="blog_list", defaults={"page": 5}, requirements={"id"="\d+"})
     */
    public function list($page =1, Request $request)
    {
        $limit=$request->get('limit', 10);
        $repository=$this->getDoctrine()->getRepository(BlogPost::class);
        $items=$repository->findAll();
        return $this->json(
            [
                'page'=> $page,
                'limit'=> $limit,
                'data'=> array_map(function(BlogPost $item)
                {
                    return $this->generateUrl('blog_by_slug', ['slug'=>$item->getSlug()]);
                }, $items)
            ]
        );
    }
    /**
     * @Route("/post/{id}",name="blog_by_id", requirements={"id"="\d+"})
     */
    public function post(BlogPost $post)
    {
        return $this->json($post);
    }

    /**
     * @Route("/post/{slug}",name="blog_by_slug")
     */
    public function postBySlug($slug)
    {
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug'=>$slug])
        );
    }
    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     */
    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $serializer= $this->get('serialize');
        $blogPost= $serializer->deserialize($request->getContent(), BlogPost::class,'json');
        $em=$this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();
        return $this->json($blogPost);
    }
}