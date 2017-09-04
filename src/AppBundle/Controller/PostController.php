<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{

    /**
     * @param $slug
     * @Route("/post/{slug}",
     *          name="post_details"
     * )
     * @return Response
     */
    public function detailsAction($slug){

        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        $post = $repository->findOneBySlug($slug);

        if(! $post){
            throw new NotFoundHttpException("post introuvable");
        }

        return $this->render("post/details.html.twig", [
            "post" => $post,
            "answerList" => $post->getAnswers()
        ]);
    }

    /**
     * @param $year
     * @return Response
     * @Route("/post-par-annee/{year}", name="post_by_year",
     *     requirements={"year":"\d{4}"})
     */
    public function postByYearAction($year){

        $repository = $this->getDoctrine()->getRepository("AppBundle:Post");
        $post=$repository->getPostByYear($year);




        return $this->render("default/theme.html.twig", ["postList"=>$post, "title"=>"Liste de postes de l'année  $year "] );
    }

}