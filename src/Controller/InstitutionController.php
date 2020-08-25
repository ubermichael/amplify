<?php

namespace App\Controller;

use App\Entity\Institution;
use App\Form\InstitutionType;
use App\Repository\InstitutionRepository;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/institution")
 * @IsGranted("ROLE_USER")
 */
class InstitutionController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * @Route("/", name="institution_index", methods={"GET"})
     * @param Request $request
     * @param InstitutionRepository $institutionRepository
     *
     * @Template()
     *
     * @return array
     */
    public function index(Request $request, InstitutionRepository $institutionRepository) : array
    {
        $query = $institutionRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'institutions' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="institution_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, InstitutionRepository $institutionRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $institutionRepository->searchQuery($q);
            $institutions = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), array('wrap-queries'=>true));
        } else {
            $institutions = [];
        }

        return [
            'institutions' => $institutions,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="institution_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, InstitutionRepository $institutionRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($institutionRepository->typeaheadSearch($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string)$result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="institution_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $institution = new Institution();
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($institution);
            $entityManager->flush();
            $this->addFlash('success', 'The new institution has been saved.');

            return $this->redirectToRoute('institution_show', ['id' => $institution->getId()]);
        }

        return [
            'institution' => $institution,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="institution_new_popup", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="institution_show", methods={"GET"})
     * @Template()
     * @param Institution $institution
     *
     * @return array
     */
    public function show(Institution $institution) {
        return [
            'institution' => $institution,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="institution_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Institution $institution
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Institution $institution) {
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated institution has been saved.');

            return $this->redirectToRoute('institution_show', ['id' => $institution->getId()]);
        }

        return [
            'institution' => $institution,
            'form' => $form->createView()
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="institution_delete", methods={"DELETE"})
     * @param Request $request
     * @param Institution $institution
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Institution $institution) {
        if ($this->isCsrfTokenValid('delete' . $institution->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($institution);
            $entityManager->flush();
            $this->addFlash('success', 'The institution has been deleted.');
        }

        return $this->redirectToRoute('institution_index');
    }
}