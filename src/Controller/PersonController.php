<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * TODO: logging, auth?, error handling, throttling
 */
class PersonController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer
    )
    {}

    #[Route('/person', name: 'app_persons')]
    #[Cache(maxage: 3600, public: true)]
    public function index(MemberRepository $memberRepository): JsonResponse
    {
        $members = $memberRepository->findAll();

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(
                $members, 'json', ['groups' => 'list']
            )
        );
    }

    #[Route('/person/{id}', name: 'app_person_details')]
    #[Cache(maxage: 3600, public: true)]
    public function details(int $id, MemberRepository $memberRepository): JsonResponse
    {
        if ($member = $memberRepository->findByIdWithContacts($id)) {
            return JsonResponse::fromJsonString(
                $this->serializer->serialize(
                    $member, 'json', ['groups' => ['list', 'details']]
                )
            );
        }

        return $this->json([
            "message" => "not found",
            "code"    => Response::HTTP_NOT_FOUND],
            Response::HTTP_NOT_FOUND
        );
    }
}
