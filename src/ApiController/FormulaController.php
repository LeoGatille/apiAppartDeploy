<?php

namespace App\ApiController;

use App\Entity\Formula;
use App\Form\FormulaType;
use App\Repository\FormulaRepository;
use App\Repository\TypeRepository;
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
 * @Route("/formula", host="api.appart.do")
 */
class FormulaController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_formula_index"
   * )
   */
  public function index(FormulaRepository $formulaRepository): View
  {
    $data = $formulaRepository->findAll();
    $formulas = [];
    foreach ($data as $formula) {
      array_push($formulas, $this->normalize($formula));
    }
    return View::create($formulas, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   *   path="/{id}",
   *   name="api_formula_show"
   * )
   */
  public function show(Formula $formula): View
  {
    $formula = $this->normalize($formula);
    return View::create($formula, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_formula_create"
   * )
   */
  public function create(Request $request){
      $formula = new Formula();

      $em = $this->getDoctrine()->getManager();

      $formula_name = $request->get('formulaName');
      if (isset($formula_name) && !empty($formula_name)) {
        $formula->setFormulaName($formula_name);
      } else {
        return View::create('Le nom saisit est invalide', Response::HTTP_EXPECTATION_FAILED);
      }


      $formula_price = $request->get('formulaPrice');
      if (is_numeric($formula_price) && $formula_price > 0) {
        $formula->setFormulaPrice($formula_price);
      } else {
        return View::create('Le prix saisit est invalide', Response::HTTP_EXPECTATION_FAILED);
      }


      $formula_description = $request->get('description');
      $formula->setDescription($formula_description);

      $em->persist($formula);
      $em->flush();

      return View::create($formula, Response::HTTP_CREATED);
    

  }

  /**
   * @Rest\Put(
   *     path="/admin/{id}/edit",
   *     name="api_formula_edit"
   * )
   */
    public function edit(Formula $formula, Request $request, FormulaRepository $formulaRepository): View
  {


      $em = $this->getDoctrine()->getManager();

      $formula_name = $request->get('formulaName');
      if (isset($formula_name) && !empty($formula_name)) {
        $formula->setFormulaName($formula_name);



      $formula_price = $request->get('formulaPrice');
      if (is_numeric($formula_price) && $formula_price > 0) {
        $formula->setFormulaPrice($formula_price);
      } else {
        return View::create('Le prix saisit est invalide', Response::HTTP_EXPECTATION_FAILED);
      }


      $formula_description = $request->get('description');
      $formula->setDescription($formula_description);

      $em->persist($formula);
      $em->flush();

      return View::create($formula, Response::HTTP_CREATED);
    }else {
      return View::create('forbidden access', Response::HTTP_FORBIDDEN);
    }
  }

  /**
   * @Rest\Delete(
   *   path="/admin/{id}/delete",
   *   name="api_formula_delete"
   * )
   */
  public function delete(Formula $formula): View
  {
    if ($this->getUser()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($formula);
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
        'formulaName',
        'formulaPrice',
        'description'
      ]]);
    return $object;
  }
}
