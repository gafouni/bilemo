<?php

namespace App\Controller;
//use JMS\Serializer\Serializer;
use OpenApi\Annotations as OA;
use App\Repository\PhoneRepository;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api", name="api_")
 */

class PhoneController extends AbstractController
{
    // public function __construct(PaginatorInterface $paginator)
    // {
    //     $this->paginator = $paginator;
        
    // }
 

    /**
     * Cette méthode permet de récupérer l'ensemble des telephones.
     * 
     * @OA\Response(
     *     response=200,
     *     description="Liste des telephones",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class))
     *     )
     * )
     * 
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
     *
     * @param PhoneRepository $phoneRepository
     * @param SerializerInterface $serializer
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return JsonResponse
     */
    /** 
     * @Route("/phones/list", name="phoneList", methods={"GET"})
     */
    public function phoneList(PhoneRepository $phoneRepository, SerializerInterface $serializer,
                                Request $request, TagAwareCacheInterface $cachePool, 
                                PaginatorInterface $paginator): JsonResponse

    {        
        $data = $phoneRepository->findAll();

        $phoneList = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            4
        );

        $jsonPhoneList = $serializer->serialize($phoneList, 'json');
        

        return new JsonResponse($jsonPhoneList, Response::HTTP_OK, [], true);
    }



    /**
     * Cette méthode permet de récupérer un telephone avec ses details.
     *
     * @OA\Response(
     *     response=200,
     *     description="Details du telephone selectionne",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class))
     *     )
     * )
     * 
     * @OA\Response(
     *     response=404,
     *     description="La ressource demandee est introuvable",
     * )
     * 
     * @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="L'id du telephone que l'on veut recuperer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Tag(name="Phones")
     * @Security(name="Bearer")
     *
     * @param int $id
     * @param PhoneRepository $phoneRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    /**
     * @Route("/phones/{id}", name="showPhone", methods={"GET"})
     */
    public function showPhone(int $id, SerializerInterface $serializer, PhoneRepository $phoneRepository):JsonResponse
    {
        $phone = $phoneRepository->find($id);
        if($phone){
            $jsonPhone = $serializer->serialize($phone, "json");
            return new JsonResponse($jsonPhone, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




}
