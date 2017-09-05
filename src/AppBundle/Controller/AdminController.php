<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Theme;
use AppBundle\Form\ThemeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 * Class AdminController
 * @package AppBundle\Controller
 */
class AdminController extends Controller
{

    /**
     * @Route("/", name="admin_home")
     * @return Response
     */
    public function indexAction(){
        return $this->render("admin/index.html.twig");
    }

    /**
     * @Route("/login", name="admin_login")
     * @return Response
     */
    public function admin_loginAction(){


        $securityUtils=$this->get("security.authentication_utils");
        $lastUserName= $securityUtils->getLastUsername();
        $error=$securityUtils->getLastAuthenticationError();

       return $this->render("default/generic-login.html.twig",["action"=>$this->generateUrl("admin_login_check"), "title"=>"login utilisateur",
                                                                     "error"=>$error,"lastUserName"=>$lastUserName ]);
    }

    /**
     * @Route("/themes", name="admin_themes")
     * @return Response
     */
    public function themeAction(Request $request){


        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Theme");
        $themeList = $repository->findAll();


        //creation du formulaire
        $theme= new Theme();
        $form=$this->createForm(ThemeType::class, $theme);

        //hydratation de l'entité   | $_GET est recuperé avec une variable  $request de type Request en param de la function
        $form->handleRequest($request);

        //traitement du formulaire

        if ($form->isSubmitted() and $form->isValid()){
            //Persistance de l'entité
            $em=$this->getDoctrine()->getManager();
            $em->persist($theme);
            $em->flush();

            return $this->redirectToRoute("admin/themes");
        }

        return $this->render("admin/theme.html.twig", ["themeList" => $themeList, "themeForm"=>$form->createView()]);
    }

}