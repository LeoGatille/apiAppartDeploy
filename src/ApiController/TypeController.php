<?php

namespace App\ApiController;

use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use App\Repository\FoodRepository;
use App\Repository\FormulaRepository;
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
 * @Route("/type", host="api.appart.do")
 */
class TypeController extends AbstractFOSRestController
{

  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_food_index"
   * )
   */
  public function index(TypeRepository $typeRepository): View
  {
    $data = $typeRepository->findAll();
    $types = [];
    foreach ( $data as $type ) {
      array_push($types, $this->normalize($type));
    }
    return View::create($types, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_type_show"
   * )
   */
  public function show(Type $type) : View
  {
    $type = $this->normalize($type);
    return View::create($type, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/create",
   *   name="api_type_create"
   * )
   */
  public function create(Request $request): View
  {
    if ($this->getUser()) {
      $type = new Type();
      $em = $this->getDoctrine()->getManager();

      $type_name = $request->get('typeName');
      $type->setTypeName($type_name);

      $em->persist($type);
      $em->flush();

      $type = $this->normalize($type);
      return View::create($type, Response::HTTP_CREATED);
    } else {
      return View::create('forbidden acces', Response::HTTP_FORBIDDEN);
    }

  }

  /**
   * @Rest\Put(
   *   path="/{id}/edit",
   *   name="api_type_edit"
   * )
   */
  public function edit(Request $request,Type $type): View
  {
    if ($this->getUser()) {
      $em = $this->getDoctrine()->getManager();

      $type_name = $request->get('typeName');
      $type->setTypeName($type_name);

      $em->persist($type);
      $em->flush();

      $type = $this->normalize($type);
      return View::create($type, Response::HTTP_CREATED);
    } else {
      return View::create('forbidden acces', Response::HTTP_FORBIDDEN);
    }

  }

  /**
   * @Rest\Delete(
   *     path="/{id}/delete",
   *     name="api_type_delete"
   * )
   */
  public function delete(Type $type): View
  {
    if ($this->getUser()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($type);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);
    } else {
      return View::create('forbidden acces', Response::HTTP_FORBIDDEN);
    }

  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'typeName',
        'foods' => [
          'id',
          'foodName',
          'foodDescription',
          'display',
          'allergen' => [
            'id',
            'allergenName'
          ]
        ]
      ]]);
    return $object;
  }
}
