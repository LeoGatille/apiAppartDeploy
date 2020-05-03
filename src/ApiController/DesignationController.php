<?php

namespace App\ApiController;

use App\Entity\Designation;
use App\Form\DesignationType;
use App\Repository\DesignationRepository;
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
 * @Route("/designation", host="api.appart.do")
 */
class DesignationController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_designation_index"
   * )
   */
  public function index(DesignationRepository $designationRepository): View
  {
    $data = $designationRepository->findAll();
    $designations = [];
    foreach ( $data as $designation ) {
      array_push($designations, $this->normalize($designation));
    }
    return View::create($designations, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_designation_show"
   * )
   */
  public function show(Designation $designation, WineRepository $wineRepository, WineController $wineController) : View
  {
    $designation = $this->normalize($designation);
    return View::create($designation, Response::HTTP_OK);

  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_designation_create"
   * )
   */
  public function create(Request $request, DesignationRepository $designationRepository): View
  {
    
      $designation_name = $request->get('designationName');

      $designation  = new Designation;
      $designation->setDesignationName($designation_name);
      if($designationRepository -> findBy(array('designation_name' => $designation_name))) {
        return View::create('L\'élément éxiste déjà', Response::HTTP_EXPECTATION_FAILED); 
      }

      $em = $this->getDoctrine()->getManager();
      $em->persist($designation);
      $em->flush();

      $designation = $this->normalize($designation);
      return View::create($designation, Response::HTTP_CREATED);
    


  }

  /**
   * @Rest\Put(
   *     path="/admin/{id}/edit",
   *     name="api_designation_edit"
   * )
   */
  public function edit(Designation $designation, Request $request): View
  {
    
      $designation_name = $request->get('designationName');
      $designation->setDesignationName($designation_name);

      $em = $this->getDoctrine()->getManager();
      $em->persist($designation);
      $em->flush();

      $designation = $this->normalize($designation);
      return View::create($designation, Response::HTTP_OK);
    

  }


  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_designation_delete"
   * )
   */
  public function delete(Designation $designation): View
  {
    
      $em = $this->getDoctrine()->getManager();
      $em->remove($designation);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);
    

  }

  private function getWines($object, WineRepository $wineRepository, WineController $wineController)
  {
    $wines_tab = [];
    $tab_designation = array($object);
    $designation_wines_tab = array_column($tab_designation, "wines");
    foreach ($designation_wines_tab[0] as $value){
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
        'designationName',
        'wines' => [
          'id',
          'wineName',
          'winePrice',
          'status' => ['id', 'statusName'],
          'category' => ['id', 'categoryName'],
          'color' => ['id', 'colorName'],
          'vintage' => ['id', 'vintageYear'],
          'label' => ['id', 'labelName']
        ]
      ]]);
    return $object;
  }
}
