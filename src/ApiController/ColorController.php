<?php

namespace App\ApiController;

use App\Entity\Color;
use App\Entity\Gallery;
use App\Form\ColorType;
use App\Repository\ColorRepository;
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
 * @Route("/color", host="api.appart.do")
 */
class ColorController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_color_index"
   * )
   */
  public function index(ColorRepository $colorRepository): View
  {
    $data = $colorRepository->findAll();
    $colors = [];
    foreach ( $data as $color ) {
      array_push($colors, $this->normalize($color));
    }
    return View::create($colors, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_color_show"
   * )
   */
    public function show(Color $color, WineRepository $wineRepository, WineController $wineController) : View
  {
    $color = $this->normalize($color);


    return View::create($color, Response::HTTP_OK);

  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_color_create"
   * )
   */
  public function create(Request $request, ColorRepository $colorRepository): View
  {

      $color  = new Color;

      $color_name = $request->get('colorName');
      if (isset($color_name) && !empty($color_name)) {
        $color->setColorName($color_name);
        if($colorRepository -> findBy(array('color_name' => $color_name))) {
          return View::create('L\'élément éxiste déjà', Response::HTTP_EXPECTATION_FAILED);
        }

      $em = $this->getDoctrine()->getManager();
      $em->persist($color);
      $em->flush();

      $color_name = $this->normalize($color);
      return View::create($color, Response::HTTP_CREATED);
    } else {
      return View::create('forbidden acces', Response::HTTP_FORBIDDEN);
    }


  }

  /**
   * @Rest\Put(
   *   path="/admin/{id}/edit",
   *   name="api_color_edit"
   * )
   */
  public function edit(Request $request, Color $color) : View
  {
 
      $em = $this->getDoctrine()->getManager();

      $color_name = $request->get('colorName');
      if (isset($color_name) && !empty($color_name)) {
        $color->setColorName($color_name);
      } else {
        return View::create('le nom saisit est invalide', Response::HTTP_EXPECTATION_FAILED);
      }


      $em->persist($color);
      $em->flush();

      $color = $this->normalize($color);
      return View::create($color, Response::HTTP_CREATED);
    


  }

  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_color_delete"
   * )
   */
  public function delete(Color $color): View
  {
   
      $em = $this->getDoctrine()->getManager();
      $em->remove($color);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);
    

  }


//  private function getWinesNOTWORKING($object)
//  {
//    $wines_tab = [];
//    $tab_color = array($object);
//    $color_wines_tab = array_column($tab_color, "wines");
//    foreach ($color_wines_tab[0] as $value){
//     // $new_wine = $wineRepository->find($value);
//      $value = $value['id'];
//      $response = $this->forward('App\ApiController\WineController::getWinesById', [
//        'id' => $value
//      ]);
//      //$response = $this->normalizeWine($response);
//      array_push($wines_tab, $response);
//    }
//    return $wines_tab;
//  }

  private function getWines($object, WineRepository $wineRepository, WineController $wineController)
  {
    $wines_tab = [];
    $tab_color = array($object);
    $color_wines_tab = array_column($tab_color, "wines");
    foreach ($color_wines_tab[0] as $value){
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
        'colorName',
        'colorOrder',
        'wines' => [
          'id',
          'wineName',
          'winePrice',
          'status' => ['id', 'statusName'],
          'category' => ['id', 'categoryName', 'categoryOrder'],
          'designation' => ['id', 'designationName'],
          'vintage' => ['id', 'vintageYear'],
          'label' => ['id', 'labelName']
        ]
      ]]);
    return $object;
  }

}
