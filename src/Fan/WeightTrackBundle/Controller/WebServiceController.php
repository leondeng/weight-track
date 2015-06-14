<?php

namespace Fan\WeightTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Symfony\Component\HttpFoundation;
use Fan\WeightTrackBundle\Entity\User;
use Fan\WeightTrackBundle\Entity\Goal;
use Fan\WeightTrackBundle\Entity\Track;

class WebServiceController extends Controller implements TransactionWrapController
{
  const ERROR_CODE_BASE = 900;

  public function needsRollback() {
    return 'test' == $this->container->get( 'kernel' )->getEnvironment();
  }

  private function saveEntity($object) {
    $this->getDoctrine()->getManager()->persist($object);
    $this->getDoctrine()->getManager()->flush();
  }

  public function createGoalAction(Request $request) {
    try {
      $em = $this->getDoctrine()->getManager();
      if ($user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find($request->request->get('id'))) {
        $data = json_decode($request->getContent(), true);
        $goal = new Goal();
        $goal->setGoal($data['goal']);
        $goal->setUser($user);

        $validator = $this->get('validator');
        $errors = $validator->validate($goal);
        if (count($errors) > 0) {
          $error = $errors->get(0);
          throw new \Exception($error->getMessage(), self::ERROR_CODE_BASE + 1);
        }
        $this->saveEntity($goal);

        return $this->responseJson($this->serialize($goal));
      } else {
        return $this->err404('User not found!');
      }
    } catch ( \Exception $e ) {
      return $this->err500($e);
    }
  }

  public function createTrackAction(Request $request) {
  try {
      $em = $this->getDoctrine()->getManager();
      if ($user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find($request->request->get('id'))) {
        $data = json_decode($request->getContent(), true);
        $track = new Track();
        $track->setWeight($data['weight']);
        $track->setDate(date_create($data['date']));
        $track->setUser($user);

        $validator = $this->get('validator');
        $errors = $validator->validate($track);
        if (count($errors) > 0) {
          $error = $errors->get(0);
          throw new \Exception($error->getMessage(), self::ERROR_CODE_BASE + 2);
        }
        $this->saveEntity($track);

        return $this->responseJson($this->serialize($track));
      } else {
        return $this->err404('User not found!');
      }
    } catch ( \Exception $e ) {
      return $this->err500($e);
    }
  }

  public function listTracksAction(Request $request) {
    try {
      $em = $this->getDoctrine()->getManager();
      if ($user = $em->getRepository('Fan\WeightTrackBundle\Entity\User')->find($request->query->get('id'))) {
        $tracks = $user->getTracks();
        return $this->responseJson($this->serialize($tracks));
      } else {
        return $this->err404('User not found!');
     }
    } catch (\Exception $e) {
      return $this->err500($e);
    }
  }

  public function updateTrackAction(Request $request) {
    try {
      $userId = $request->request->get('id');
      $date = date_create(date('Y-m-d', $request->request->get('date')));

      $data = json_decode($request->getContent(), true);

      $em = $this->getDoctrine()->getManager();
      if ($track = $em->getRepository('Fan\WeightTrackBundle\Entity\Track')->findOneBy(array('user' => $userId, 'date' => $date))) {
        $track->setWeight($data['weight']);
        $track->setDate($date);

        $validator = $this->get('validator');
        $errors = $validator->validate($track);
        if (count($errors) > 0) {
          $error = $errors->get(0);
          throw new \Exception($error->getMessage(), self::ERROR_CODE_BASE + 2);
        }
        $this->saveEntity($track);

        return $this->responseJson($this->serialize($track));
     } else {
        return $this->err404('Track not found!');
      }
    } catch ( \Exception $e ) {
      return $this->err500($e);
    }
  }

  public function deleteTrackAction(Request $request) {
    try {
      $userId = $request->request->get('id');
      $date = date_create(date('Y-m-d', $request->request->get('date')));

      $em = $this->getDoctrine()->getManager();
      if ($track = $em->getRepository('Fan\WeightTrackBundle\Entity\Track')->findOneBy(array('user' => $userId, 'date' => $date))) {
        $em->remove($track);
        $em->flush();

        return new JsonResponse(array('status' => 'ok'));
      } else {
        return $this->err404('Track not found!');
     }
    } catch (\Exception $e) {
      return $this->err500($e);
    }

  }

  private function serialize($object) {
    $serializer = $this->container->get('jms_serializer');
    return $serializer->serialize($object, 'json');
  }

  private function responseJson($data) {
    return new Response($data, 200, array('Content-Type' => 'application/json'));
  }

  private function err400($msg) {
    return new JsonResponse(array('message' => $msg), 400);
  }

  private function err401($msg) {
    return new JsonResponse(array('message' => $msg), 401);
  }

  private function err404($msg) {
    return new JsonResponse(array('message' => $msg), 404);
  }

  private function err500(\Exception $e) {
    $data = array (
      'error_code' => $e->getCode(),
      'message' => $e->getMessage()
    );

    return new JsonResponse($data, 500);
  }
}
