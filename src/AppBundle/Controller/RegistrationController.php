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


class RegistrationController extends Controller
{
    public function UserNotFound(){
        return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"user"})
     * @Rest\Post("/register")
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