<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Form\CommentType;
use AppBundle\Form\PostType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @ApiDoc(
     *  resource=true,
     *  description="Retourne le post associé à l'id",
     *  section="Posts",
     *  requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The post unique identifier."
     *         }
     *     },
     *  output="AppBundle\Entity\Post",
     *  statusCodes={
     *         200="Returned when everything works",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  section="Posts",
     *  description="Retourne tout les posts en BDD",
     *  output="AppBundle\Entity\Post",
     *  statusCodes={
     *         200="Returned when everything works",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  section="Posts et Users",
     *  description="Retourne tout les posts de l'utilisateur id",
     *  output="AppBundle\Entity\Post",
     *  requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The user unique identifier."
     *         }
     *     },
     *  statusCodes={
     *         200="Returned when everything works",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  section="Posts",
     *  description="Ajoute un Post",
     *  output="AppBundle\Entity\Post",
     *  requirements={
     *         {
     *             "name"="titre",
     *             "dataType"="string",
     *             "description"="Le titre du Post"
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="text",
     *             "description"="La description du Post"
     *         }
     *     },
     *  statusCodes={
     *         201="Returned when a post is created",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  section="Posts",
     *  description="Supprime le post associé à id",
     *  statusCodes={
     *         204="Returned when the post is deleted",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  section="Posts",
     *  description="Modifie le post associé à id",
     *  statusCodes={
     *         200="Returned when the post is deleted",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
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
     * @Rest\Patch("/post/{id}")
     * @ApiDoc(
     *  resource=true,
     *  section="Posts",
     *  description="Modifie le post associé à id",
     *  statusCodes={
     *         200="Returned when the post is deleted",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
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
     * @ApiDoc(
     *  resource=true,
     *  section="Users",
     *  description="Retourne tout les utilisateurs",
     *  statusCodes={
     *         200="Returned when everything is OK",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
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
