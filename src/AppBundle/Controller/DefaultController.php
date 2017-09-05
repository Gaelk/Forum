<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Entity\Post;
use AppBundle\Form\AuthorType;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction( Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Theme");
        $postRepository= $this->getDoctrine()
            ->getRepository("AppBundle:Post");
    /*
     *
        $themeList = $repository->findAll();
        return $this->render('default/index.html.twig', ["themeList" => $themeList]);
    */
        //creation formulaire
        $post=new Post();
        $post->setCreatedAt(new \DateTime());
        $form=$this->createForm(PostType::class, $post);

        //hydratation de l'entité   | $_GET est recuperé avec une variable  $request de type Request en param de la function
        $form->handleRequest($request);

        //traitement du formulaire

        if ($form->isSubmitted() and $form->isValid()){
            //Persistance de l'entité
            $em=$this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute("homepage");
        }



        //utilisation de la fonction Repository
        $list=$repository->getAllThemes()->getArrayResult();
        $postListByYear=$postRepository->getPostsGroupedByYear();

        return $this->render('default/index.html.twig', ["themeList" => $list, "postList"=>$postListByYear, "postForm"=>$form->createView()]);
    }

    /**
     * @Route("/theme/{id}", name="theme_details", requirements={"id":"\d+"})
     * @param $id
     * @return Response
     */
    public function themeAction($id){

        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Theme");

        $theme = $repository->find($id);
        //recuperation de la fonction repository getAllThemes(AppBundle/Repository/ThemeRepository.php)
        $allThemes=$repository->getAllThemes()->getResult();


        if(! $theme){
            throw new NotFoundHttpException("Thème introuvable");
        }


        return $this->render('default/theme.html.twig', [
            "theme" => $theme,
            "postList" => $theme->getPosts(),
            "all"=>  $allThemes
        ]);

    }


    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/inscription", name="author_registration")
     */
    public function registrationAction(Request $request){

        $author= new Author();
        $form= $this->createForm( AuthorType::class, $author );

        //hydratation de l'entité   | $_GET est recuperé avec une variable  $request de type Request en param de la function
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){

            //encodage du mot de passe
            $encoderFactory=$this->get("security.encoder_factory");
            $encoder= $encoderFactory->getEncoder($author);
            $author->setPassword($encoder->encodePassword($author->getPlainPassword(), null));


            //Persistance de l'entité
            $em=$this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();


        }
        return $this->render("default/author-registration.html.twig", ["registrationForm"=>$form->createView()]);
    }

    /**
     * @return Response
     * @Route("/author-login", name="author_login")
     */
    public function authorLoginAction(){

        $securityUtils=$this->get("security.authentication_utils");
        $lastUserName= $securityUtils->getLastUsername();
        $error=$securityUtils->getLastAuthenticationError();

        return $this->render(':default:generic-login.html.twig',[
                "action"=>$this->generateUrl("author_login_check"), "title"=>"login Auteur",
                "error"=>$error,"lastUserName"=>$lastUserName

            ]

            );
    }





}
