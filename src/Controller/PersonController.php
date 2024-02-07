<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * TODO: logging, auth?, error handling
 */
class PersonController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly RateLimiterFactory $anonymousApiLimiter
    )
    {}

    #[Route('/person', name: 'app_persons')]
    #[Cache(maxage: 3600, public: true)]
    public function index(Request $request, MemberRepository $memberRepository): JsonResponse
    {
        $limiter = $this->createLimiter($request);

        $limit   = $limiter->consume(1);

        if (!$limit->isAccepted()) {
            return $this->rateLimitErrorResponse();
        }

        $headers = $this::createLimiterHeaders($limit);

        $members = $memberRepository->findAll();

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(
                $members, 'json', ['groups' => 'list']
            ), Response::HTTP_OK, $headers
        );
    }

    #[Route('/person/{id}', name: 'app_person_details')]
    #[Cache(maxage: 3600, public: true)]
    public function details(Request $request, int $id, MemberRepository $memberRepository): JsonResponse
    {
        $limiter = $this->createLimiter($request);
        $limit   = $limiter->consume(1);

        if (!$limit->isAccepted()) {
            return $this->rateLimitErrorResponse();
        }

        $headers = $this::createLimiterHeaders($limit);

        if ($member = $memberRepository->findByIdWithContacts($id)) {
            return JsonResponse::fromJsonString(
                $this->serializer->serialize(
                    $member, 'json', ['groups' => ['list', 'details']]
                ), Response::HTTP_OK, $headers
            );
        }

        throw $this->createNotFoundException("Person not found");
    }

    private function createLimiter(Request $request): LimiterInterface
    {
        return $this->anonymousApiLimiter->create($request->getClientIp());
    }

    private static function createLimiterHeaders(RateLimit $limit): array
    {
        return [
            'X-RateLimit-Remaining'   => $limit->getRemainingTokens(),
            'X-RateLimit-Retry-After' => $limit->getRetryAfter()->getTimestamp() - time(),
            'X-RateLimit-Limit'       => $limit->getLimit(),
        ];
    }

    private function rateLimitErrorResponse(): JsonResponse
    {
        return $this->json([
            "message" => "Too many requests",
            "code"    => Response::HTTP_TOO_MANY_REQUESTS
            // Response::HTTP_ENHANCE_YOUR_CALM xD
        ], Response::HTTP_TOO_MANY_REQUESTS);
    }
}
