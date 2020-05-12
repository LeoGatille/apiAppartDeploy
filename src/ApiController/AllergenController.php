<?php

namespace App\ApiController;

use App\Entity\Allergen;
use App\Form\AllergenType;
use App\Repository\AllergenRepository;
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
 * @Route("/allergen", host="api.bundles.do")
 */
class AllergenController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_allergen_index"
   * )
   */
  public function index(AllergenRepository $allergenRepository): View
  {
    $data = $allergenRepository->findAll();
    $allergens = [];
    foreach ( $data as $allergen) {
      array_push($allergens, $this->normalize($allergen));
    }
    return View::create($allergens, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   *   path="/{id}",
   *   name="api_allergen_show"
   * )
   */
    public function show(Allergen $allergen) : View
    {
      $allergen= $this->normalize($allergen);
      return View::create($allergen, Response::HTTP_OK);
    }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_allergen_create"
   * )
   */
    public function create(Request $request, AllergenRepository $allergenRepository): View
    {
    
        $allergen = new Allergen();

        $allergen_name = $request->get('allergenName');
        if (isset($allergen_name) && !empty($allergen_name)) {
          $allergen->setAllergenName($allergen_name);
          if($allergenRepository->findBy(array('allergen_name' => $allergen_name))) {
            return View::create('L\'élément éxiste déjà', Response::HTTP_EXPECTATION_FAILED);
          }
        } else {
          return View::create('le nom saisit est invalide', Response::HTTP_EXPECTATION_FAILED);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($allergen);
        $em->flush();

        $allergen = $this->normalize($allergen);
        return View::create($allergen, Response::HTTP_CREATED);
     


    }

    /**
   * @Rest\Put(
   *   path="/admin/{id}/edit",
   *   name="api_allergen_edit"
   * )
   */
  public function edit(Request $request, Allergen $allergen) : View
  {
    $em = $this->getDoctrine()->getManager();

    $allergen_name = $request->get('allergenName');
    $allergen->setAllergenName($allergen_name);

    $em->persist($allergen);
    $em->flush();

    $label = $this->normalize($allergen);
    return View::create($allergen, Response::HTTP_CREATED);
  }
  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_allergen_delete"
   * )
   */
  public function delete(Allergen $allergen): View
  {
  
      $em = $this->getDoctrine()->getManager();
      $em->remove($allergen);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);
    

  }

    private function normalize($object)
    {
      $serializer = new Serializer([new ObjectNormalizer()]);
      $object = $serializer->normalize($object, 'json',
        ['attributes' => [
          'id',
          'allergenName',
          'foods' => [
            'id',
            'foodName'
          ]
        ]]);
      return $object;
    }
}
