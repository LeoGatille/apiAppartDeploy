<?php

namespace App\ApiController;

use App\Entity\Label;
use App\Form\LabelType;
use App\Repository\LabelRepository;
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
 * @Route("/label", host="api.appart.do")
 */
class LabelController extends AbstractFOSRestController
{

  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_label_index"
   * )
   */
  public function index(LabelRepository $labelRepository): View
  {
    $data = $labelRepository->findAll();
    $labels = [];
    foreach ( $data as $label ) {
      array_push($labels, $this->normalize($label));
    }
    return View::create($labels, Response::HTTP_OK);
  }

//  /**
//   * @Rest\post(
//   * path="/find",
//   * name="api_label_find"
//   * )
//   */
//  public function find(LabelRepository $labelRepository, Request $request): View
//  {
//   $label_name = $request->get('labelName');
//   $label = $labelRepository->findBy([$label_name]);
//
//   return View::create($label, Response::HTTP_OK);
//  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_label_show"
   * )
   */
  public function show(Label $label, WineRepository $wineRepository, WineController $wineController) : View
  {
    $label = $this->normalize($label);
    return View::create($label, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_label_create"
   * )
   */
  public function create(Request $request, LabelRepository $labelRepository): View
  {

      $label_name = $request->get('labelName');

      $label  = new Label();
      $label->setLabelName($label_name);
      if($labelRepository -> findBy(array('label_name' => $label_name))) {
        return View::create('L\'élément éxiste déjà', Response::HTTP_EXPECTATION_FAILED);
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($label);
      $em->flush();

      $label_name = $this->normalize($label);
      return View::create($label, Response::HTTP_CREATED);



  }

  /**
   * @Rest\Put(
   *   path="/admin/{id}/edit",
   *   name="api_label_edit"
   * )
   */
  public function edit(Request $request, Label $label) : View
  {

      $em = $this->getDoctrine()->getManager();

      $label_name = $request->get('labelName');
      $label->setLabelName($label_name);

      $em->persist($label);
      $em->flush();

      $label = $this->normalize($label);
      return View::create($label, Response::HTTP_CREATED);



  }

  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_label_delete"
   * )
   */
  public function delete(Label $label): View
  {

      $em = $this->getDoctrine()->getManager();
      $em->remove($label);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);


  }

  private function getWines($object, WineRepository $wineRepository, WineController $wineController)
  {
    $wines_tab = [];
    $label_color = array($object);
    $label_wines_tab = array_column($label_color, "wines");
    foreach ($label_wines_tab[0] as $value){
      $value = $value['id'];
      $new_wine = $wineController->getWinesById($value, $wineRepository);
      array_push( $wines_tab, $new_wine);
    }
    return $wines_tab;
  }



  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'labelName',
        'wines' => [
          'id',
          'wineName',
          'winePrice',
          'status' => ['id', 'statusName'],
          'category' => ['id', 'categoryName', 'categoryOrder'],
          'designation' => ['id', 'designationName'],
          'vintage' => ['id', 'vintageYear'],
          'color' => ['id', 'colorName', 'colorOrder']
        ]
      ]]);
    return $object;
  }
}
