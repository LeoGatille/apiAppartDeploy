<?php

namespace App\ApiController;

use App\Entity\Food;
use App\Repository\FoodRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use App\Repository\TypeRepository;
use App\Repository\AllergenRepository;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/food", host="api.appart.do")
 */
class FoodController extends AbstractFOSRestController
{

  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_bonjour_index"
   * )
   */
  public function index(FoodRepository $foodRepository): View
  {
    $data = $foodRepository->findAll();
    $foods = [];
    foreach ( $data as $food ) {
      array_push($foods, $this->normalize($food));
    }
    return View::create($foods, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_food_show"
   * )
   */
  public function show(Food $food) : View
  {
    $food = $this->normalize($food);
    return View::create($food, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_food_create"
   * )
   */
  public function create(
    Request $request,
    TypeRepository $typeRepository,
    AllergenRepository $allergenRepository
  ): View
  {
    $food = new Food();
      $em = $this->getDoctrine()->getManager();

      $food_name = $request->get('foodName');
      if (isset($food_name) && !empty($food_name) && is_string($food_name) && strlen($food_name) < 256) {
        $food_name = htmlspecialchars($food_name);
        $food->setFoodName($food_name);
      } else {
        return View::create('le nom envoyé n\'est pas valide', Response::HTTP_EXPECTATION_FAILED);
      }
      $food_description = $request->get('foodDescription');
      if (isset($food_description) && !empty($food_description) && is_string($food_description)) {
        if(strlen($food_description) < 501) {
          $food->setFoodDescription($food_description);
        } else {
          return View::create('la description envoyé n\'est pas valide', Response::HTTP_EXPECTATION_FAILED);
        }
      }
      $food_display = $request->get('display');
      if ($food_display !== 1) {
        $food_display = 0;
      }
      $food->setDisplay($food_display);
      $typeId = $request->get('type');
      if (is_numeric($typeId)) {
        $type = $typeRepository->find($typeId);
      } else {
        return View::create('le type envoyé n\'est pas valide', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($type)) {
        $food->setType($type);
      } else {
        return View::create('le type envoyé n\'est pas valide', Response::HTTP_EXPECTATION_FAILED);
      }


      $allergens = $request->get('allergens');
      if ($allergens) {
        foreach ($allergens as $allergenId) {
          $allergen = $allergenRepository->find($allergenId);
          if(!is_null($allergen)) {
            $food->addAllergen($allergen);
            $em->persist($allergen);
          } else {
            return View::create('La liste d\'allergènes envoyée n\'est pas valide', Response::HTTP_EXPECTATION_FAILED);
          }
          
        }
      }


      $em->persist($food);
      $em->flush();

      $food = $this->normalize($food);
      return View::create($food, Response::HTTP_CREATED);

  }



  /**
   * @Rest\Put(
   *   path="/admin/{id}/edit",
   *   name="api_food_edit"
   * )
   */
  public function edit(
    Request $request,
    Food $food,
    TypeRepository $typeRepository,
    AllergenRepository $allergenRepository
): View
  {
   
      $em = $this->getDoctrine()->getManager();
      $food_name = $request->get('foodName');
      if (isset($food_name) && !empty($food_name) && is_string($food_name)) {
        $food->setFoodName($food_name);
      } else {
        return View::create('le nom envoyé n\'est pas valide', Response::HTTP_EXPECTATION_FAILED);
      }


      $food_description = $request->get('foodDescription');
      if (isset($food_description) && !empty($food_description)) {
        $food->setFoodDescription($food_description);
      }

      $food_display = $request->get('display');
      if ($food_display !== 1) {
        $food_display = 0;
      }
      $food->setDisplay($food_display);

      $typeId = $request->get('type');
      $type = $typeRepository->find($typeId);
      $food->setType($type);

//    $reset = $request->get('reset');
//    if ($reset === true){
//      $old_allergens = $food->getAllergen();
//      foreach ($old_allergens as $key => $old_allergen){
//        $food->removeAllergen($old_allergen);
//      }
//    }


      $allergens = $request->get('allergens');
      if ($allergens) {
        $old_allergens = $food->getAllergen();
        foreach ($old_allergens as $key => $old_allergen){
          $food->removeAllergen($old_allergen);
        };
        foreach ($allergens as $allergenId){
          $allergen = $allergenRepository->find($allergenId);
          $food->addAllergen($allergen);
          $em->persist($allergen);
        }
      } else {
        $old_allergens = $food->getAllergen();
        foreach ($old_allergens as $key => $old_allergen){
          $food->removeAllergen($old_allergen);
        };
      }

      $em->persist($food);
      $em->flush();

      $food = $this->normalize($food);
      return View::create($food, Response::HTTP_CREATED);
    

  }




  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_food_delete"
   * )
   */
  public function delete(Food $food): View
  {
   
      $em = $this->getDoctrine()->getManager();
      $em->remove($food);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);
   

  }

  /**
   * Patch a Food
   * @Rest\Patch(
   *     path = "/admin/{id}/patch",
   *     name = "api_patch_food",
   * )
   * @Rest\View()
   * @param Request $request
   * @param Food $food
   * @return View;
   */
  public function patch(Request $request, Food $food): View
  {

      $em = $this->getDoctrine()->getManager();

      $display = $request->get('display');
      if ($display !== 1) {
        $display = 0;
      }
      $food->setDisplay($display);

      $em->persist($food);
      $em->flush();

      $food = $this->normalize($food);
      return View::create($food, Response::HTTP_OK);
    

  }



  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'foodName',
        'foodDescription',
        'display',
        'type' => [
          'id',
          'typeName'
        ],
        'allergen' => [
          'id',
          'allergenName'
        ],
        'event' => [
          'id',
          'eventName'
        ]
      ]]);
    return $object;
  }

}
