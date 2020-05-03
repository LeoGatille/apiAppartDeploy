<?php

namespace App\ApiController;

use App\Entity\Status;
use App\Repository\StatusRepository;
use App\Repository\WineRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/status", host="api.appart.do")
 */
class StatusController extends AbstractFOSRestController
{

  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_status_index"
   * )
   */
  public function index(StatusRepository $statusRepository): View
  {
    $data = $statusRepository->findAll();
    $statuses = [];
    foreach ($data as $status) {
      array_push($statuses, $this->normalize($status));
    }
    return View::create($statuses, Response::HTTP_OK);
  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'statusName',
        'wines' => [
          'id',
          'wineName',
          'winePrice',
          'label' => ['id', 'labelName'],
          'category' => ['id', 'categoryName', 'categoryOrder'],
          'designation' => ['id', 'designationName'],
          'vintage' => ['id', 'vintageYear'],
          'color' => ['id', 'colorName', 'colorOrder']
        ]
      ]]);
    return $object;
  }
}
