<?php

/**
 * Answer controller.
 */

namespace App\Controller;

use App\Entity\Answer;
use App\Form\Type\AnswerType;
use App\Service\AnswerServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AnswerController.
 */
#[Route('/answer')]
class AnswerController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param AnswerServiceInterface $answerService Answer service
     * @param TranslatorInterface    $translator    Translator
     */
    public function __construct(private readonly AnswerServiceInterface $answerService, private readonly TranslatorInterface $translator)
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
        name: 'answer_index',
        methods: 'GET'
    )]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->answerService->getPaginatedList($page);

        return $this->render('answer/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * View action.
     *
     * @param Answer $answer Answer entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'answer_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function view(Answer $answer): Response
    {
        return $this->render(
            'answer/view.html.twig',
            ['answer' => $answer]
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
        name: 'answer_create',
        methods: 'GET|POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->answerService->save($answer);
                $this->addFlash(
                    'success',
                    $this->translator->trans('message.created_successfully')
                );

                return $this->redirectToRoute('answer_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render(
            'answer/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Answer  $answer  Answer entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'answer_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Answer $answer): Response
    {
        $form = $this->createForm(
            AnswerType::class,
            $answer,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('answer_edit', ['id' => $answer->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->answerService->save($answer);
                $this->addFlash(
                    'success',
                    $this->translator->trans('message.edited_successfully')
                );

                return $this->redirectToRoute('answer_index');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render(
            'answer/edit.html.twig',
            [
                'form' => $form->createView(),
                'answer' => $answer,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Answer  $answer  Answer entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'answer_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|POST'
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Answer $answer): Response
    {
        if (!$this->answerService->canBeDeleted($answer)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.answer_cannot_be_deleted')
            );

            return $this->redirectToRoute('answer_index');
        }

        $form = $this->createForm(
            FormType::class,
            $answer,
            [
                'method' => 'POST',
                'action' => $this->generateUrl('answer_delete', ['id' => $answer->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->answerService->delete($answer);
            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('answer_index');
        }

        return $this->render(
            'answer/delete.html.twig',
            [
                'form' => $form->createView(),
                'answer' => $answer,
            ]
        );
    }
}
