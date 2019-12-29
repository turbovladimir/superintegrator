<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\CodeGenerator;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegisterController
 *
 * @package App\Controller
 */
class RegisterController extends AbstractController
{
    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request                      $request
     * @param CodeGenerator                $codeGenerator
     * @param Mailer                       $mailer
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        CodeGenerator $codeGenerator,
        Mailer $mailer
    ) {
        $user = new User();
        $form = $this->createForm(
            Uset::class,
            $user
        );
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            
            $password = $passwordEncoder->encodePassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($password);
            $user->setConfirmationCode($codeGenerator->getConfirmationCode());
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($user);
            $em->flush();
            
            $mailer->sendConfirmationMessage($user);
        }
        
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
