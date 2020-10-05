<?php

namespace App\ApiController;

use App\Entity\Wine;
use App\Form\ColorType;
use App\Repository\CategoryRepository;
use App\Repository\ColorRepository;
use App\Repository\DesignationRepository;
use App\Repository\LabelRepository;
use App\Repository\StatusRepository;
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
 * @Route("/wine", host="api.appart.do")
 */
class WineController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_wine_index"
   * )
   */
  public function index(WineRepository $wineRepository): View
  {
    $data = $wineRepository->findAll();
    $wines = [];
    foreach ( $data as $wine ) {
      array_push($wines, $this->normalize($wine));
    }
    return View::create($wines, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_wine_show"
   * )
   */
  public function show(Wine $wine, WineRepository $wineRepository) : View
  {

    $wine = $this->normalize($wine);
    return View::create($wine, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_wine_create"
   * )
   */
  public function create(
    Request $request,
    CategoryRepository $categoryRepository,
    DesignationRepository $designationRepository,
    ColorRepository $colorRepository,
    LabelRepository $labelRepository,
    VintageRepository $vintageRepository,
    StatusRepository $statusRepository
  ): View
  {

      $wine = new Wine();
      $em = $this->getDoctrine()->getManager();

      $wine_name = $request->get('wineName');
      if(isset($wine_name) && !empty($wine_name) && is_string($wine_name) && strlen($wine_name) < 256){
        $wine->setWineName($wine_name);
      } else {
        return View::create('la valeur evoyee pour le Nom du vin est incorrecte', Response::HTTP_EXPECTATION_FAILED);
      }


      $wine_price = $request->get('winePrice');
      if (isset($wine_price) && !empty($wine_price) && is_numeric($wine_price) && $wine_price > 0) {
        $wine->setWinePrice($wine_price);
      } else {
        return View::create('la valeur envoyee pour le prix du vin est incorrecte', Response::HTTP_EXPECTATION_FAILED);
      }

      $statusId = $request->get('status');
      if (isset($statusId) && !empty($statusId) && is_numeric($statusId)) {
        $status = $statusRepository->find($statusId);
      } else {
        return View::create('le statut selectionné n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($status)) {
        $wine->setStatus($status);
      } else {
        return View::create('le statut selectione n\'existe pas', Response::HTTP_EXPECTATION_FAILED );
      }

      $categoryId = $request->get('category');
      if (isset($categoryId) && !empty($categoryId) && is_numeric($categoryId)) {
        $category = $categoryRepository->find($categoryId);
      } else {
        return View::create('la catégorie selectionnée n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($category)) {
        $wine->setCategory($category);
      } else {
        return View::create('la catégorie selectionnee n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }

      $designationId = $request->get('designation');
      if (isset($designationId) && !empty($designationId) && is_numeric($designationId)) {
        $designation = $designationRepository->find($designationId);
      } else {
        return View::create('l\'appelation selectionée n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($designation)) {
        $wine->setDesignation($designation);
      } else {
        return View::create('l\'appelation selectionnee n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }

      $colorId = $request->get('color');
      if (isset($colorId) && !empty($colorId) && is_numeric($colorId)) {
        $color = $colorRepository->find($colorId);
      } else {
        return View::create('la couleur selectionnée n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($color)) {
        $wine->setColor($color);
      } else {
        return View::create('la couleur selectionnée n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }

      $labelId = $request->get('label');
      if (isset($labelId) && !empty($labelId) && is_numeric($labelId)) {
        $label = $labelRepository->find($labelId);
      } else {
        return View::create('le label selectionnée n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($label)) {
        $wine->setLabel($label);
      } else {
        return View::create('le label selectionne n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }

      $vintageId = $request->get('vintage');
      if (isset($vintageId) && !empty($vintageId) && is_numeric($vintageId)) {
        $vintage = $vintageRepository->find($vintageId);
      } else {
        return View::create('le millésime selectionnée n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($vintage)) {
        $wine->setVintage($vintage);
      } else {
        return View::create('le millésime selectionnée n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }

      $em->persist($wine);
      $em->flush();

      $wine = $this->normalize($wine);
      return View::create($wine, Response::HTTP_CREATED);


  }

  /**
   * @Rest\Put(
   *   path="/admin/{id}/edit",
   *   name="api_wine_edit"
   * )
   */
  public function edit(
    Request $request,
    Wine $wine,
    CategoryRepository $categoryRepository,
    DesignationRepository $designationRepository,
    ColorRepository $colorRepository,
    LabelRepository $labelRepository,
    VintageRepository $vintageRepository,
    StatusRepository $statusRepository
  ): View
  {
      $wine_name = $request->get('wineName');
      if (isset($wine_name) && !empty($wine_name)) {
        $wine->setWineName($wine_name);
      } else {
        return View::create('le nom envoye n\'est pas valide', Response::HTTP_EXPECTATION_FAILED);
      }

      $wine_price = $request->get('winePrice');
      if (is_numeric($wine_price) && $wine_price > 0) {
        $wine->setWinePrice($wine_price);
      } else {
        return View::create('le prix envoyé n\'est pas valide', Response::HTTP_EXPECTATION_FAILED);
      }

      $statusId = $request->get('status');
      if (is_numeric($statusId)) {
        $status = $statusRepository->find($statusId);
      } else {
        return View::create('le statut selectionne n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($status)) {
        $wine->setStatus($status);
      } else {
        return View::create('le statut selectionne n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }


      $categoryId = $request->get('category');
      if (is_numeric($categoryId)) {
        $category = $categoryRepository->find($categoryId);
      } else {
        return View::create('le categorie selectionne n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($category)) {
        $wine->setCategory($category);
      } else {
        return View::create('la categorie selectionnee n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }


      $designationId = $request->get('designation');
      if (is_numeric($designationId)) {
        $designation = $designationRepository->find($designationId);
      } else {
        return View::create('l\'appellation selectionnee n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($designation)) {
        $wine->setDesignation($designation);
      } else {
        return View::create('l\'appellation selectionnee n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }


      $colorId = $request->get('color');
      if (is_numeric($colorId)) {
        $color = $colorRepository->find($colorId);
      } else {
        return View::create('la couleur selectionnee n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }
      if (!is_null($color)) {
        $wine->setColor($color);
      } else {
        return View::create('la couleur selectionnee n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }


      $labelId = $request->get('label');
      if (is_numeric($labelId)) {
        $label = $labelRepository->find($labelId);
      } else {
        return View::create('le label selectionne n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }

      if (!is_null($label)) {
        $wine->setLabel($label);
      } else {
        return View::create('le label selectionne n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }


      $vintageId = $request->get('vintage');
      if (is_numeric($vintageId)) {
        $vintage = $vintageRepository->find($vintageId);
      } else {
        return View::create('le millésime selectionne n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }

      if (!is_null($vintage)) {
        $wine->setVintage($vintage);
      } else {
        return View::create('le millésime selectionne n\'existe pas', Response::HTTP_EXPECTATION_FAILED);
      }


      $em = $this->getDoctrine()->getManager();
      $em->persist($wine);
      $em->flush();

      $wine = $this->normalize($wine);
      return View::create($wine, Response::HTTP_CREATED);


  }

  /**
   * Patch a Wine
   * @Rest\Patch(
   *     path = "/admin/{id}/patch",
   *     name = "api_patch_status_wine",
   * )
   * @Rest\View()
   * @param Request $request
   * @param Wine $wine
   * @param StatusRepository $statusRepository
   * @return View;
   */
  public function patch(Request $request, Wine $wine, StatusRepository $statusRepository): View
  {
      $em = $this->getDoctrine()->getManager();      $statusId = $request->get('status');
      $status = $statusRepository->find($statusId);
      if (!is_null($status)) {
        $wine->setStatus($status);
      } else {
        return View::create(
          'le statut selectionne n\'existe pas', 
          Response::HTTP_EXPECTATION_FAILED
        );
      }
      $em->persist($wine);
      $em->flush();
      $wine = $this->normalize($wine);
      return View::create($wine, Response::HTTP_CREATED);
  }

  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_wine_delete"
   * )
   */
  public function delete(Wine $wine): View
  {

      $em = $this->getDoctrine()->getManager();
      $em->remove($wine);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);


  }


  public function getWinesById($id, WineRepository $wineRepository){
    $wine = $wineRepository->find($id);
    $wine = $this->normalize($wine);
    return $wine;
  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'category' => ['id', 'categoryName', 'categoryOrder'],
        'designation' => ['id', 'designationName'],
        'color' => ['id', 'colorName', 'colorOrder'],
        'label' => ['id', 'labelName'],
        'wineName',
        'winePrice',
        'status' => ['id', 'statusName'],
        'vintage' =>['id', 'vintageYear']
      ]]);
    return $object;
  }
}
