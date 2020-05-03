<?php

namespace App\ApiController;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
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
use App\Service;

/**
 * @Route("/category", host="api.appart.do")
 */
class CategroyController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_category_index"
   * )
   */
  public function index(CategoryRepository $categoryRepository): View
  {
    $data = $categoryRepository->findAll();
    $categories = [];
    foreach ( $data as $category ) {
      array_push($categories, $this->normalize($category));
    }
    return View::create($categories, Response::HTTP_OK);
  }


  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_category_show"
   * )
   */
  public function show(Category $category, WineRepository $wineRepository, WineController $wineController) : View
  {
    $category = $this->normalize($category);


    return View::create($category, Response::HTTP_OK);

  }

  /**
   * @Rest\Put(
   *   path="/admin/{id}/edit",
   *   name="api_category_edit"
   * )
   */
  public function edit(Request $request, Category $category ) : View
  {
    
      $em = $this->getDoctrine()->getManager();

      $category_name = $request->get('categoryName');
      if (isset($category_name) && !empty($category_name)) {
        $category->setCategoryName($category_name);

      } else {
        return View::create('le nom saisit est invalide', Response::HTTP_EXPECTATION_FAILED);
      }

      $em->persist($category);
      $em->flush();

      $category = $this->normalize($category);
      return View::create($category, Response::HTTP_CREATED);
   


  }





  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_category_create"
   * )
   */
  public function create(Request $request, CategoryRepository $categoryRepository): View
  {
   
      $category  = new Category;

      $category_name = $request->get('categoryName');
      if (isset($category_name) && !empty($category_name)) {
        $category->setCategoryName($category_name);
        if($categoryRepository->findBy(array('category_name' => $category_name))) {
            return View::create('L\'élément éxiste déjà', Response::HTTP_EXPECTATION_FAILED);
        }
      } else {
        return View::create('le nom saisit est invalid', Response::HTTP_EXPECTATION_FAILED);
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($category);
      $em->flush();

      $category = $this->normalize($category);
      return View::create($category, Response::HTTP_CREATED);
  


  }

  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_category_delete"
   * )
   */
  public function delete(Category $category): View
  {
  
      $em = $this->getDoctrine()->getManager();
      $em->remove($category);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);
  

  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'categoryName',
        'categoryOrder',
        'wines' => [
          'id',
          'wineName',
          'winePrice',
          'status' => ['id', 'statusName'],
          'color' => ['id', 'colorName', 'colorOrder'],
          'designation' => ['id', 'designationName'],
          'vintage' => ['id', 'vintageYear'],
          'label' => ['id', 'labelName']
        ]
      ]]);
    return $object;
  }

}
