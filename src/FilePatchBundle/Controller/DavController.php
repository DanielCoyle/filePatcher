<?php

namespace FilePatchBundle\Controller;
use FilePatchBundle\Entity\Dav;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * CRUD and test opperations for webDAV clients.
 */
class DavController extends Controller
{
   /**
    * List all webDAV clients (stores)
     * @Route("/dav", name="dav_list")
     */
    public function indexAction(){
       $davs = $this->getDoctrine()
                        ->getRepository("FilePatchBundle:Dav")
                        ->findAll();
        return $this->render('FilePatchBundle:Default:davList.html.twig', 
            array('davs'=> $davs)
            );
    } 

    /**
     * Create a new webDAV client
     * @Route("/dav/new", name="dav_new")
     */
    public function newAction(Request $request){
        $dav = new Dav;
        $form = $this->createFormBuilder($dav)
                    ->add("store", TextType::class,array("attr"=> array("class" => "form-control")))
                    ->add("davUrl", TextType::class,array("attr"=> array("class" => "form-control")))
                    ->add("userName", TextType::class,array("attr"=> array("class" => "form-control")))
                    ->add("password", TextType::class,array("attr"=> array("class" => "form-control")))
                    ->add("submit", SubmitType::class,array("label" => 'Submit', "attr"=> array("class" => "btn btn-primary")))
                    ->getForm();
                    
                    $form->handleRequest($request);
                    if($form->isSubmitted() && $form->isValid()){
                        $dav->setDavUrl($form['davUrl']->getData());
                        $dav->setStore($form['store']->getData());
                        $dav->setUserName($form['userName']->getData());
                        $dav->setPassword($form['password']->getData());
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($dav);
                        $em->flush();
                        $this->addFlash(
                            'notice',
                            "New Dav Added"
                            );
                        return $this->redirectToRoute("dav_list");
                    }
                    return $this->render('FilePatchBundle:Default:davNew.html.twig', array("form"=> $form->createView()));
    }
    /**
     * Edit an existing webDAV client
     * @param   $id the dav ID
     * @Route("/dav/edit/{id}", name="dav_edit")
     */
    public function editAction(Request $request ,$id){
        $dav = $this->getDoctrine()
                        ->getRepository("FilePatchBundle:Dav")
                        ->find($id);

          $form = $this->createFormBuilder($dav)
                    ->add("store", TextType::class,array("attr"=> array("class" => "form-control")))
                    ->add("davUrl", TextType::class,array("attr"=> array("class" => "form-control")))
                    ->add("userName", TextType::class,array("attr"=> array("class" => "form-control")))
                    ->add("password", TextType::class,array("attr"=> array("class" => "form-control")))
                    ->add("submit", SubmitType::class,array("label" => 'Submit', "attr"=> array("class" => "btn btn-primary")))
                    ->getForm();
                    
                    $form->handleRequest($request);
                    if($form->isSubmitted() && $form->isValid()){
                        //Get Data
                        $em = $this->getDoctrine()->getManager();
                        $dav = $em->getRepository("FilePatchBundle:Dav")->find($id);
                        $dav->setDavUrl($form['davUrl']->getData());
                        $dav->setStore($form['store']->getData());
                        $dav->setUserName($form['userName']->getData());
                        $dav->setPassword($form['password']->getData());

                        $em->flush();

                        $this->addFlash(
                            'notice',
                            "Patch Updated"
                            );
                        return $this->redirectToRoute("dav_list");

                    }


        return $this->render('FilePatchBundle:Default:davEdit.html.twig', array("dav" => $dav, "form"=> $form->createView()));
    }  

    /**
     * Delete an existing webDAV client
     * @param   $id the dav ID
     * @Route("/dav/delete/{id}", name="dav_delete")
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();
        $dav = $em->getRepository("FilePatchBundle:Dav")->find($id);
        $em->remove($dav);
        $em->flush();
        $this->addFlash("notice","Dav Removed");
        return $this->redirectToRoute("dav_list");
    }

     /**
     * Test to see if a webDAV client's credentials are correct by uploading a test file to the webDAV's content folder
     * @param   $id the dav ID 
     * @Route("/dav/test/{id}", name="dav_test")
     */
    public function testAction($id){
          $dav = $this->getDoctrine()
            ->getRepository("FilePatchBundle:Dav")
            ->find($id);
            $DAV = new Dav;
            $DAV->getId($id);
            $testFile = $DAV->downloadDirectory."content/test.txt";
            $f = fopen($testFile, "w");
            fwrite($f,"TEST");
            fclose($f);
            $upload = $DAV->davUpload($dav, "content/test.txt");
            unlink($testFile);
            $result = $DAV->davDownload($dav, "content/test.txt","content/test.txt");
            $upload = $DAV->davUpload($dav, "content/test.txt");
            
          return $this->render('FilePatchBundle:Default:davTest.html.twig', array("dav" => $dav,"content" => $result));
    }

}
