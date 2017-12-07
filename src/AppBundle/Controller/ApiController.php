<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Form\CommentType;
use AppBundle\Form\PostType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiController extends Controller
{
    public function PostNotFound(){
        return View::create(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
    }
    public function CommentNotFound(){
        return View::create(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
    }
    public function UserNotFound(){
        return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"post"})
     * @Rest\Get("/post/{id}")
     */
    public function getPostAction(Post $post)
    {
        if (NULL === $post) {
            return $this->PostNotFound();
        }
        return $post;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"post"})
     * @Rest\Get("/posts")
     */
    public function getPostsAction()
    {
        $posts = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findAll();
        if (NULL === $posts) {
            return $this->PostNotFound();
        }
        return $posts;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"post"})
     * @Rest\Get("user/{id}/posts")
     */
    public function getUserPostsAction($id)
    {
        $posts = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->findByUser($id);
        if (NULL === $posts) {
            return $this->PostNotFound();
        }
        return $posts;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED,  serializerGroups={"post"})
     * @Rest\Post("post")
     */
    public function postPostAction(Request $request)
    {
        $post = new Post();
        $this->getUser()->addPost($post);
        $form = $this->createForm(PostType::class, $post);
        $form->submit($request->request->all());
        if ($form->isValid()){
            $em = $this
                ->getDoctrine()
                ->getManager();

            $em->persist($post);
            $em->flush();
            return $post;
        }
        else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/post/{id}")
     */
    public function deletePostAction(Post $post)
    {
        if (NULL === $post) {
            return;
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
    }


    /**
     * @Rest\View(statusCode=Response::HTTP_OK,  serializerGroups={"post"})
     * @Rest\Put("/post/{id}")
     */
    public function putPostAction(Request $request, Post $post)
    {
        if (NULL === $post) {
            return $this->PostNotFound();
        }

        $form = $this->createForm(PostType::class, $post);
        $form->submit($request->request->all());
        if ($form->isValid()){
            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($post);
            $em->flush();
            return $post;
        }
        else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Patch("/post{id}")
     */
    public function patchPostAction(Request $request, Post $post)
    {
        if (NULL === $post) {
            return $this->PostNotFound();
        }
        $form = $this->createForm(PostType::class, $post);

        $form->submit($request->request->all(), false);
        if ($form->isValid()){
            $em = $this
                ->getDoctrine()
                ->getManager();
            $em->persist($post);
            $em->flush();
            return $post;
        }
        else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"user"})
     * @Rest\Get("/users")
     */
    public function getUsersAction()
    {
        $users = $this
            ->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();
        if (NULL === $users) {
            return $this->UserNotFound();
        }
        return $users;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"user"})
     * @Rest\Get("/users/{id}")
     */
    public function getUserAction(User $user)
    {
        if (NULL === $user) {
            return $this->UserNotFound();
        }
        return $user;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/user/{id}")
     */
    public function deleteUserAction(User $user)
    {
        if (NULL === $user) {
            return;
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"comment"})
     * @Rest\Get("/comment/{id}")
     */
    public function getCommentAction(Comment $comment)
    {
        if (NULL === $comment) {
            return $this->CommentNotFound();
        }
        return $comment;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"comment"})
     * @Rest\Get("/comments")
     */
    public function getCommentsAction()
    {
        $comments = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findAll();
        return $comments;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"comment"})
     * @Rest\Get("/post/{id}/comments")
     */
    public function getPostCommentsAction($id)
    {
        $comments = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findByPost($id);
        if (NULL === $comments) {
            return $this->CommentNotFound();
        }
        return $comments;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"comment"})
     * @Rest\Get("/user/{id}/comments")
     */
    public function getUserCommentAction($id)
    {
        $comments = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findByUser($id);
        if (NULL === $comments) {
            return $this->PostNotFound();
        }
        return $comments;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED,  serializerGroups={"comment"})
     * @Rest\Post("post/{id}/comment")
     */
    public function postCommentAction(Request $request, Post $post)
    {
        if (NULL === $post) {
            return $this->PostNotFound();
        }
        $user = $this->getUser();
        $comment = new Comment();
        $comment->setLikes(0);
        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($request->request->all());
        if ($form->isValid()){
            $em = $this
                ->getDoctrine()
                ->getManager();
            $post->addComment($comment);
            $user->addComment($comment);
            $em->persist($comment);
            $em->flush();
            return $comment;
        }
        else {
            return $form;
        }
    }


}
