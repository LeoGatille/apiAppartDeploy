<?php

namespace App\ApiController;

use App\Entity\Gallery;
use App\Entity\Picture;
use App\Form\GalleryType;
use App\Repository\GalleryRepository;
use App\Repository\PictureRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/gallery", host="api.appart.do")
 */
class GalleryController extends AbstractFOSRestController
{
  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_gallery_index"
   * )
   */
  public function index(): View
  {
    $finder = new Finder();
    $finder->files()->in('/var/www/mesBundles/public/test');
    $pathTab = [];

    foreach ($finder as $file) {
      $absoluteFilePath = $file->getRealPath();
      array_push($pathTab, $this->normalizePath($absoluteFilePath));
    }
    return View::create($pathTab, Response::HTTP_OK);
  }



  /**
   * @Rest\Get(
   * path="/{id}",
   * name="api_gallery_show"
   * )
   */
  public function show(Gallery $gallery) : View
  {
    $gallery = $this->normalize($gallery);
    return View::create($gallery, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/create",
   *   name="api_gallery_create"
   * )
   */
  public function create(
    Request $request,
    PictureRepository $pictureRepository
  ): View
  {
    $gallery = new Gallery();
    $em = $this->getDoctrine()->getManager();

    $gallery_name = $request->get('galleryName');
    $gallery->setGalleryName($gallery_name);

    $pictures = $request->get('pictures');
    foreach ($pictures as $picturesId ){
      $picture = $pictureRepository->find($picturesId);
      $gallery->addPicture($picture);
      $em->persist($picture);
  }

    $em->persist($gallery);
    $em->flush();

    $gallery = $this->normalize($gallery);
    return View::create($gallery, Response::HTTP_CREATED);
  }

  /**
   * @Rest\Put(
   *   path="/{id}/edit",
   *   name="api_gallery_edit"
   * )
   */
   public function edit(Request $request, Gallery $gallery, PictureRepository $pictureRepository): View
   {
       $em = $this->getDoctrine()->getManager();

       $gallery_name = $request->get('galleryName');
       $gallery->setGalleryName($gallery_name);

       $reset = $request->get('reset');
       if ($reset === true){
         $old_pictures = $gallery->getPicture();
         foreach ($old_pictures as $key => $old_picture ){
           $gallery->removePicture($old_picture);
         }
       }

       $pictures = $request->get('pictures');
       foreach ($pictures as $picturesId ){
         $picture = $pictureRepository->find($picturesId);
         $gallery->addPicture($picture);
         $em->persist($picture);
       }

       $em->persist($gallery);
       $em->flush();

       $gallery = $this->normalize($gallery);
       return View::create($gallery, Response::HTTP_CREATED);
   }

  /**
   * @Rest\Delete(
   *     path="/{id}/delete",
   *     name="api_gallery_delete"
   * )
   */
  public function delete(Gallery $gallery): View
  {
    $em = $this->getDoctrine()->getManager();
    $em->remove($gallery);
    $em->flush();

    return View::create(array(), Response::HTTP_OK);
  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'galleryName',
        'picture' => [
          'id',
          'pictureUrl',
          'pictureName',
          'pictureAlt'
        ]
      ]]);
    return $object;
  }

  private function normalizePath($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'imagePath'
      ]]);
    return $object;
  }


}
