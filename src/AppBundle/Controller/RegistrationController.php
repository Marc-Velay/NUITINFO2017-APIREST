<?php
/**
 * Created by PhpStorm.
 * User: kafim
 * Date: 10/11/2017
 * Time: 12:48
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;



class RegistrationController extends Controller
{
    public function UserNotFound(){
        return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/register")
     * @ApiDoc(
     *  resource=true,
     *  section="Users",
     *  description="Inscription d'un utilisateur",
     *  output="AppBundle\Entity\User",
     *  requirements={
     *         {
     *             "name"="username",
     *             "dataType"="string",
     *             "description"="Le nom de l'utilisateur"
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="string",
     *             "description"="L'email de l'utilisateur"
     *         },
     *         {
     *             "name"="plainPassword[first]",
     *             "dataType"="string",
     *             "description"="Le mot de passe exemplaire 1"
     *         },
     *         {
     *             "name"="plainPassword[second]",
     *             "dataType"="string",
     *             "description"="Le mot de passe exemplaire 2"
     *         }
     *     },
     *  statusCodes={
     *         201="Returned when a user is created",
     *         400="Returned when a violation is raised by validation",
     *         401="Returned when the user is not autenticated"
     *     }
     * )
     */
    public function registerAction(Request $request)
    {
        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $userManager->updateUser($user);
            return $user;
        }

        return $form;
    }



}