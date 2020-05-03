<?php

namespace App\ApiController;

use App\Entity\Gallery;
use App\Entity\Picture;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use App\Repository\PictureRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/picture", host="api.appart.do")
 */
class PictureController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_picture_index"
   * )
   */
  public function index(PictureRepository $pictureRepository): View
  {
    $data = $pictureRepository->findAll();
    $pictures = [];
    foreach ($data as $picture) {
      array_push($pictures, $this->normalize($picture));
    }
    return View::create($pictures, Response::HTTP_OK);
  }

  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_picture_show"
   * )
   */
  public function show(Picture $picture) : View
  {
    $picture = $this->normalize($picture);
    return View::create($picture, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/create",
   *   name="api_picture_create"
   * )
   */
  public function create(Request $request): View
  {
    $picture = new Picture();
    $em = $this->getDoctrine()->getManager();

    $picture_name = $request->get('pictureName');
    $picture->setPictureName($picture_name);

    $picture_url = $request->get('pictureUrl');
    $picture->setPictureUrl($picture_url);

    $picture_atl = $request->get('pictureAlt');
    $picture->setPictureAlt($picture_atl);

    $em->persist($picture);
    $em->flush();

    $picture = $this->normalize($picture);
    return View::create($picture, Response::HTTP_CREATED);


  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'pictureName',
        'pictureUrl',
        'pictureAlt',
        'galleries' => [
          'id',
          'galleryName',
        ]
      ]]);
    return $object;
  }
}
