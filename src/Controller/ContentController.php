<?php

namespace App\Controller;

use App\Entity\Content;
use App\Form\ContentType;
use App\Repository\ActRepository;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/content")
 */
class ContentController extends AbstractController
{

    /**
     * @var ActRepository
     */
    private $actRepository;

    /**
     * @var ContentRepository
     */
    private $contentRepository;

    private $security;

    public function __construct(ContentRepository $contentRepository, ActRepository $actRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->actRepository = $actRepository;
        $this->contentRepository = $contentRepository;
        $this->security = $security;

    }

    /**
     * @Route("/", name="content_index", methods={"GET"})
     * @param ContentRepository $contentRepository
     * @return Response
     */
    public function index(ContentRepository $contentRepository): Response
    {
        return $this->render('content/index.html.twig', [
            'contents' => $contentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="content_new", methods={"GET","POST"})
     */
    public function new(Request $request, ActRepository $actRepository): Response
    {
        $content = new Content();
        $form = $this->createForm(ContentType::class, $content);
        $act = $actRepository->findOneBy(['id'=>1]);
        $date = new \DateTime("now");
        $content->setAct($act);
        $content->setDate($date);
        $content->setNumber("1");

        $content->setDateCreated($date);
        $content->setConsecutive($act->getConsecutive()+1);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($content);
            $entityManager->flush();

            return $this->redirectToRoute('content_index');
        }

        return $this->render('content/new.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="content_show", methods={"GET"})
     */
    public function show(Content $content): Response
    {
        return $this->render('content/show.html.twig', [
            'content' => $content,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="content_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Content $content): Response
    {
        $form = $this->createForm(ContentType::class, $content);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('content_index');
        }

        return $this->render('content/edit.html.twig', [
            'content' => $content,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/create", name="create_content", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(Request $request):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            $value = $request->request->get('value');
        }
        else {
            die();
        }

        $date = new \DateTime("now");

        $act = $this->actRepository->findOneBy(['id'=>1]);
        $number = $this->contentRepository->findMaxNumber(1);
        $consecutive = $this->contentRepository->findMaxConsecutive(1);

        $em = $this->getDoctrine()->getManager();
        $content = new Content();

        $content->setData($value);
        $content->setContent($value);
        $content->setAct($act);
        $content->setDateCreated($date);
        $content->setDate($date);
        $content->setNumber($number+1);
        $content->setConsecutive($consecutive+1);

        $em->persist($content);
        $em->flush();
        $response = 'New Value Added';

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/update", name="update_content", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function update(Request $request):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {

            $value = $request->request->get('value');
            $column = $request->request->get('column');
            $id = $request->request->get('id');

        }
        else {
            die();
        }

        $em = $this->getDoctrine()->getManager();

        $content = $this->contentRepository->findOneByCompanyID(1,$id);
        $date = new \DateTime("now");


        $content->setData($value);
        $content->setDate($date);

        $em->flush();
        $response = 'aaaa';

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/{id}", name="content_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Content $content): Response
    {
        if ($this->isCsrfTokenValid('delete'.$content->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($content);
            $entityManager->flush();
        }

        return $this->redirectToRoute('content_index');
    }
}
