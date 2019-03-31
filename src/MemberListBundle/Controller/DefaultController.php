<?php

namespace MemberListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MemberListBundle\Entity\Regist;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;




class DefaultController extends Controller
{
    /**
     * @Route("/", name="new_regist", methods={"GET","POST"})
     */
    public function indexAction(Request $request)
    {
      $regist = new Regist();

      $regist-> setCreatedAt(new \DateTime());
      $regist-> setUpdatedAt(new \DateTime());

      $form = $this->createFormBuilder($regist)
        ->add('name', TextType::class)
        ->add('furigana', TextType::class)
        ->add('email',  EmailType::class)
        ->add('github', EmailType::class)
        ->add('createdAt', DateType::class)
        ->add('updatedAt', DateType::class)
        ->add('save', SubmitType::class, array('label' => 'Add Profile'))
        ->getForm();

      $form->handleRequest($request);

      $message = "member登録画面";

      if($form->isSubmitted() && $form->isValid())
      {

        $message="登録完了";
        $data = $form->getData();

        $regist = new Regist();
        $regist -> setName($form->get('name')->getData());
        $regist -> setFurigana($form->get('furigana')->getData());
        $regist -> setEmail($form->get('email')->getData());
        $regist -> setGithub($form->get('github')->getData());
        $regist -> setCreatedAt(new \DateTime());
        $regist -> setUpdatedAt(new \DateTime());

        $em =$this->getDoctrine()->getManager();
        $em->persist($regist);
        $em->flush();

        return
          $this->redirect( '/show' );
//        new Response('Saved new product with id '.$product->getId());
      }

        return $this->render('MemberListBundle:Default:index.html.twig',
          array(
            'form' => $form->createView(),
            'message' => $message,));
    }

  /**
   * @Route("/show", name="show_regist", methods={"GET","POST"})
   */
  public function showAction(Request $request)
  {

    $regist = new Regist();

    $form = $this->createFormBuilder($regist)
//      ->add('id', IntegerType::class)
      ->add('email',  EmailType::class)
      ->add('serch', SubmitType::class, array('label' => 'serch'))
      ->getForm();

    $memberList = $this->getDoctrine()->getManager()
      ->getRepository(Regist::class)
      ->findAll();

    $message = "";
    return $this->render('MemberListBundle:Default:show.html.twig',
      array(
        'form' => $form->createView(),
        'memberlist' => $memberList,
        'msg' => $message)
    );
  }

  /**
   * @Route("/serch", name="serch_regist", methods={"GET","POST"})
   */
  public function serchAction(Request $request)
  {

    $regist = new Regist();

    $form = $this->createFormBuilder($regist)
//      ->add('id', IntegerType::class)
      ->add('email', EmailType::class)
      ->add('serch', SubmitType::class, array('label' => 'serch'))
      ->getForm();

    $memberList = [];

    $form->handleRequest($request);

    $memberList = $this->getDoctrine()->getManager()
      ->getRepository(Regist::class)
      ->findBy(
        array(
          'email' => $regist->getEmail()
        ));


    return $this->render('MemberListBundle:Default:serch.html.twig',
      array(
        'form' => $form->createView(),
      ));
  }

  /**
   * @Route("/user", name="user_regist", methods={"GET","POST"})
   */
  public function userAction(Request $request)
  {

    $regist = new Regist();

    $form = $this->createFormBuilder($regist)
      ->add('id', IntegerType::class)
      ->add('email', EmailType::class)
      ->add('serch', SubmitType::class, array('label' => 'delete'))
      ->getForm();

    $memberList = [];

    $form->handleRequest($request);

    $memberList = $this->getDoctrine()->getManager()
      ->getRepository(Regist::class)
      ->findBy(
        array(
          'email' => $regist->getEmail()
        ));


      return $this->render('MemberListBundle:Default:user.html.twig',
        array(
          'form' => $form->createView(),
          'memberlist' => $memberList

        ));
    }
//  }

  /**
   * @Route("/delete", name="delete_regist", methods={"GET","POST","DELETE"})
   */
  public function deleteAction(Request $request)
  {

    $regist = new Regist();

//    $form = $this->createFormBuilder($regist)
//      ->add('id', IntegerType::class)
//      ->add('email', EmailType::class)
//      ->add('serch', SubmitType::class, array('label' => 'serch'))
//      ->getForm();

    $memberList = [];

//    $form->handleRequest($request);



    $em = $this->getDoctrine()->getManager();

    $del_items=  $this->getDoctrine()
      ->getRepository(Regist::class)
      ->findBy(
        array(
          'email' => $regist->getEmail(),
          'id' => $regist->getId()

        ));
    foreach ($del_items as $del_item) {
      $em->remove($del_item);
    }
    $em  -> flush();

    $message ="登録情報削除しました(To delete your information is Suscessfully.)";



    return $this->render('MemberListBundle:Default:delete.html.twig',
      array(
//        'form' => $form->createView(),
        'memberlist' => $del_items,
        'message' => $message


      ));


  }
}
