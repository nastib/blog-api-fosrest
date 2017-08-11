<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Exception\ResourceValidationException;
use AppBundle\Representation\Articles;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpFoundation\Response;
use Hateoas\Representation\PaginatedRepresentation;
use Nelmio\ApiDocBundle\Annotation as Doc;


class ArticleController extends FOSRestController
{
    /**
     * @Rest\Get("/articles", name="app_article_list")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of movies per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     * 
     * @Doc\ApiDoc( 
     *     section="Articles", 
     *     resource=true, 
     *     description="Get the list of all articles." 
     * )      
     */
    public function listAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('AppBundle:Article')->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        return new Articles($pager);
    }

    /**
     * @Rest\Get(
     *     path = "/articles/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @Rest\View
     * 
     * @Doc\ApiDoc(
     *     section="Articles", 
     *     resource=true,
     *     description="Get one article.",
     *     requirements={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "requirements"="\d+",
     *             "description"="The article unique identifier."
     *         }
     *     }
     * )
     * 
     */
    public function showAction(Article $article)
    {
        $this->get('logger')->info('ressource article '. $article->getId().' trouvé avec suscces');
        $this->container->get(logger::class)->info(' ** Ressource '. $article->getId().' Ok');
        return $article;
    }

    /**
     * @Rest\Post("/articles")
     * @Rest\View(StatusCode = 201)
     * @ParamConverter(
     *      "article", 
     *      converter="fos_rest.request_body"
     * )
     * 
     * @Doc\ApiDoc(
     *     section="Articles", 
     *     resource=true,
     *     description="Add new article resource.",
     *     statusCodes={ 
     *        201="Returned when created", 
     *        400="Returned when a violation is raised by validation" 
     *     } 
     * )
     */
    public function createAction(Article $article, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($article);
        $em->flush();
        
        
        return $article;
    }

    /**
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "/articles/{id}",
     *     name = "app_article_update",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("newArticle", converter="fos_rest.request_body")
     */
    public function updateAction(Article $article, Article $newArticle, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $article->setTitle($newArticle->getTitle());
        $article->setContent($newArticle->getContent());

        $this->getDoctrine()->getManager()->flush();

        return $article;
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "/articles/{id}",
     *     name = "app_article_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function deleteAction(Article $article)
    {
        $this->getDoctrine()->getManager()->remove($article);

        return;
    }
}
