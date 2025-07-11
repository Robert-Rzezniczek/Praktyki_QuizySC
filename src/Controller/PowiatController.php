<?php

/**
 * Powiat controller.
 */

namespace App\Controller;

use App\Entity\Powiat;
use App\Form\Type\PowiatType;
use App\Service\PowiatServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PowiatController.
 */
#[Route('/powiat')]
class PowiatController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param PowiatServiceInterface $powiatService Powiat service
     * @param TranslatorInterface    $translator    Translator
     */
    public function __construct(private readonly PowiatServiceInterface $powiatService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'powiat_index',
        methods: 'GET'
    )]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->powiatService->getPaginatedList($page);

        return $this->render('powiat/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * View action.
     *
     * @param Powiat $powiat Powiat entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'powiat_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function view(Powiat $powiat): Response
    {
        return $this->render(
            'powiat/view.html.twig',
            ['powiat' => $powiat]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'powiat_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $powiat = new Powiat();
        $form = $this->createForm(PowiatType::class, $powiat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->powiatService->save($powiat);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('powiat_index');
        }

        return $this->render(
            'powiat/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Powiat  $powiat  Powiat entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'powiat_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Powiat $powiat): Response
    {
        $form = $this->createForm(
            PowiatType::class,
            $powiat,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('powiat_edit', ['id' => $powiat->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->powiatService->save($powiat);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('powiat_index');
        }

        return $this->render(
            'powiat/edit.html.twig',
            [
                'form' => $form->createView(),
                'powiat' => $powiat,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Powiat  $powiat  Powiat entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'powiat_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE'
    )]
    public function delete(Request $request, Powiat $powiat): Response
    {
        if (!$this->powiatService->canBeDeleted($powiat)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.cannot_delete_powiat_with_user_profiles')
            );

            return $this->redirectToRoute('powiat_index');
        }

        $form = $this->createForm(FormType::class, $powiat, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('powiat_delete', ['id' => $powiat->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->powiatService->delete($powiat);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('powiat_index');
        }

        return $this->render(
            'powiat/delete.html.twig',
            [
                'form' => $form->createView(),
                'powiat' => $powiat,
            ]
        );
    }
}
