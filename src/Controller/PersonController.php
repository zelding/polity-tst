<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TODO: logging, auth?, cache, error handling
 */
class PersonController extends AbstractController
{
    #[Route('/person', name: 'app_persons')]
    public function index(MemberRepository $memberRepository): JsonResponse
    {
        $members = $memberRepository->findAll();

        return $this->json($members);
    }

    #[Route('/person/{id}', name: 'app_person_details')]
    public function details(int $id, MemberRepository $memberRepository): JsonResponse
    {
        $member = $memberRepository->findByIdWithContacts($id);

        return $this->json($member);
    }
}
