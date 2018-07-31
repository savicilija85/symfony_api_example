<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\Validate;

class PostController extends Controller
{
    private $serializer;
    private $validate;

    public function __construct(SerializerInterface $serializer, Validate $validate)
    {
        $this->serializer = $serializer;
        $this->validate =$validate;
    }

    /**
     * @param $id
     * @Route("/api/posts/{id}", name="show_post", methods="GET")
     * @return JsonResponse
     */
    public function showPost($id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if(empty($post)){
            $response = [
                'code' => 1,
                'message' => 'Post not found',
                'error' => null,
                'result' => null
            ];
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($post, 'json');

        $response = [
            'code' => 0,
            'message' => 'success',
            'error' => null,
            'result' => json_decode($data)
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validate
     * @return JsonResponse
     * @Route("/api/posts", name="create_post_post", methods="POST")
     */
    public function createPost(Request $request, ValidatorInterface $validate){

        $data = $request->getContent();

        $post = $this->serializer->deserialize($data, Post::class, 'json');

        $response = $this->validate->validateRequest($post);

        if(!empty($response)){
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        $response = [
            'code' => 0,
            'message' => 'Post created!',
            'error' => null,
            'result' => null
        ];

        return new JsonResponse($response, Response::HTTP_CREATED);

    }

    /**
     * @Route("/api/posts", name="list_posts", methods="GET")
     * @return JsonResponse
     */
    public function listPost(){
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();

        if(count($posts) == 0){
            $response = [
                'code' => 1,
                'message' => 'No data found',
                'error' => null,
                'result' => null
            ];

            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($posts, 'json');

        $response = [
            'code' => 0,
            'message' => 'Data found',
            'error' => null,
            'result' => json_decode($data)
        ];

        return new JsonResponse($response, Response::HTTP_OK);

    }

    /**
     * @param $id
     * @param Request $request
     * @Route("/api/posts/{id}", name="update_post", methods="PUT")
     * @return JsonResponse
     */
    public function updatePost(Request $request, $id){
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if(empty($post)){
            $response = [
                'code' => 1,
                'message' => "Post with $id does not exists",
                'error' => null,
                'result' => null
            ];

            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $body = $request->getContent();

        $data = $this->serializer->deserialize($body, Post::class, 'json');

        $response = $this->validate->validateRequest($data);

        if(!empty($response)){
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        $post->setTitle($data->getTitle());
        $post->setDescription($data->getDescription());

        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();

        $response = [
            'code' => 0,
            'message' => "Post with $id successfully updated",
            'error' => null,
            'result' => null
        ];

        return new JsonResponse($response, Response::HTTP_OK);

    }

    /**
     * @param $id
     * @Route("api/posts/{id}", name="delete_post", methods="DELETE")
     * @return JsonResponse
     */
    public function deletePost($id){

        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if(empty($post)){
            $response = [
                'code' => 1,
                'message' => "Post with $id does not exists",
                'error' => null,
                'result' => null
            ];

            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $response = [
            'code' => 0,
            'message' => "Post with $id successfully deleted",
            'error' => null,
            'result' => null
        ];

        return new JsonResponse($response, Response::HTTP_OK);

    }


}
