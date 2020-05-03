<?php

namespace App\ApiController;

use App\Entity\Vintage;
use App\Form\VintageType;
use App\Repository\VintageRepository;
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
 * @Route("/vintage", host="api.appart.do")
 */
class VintageController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_vintage_index"
   * )
   */
  public function index(VintageRepository $vintageRepository): View
  {
    $data = $vintageRepository->findAll();
    $vintages = [];
    foreach ( $data as $vintage ) {
      array_push($vintages, $this->normalize($vintage));
    }
    return View::create($vintages, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_vintage_show"
   * )
   */
  public function show(Vintage $vintage, WineRepository $wineRepository, WineController $wineController) : View
  {
    $vintage = $this->normalize($vintage);
    return View::create($vintage, Response::HTTP_OK);

  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_vintage_create"
   * )
   */
  public function create(Request $request, VintageRepository $vintageRepository): View
  {
      $vintage_year = $request->get('vintageYear');

      $vintage  = new Vintage();
      $vintage->setVintageYear($vintage_year);
      if($vintageRepository->findBy(array('vintage_year' => $vintage_year))) {
        return View::create('L\'élément éxiste déjà', Response::HTTP_EXPECTATION_FAILED);
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($vintage);
      $em->flush();

      $color_name = $this->normalize($vintage);
      return View::create($vintage, Response::HTTP_CREATED);


  }

  /**
   * @Rest\Put(
   *   path="/admin/{id}/edit",
   *   name="api_vintage_edit"
   * )
   */
  public function edit(Request $request, Vintage $vintage) : View
  {

      $em = $this->getDoctrine()->getManager();

      $vintage_year = $request->get('vintageYear');
      $vintage->setVintageYear($vintage_year);

      $em->persist($vintage);
      $em->flush();

      $vintage = $this->normalize($vintage);
      return View::create($vintage, Response::HTTP_CREATED);


  }

  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_vintage_delete"
   * )
   */
  public function delete(Vintage $vintage): View
  {

      $em = $this->getDoctrine()->getManager();
      $em->remove($vintage);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);


  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'vintageYear',
        'wines' => [
          'id',
          'wineName',
          'winePrice',
          'status' => ['id', 'statusName'],
          'category' => ['id', 'categoryName', 'categoryOrder'],
          'designation' => ['id', 'designationName'],
          'color' => ['id', 'colorName', 'colorOrder'],
          'label' => ['id', 'labelName']
        ]
      ]]);
    return $object;
  }
}
