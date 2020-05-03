<?php

namespace App\ApiController;

use App\Entity\Food;
use App\Entity\Image;
use App\Repository\FoodRepository;
use App\Repository\ImageRepository;
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
use Symfony\Component\HttpFoundation\File\Exception\FileException;


/**
 * @Route("/image", host="api.appart.do")
 */
class ImageController extends AbstractFOSRestController
{

  /**
   * @Rest\Get(
   *   path="/",
   *   name="api_image_index"
   * )
   */
  public function index(ImageRepository $imageRepository): View
  {
    $data = $imageRepository->findAll();
    $images = [];
    foreach ($data as $image) {
      array_push($images, $this->normalize($image));
    }
    return View::create($images, Response::HTTP_OK);
  }

  /**
   * @Rest\Post(
   *   path="/admin/create",
   *   name="api_image_create"
   * )
   */
  public function create(Request $request): View
  {
      $file = $request->files->get('image');
      //$file = $dataFile['file'];
      $dataOther = $request->get('alternative');
      $alternative = $dataOther;
      if ($file) {
        $image = new Image();
        $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

        try {
          $file->move(
            $this->getParameter('image_abs_path'),
            $fileName
          );
        } catch (FileException $e) {

        }
        $image->setPath($this->getParameter('image_abs_path').'/'.$fileName);
        $image->setImgPath($this->getParameter('image_path').'/'.$fileName);
        $image->setAlternative($alternative);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($image);
        $entityManager->flush();

        return View::create($image, Response::HTTP_CREATED);
      } else {
        return View::create('failed to create file', Response::HTTP_EXPECTATION_FAILED);
      }


  }

  /**
   * @Rest\Delete(
   *     path="/admin/{id}/delete",
   *     name="api_image_delete"
   * )
   */
  public function delete(Image $image): View
  {
      $em = $this->getDoctrine()->getManager();
      $this->removeFile($image->getPath());
      $em->remove($image);
      $em->flush();

      return View::create(array(), Response::HTTP_OK);

  }

  /**
   * @return string
   */
  private function generateUniqueFileName()
  {
    // md5() reduces the similarity of the file names generated by
    // uniqid(), which is based on timestamps
    return md5(uniqid());
  }

  private function removeFile($path)
  {
    if(file_exists($path))
    {
      unlink($path);
    }
  }

  private function normalize($object)
  {
    $serializer = new Serializer([new ObjectNormalizer()]);
    $object = $serializer->normalize($object, 'json',
      ['attributes' => [
        'id',
        'path',
        'imgPath',
        'alternative'
      ]]);
    return $object;
  }
}