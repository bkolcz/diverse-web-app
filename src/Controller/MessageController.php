<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MessageServiceInterface;

#[Route('/api/message')]
class MessageController extends AbstractController
{
    public function __construct(private MessageServiceInterface $messageService)
    {
    }

    #[Route('', name: 'app_message', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $response = $this->messageService->sort(
            $this->messageService->getAll(),
            $request->query->get('by') ?? '',
            $request->query->get('order') ?? ''
        );
        return $this->json($response);
    }

    #[Route('', name: 'app_message_save', methods: ['POST'])]
    public function save(Request $request): JsonResponse
    {
        $content = $request->getContent(); // get message
        $status = $this->messageService->save($content); // call service to save it to file
        return $this->json([
            'status' => $status
        ]);
    }

    #[Route('/{uuid}', name: 'app_message_get_one', methods: ['GET'])]
    public function getOne(string $uuid): JsonResponse
    {
        return $this->json($this->messageService->getOne($uuid));
    }
}
