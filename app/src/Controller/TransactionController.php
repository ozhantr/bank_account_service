<?php

namespace App\Controller;

use App\Application\Service\TransactionService;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Entity\User;
use App\Http\Dto\TransactionRequest;
use App\Util\Money;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/accounts')]
final class TransactionController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/{id}/transactions', name: 'api_accounts_tx_create', methods: ['POST'])]
    public function create(
        #[CurrentUser]
        ?User $user,
        string $id,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        TransactionService $service
    ): JsonResponse {
        if (null === $user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $dto = $serializer->deserialize($request->getContent(), TransactionRequest::class, 'json');
        $violations = $validator->validate($dto);
        if (\count($violations) > 0) {
            $errors = [];
            foreach ($violations as $v) {
                $errors[] = ['field' => $v->getPropertyPath(), 'message' => $v->getMessage()];
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $amountCents = Money::decimalToCents($dto->amount);

            $type = $dto->type;

            $tx = $service->perform($user, $id, $type, $amountCents);

            return $this->json([
                'id' => (string) $tx->getId(),
                'type' => $tx->getType(),
                'amount_cents' => $tx->getAmountCents(),
                'balance_after_cents' => $tx->getBalanceAfterCents(),
                'currency' => 'EUR',
                'created_at' => $tx->getCreatedAt()->format(\DATE_ATOM),
            ], Response::HTTP_CREATED);
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/{id}/transactions', name: 'api_accounts_tx_list', methods: ['GET'])]
    public function list(
        #[CurrentUser]
        ?User $user,
        string $id,
    ): JsonResponse {
        if (null === $user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $account = $this->em->getRepository(Account::class)->find($id);
        if (!$account) {
            return $this->json(['error' => 'Account not found'], Response::HTTP_NOT_FOUND);
        }
        if ($account->getUser()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        $txRepo = $this->em->getRepository(Transaction::class);

        $txs = $txRepo->createQueryBuilder('t')
            ->andWhere('t.account = :acc')->setParameter('acc', $account)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();

        $out = [];
        foreach ($txs as $tx) {
            $out[] = [
                'id' => (string) $tx->getId(),
                'type' => $tx->getType(),
                'amount_cents' => $tx->getAmountCents(),
                'balance_after_cents' => $tx->getBalanceAfterCents(),
                'currency' => 'EUR',
                'created_at' => $tx->getCreatedAt()->format(\DATE_ATOM),
            ];
        }

        return $this->json(['items' => $out]);
    }
}
