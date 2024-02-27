<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\FriendRequest;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/inbox')]
class InboxController extends AbstractController
{
	#[Route('/', name: 'app_inbox_index', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();

		if (isset($_POST["request_id"]) && isset($_POST["request"])) {
			$this->handleFriendRequest($entityManager, $_POST["request_id"], $_POST["request"]);
			return $this->redirectToRoute($request->attributes->get('_route'), $request->attributes->get('_route_params'));
		}

        return $this->render('inbox/index.html.twig', [
            'conversations' => $appUser->getConversations(),
			'friend_requests' => $appUser->getFriendRequestsReceived(true),
        ]);
    }

	#[Route('/new', name: 'app_inbox_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FriendRequestRepository $friendRequestRepository, UserRepository $userRepository): Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();

		$message = new Message;
		if (isset($_GET['to'])) { // This is used for when you come to this screen by clicking on 'send new message' from a friend's profile
			$message->setUserTo($userRepository->findOneBy([ 'id' => $_GET['to'] ]));
		}
        $form = $this->createForm(MessageType::class, $message, [ 'app_user' => $appUser ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			// Double check: is this user allowed to send messages?
			if ($appUser->isMessageAllowed() === false) {
				throw $this->createAccessDeniedException('You are banned from sending messages!');
			}
			// Add fields to new message not filled out by the form
			$message->setUserFrom($this->getUser());
			$message->setCreatedOn(new DateTimeImmutable('now'));
			$message->setUnread(true);
			// Double check: are the sender and the recipient (still) friends?
			if ($message->getUserTo()->getFriendStatus($this->getUser(), $friendRequestRepository) != 3) {
				throw $this->createAccessDeniedException('You can only send messages to friends!');
			}
			// Create conversation to house the new message
			$conversation = new Conversation;
			$conversation->addUser($message->getUserFrom());
			$conversation->addUser($message->getUserTo());
			$conversation->addMessage($message);
			// persist & flush data
			$entityManager->persist($conversation);
			$entityManager->persist($message);
            $entityManager->flush();
			// redirect to conversation page
            return $this->redirectToRoute('app_inbox_show', ['id' => $conversation->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inbox/new.html.twig', [
            'message' => $message,
            'messageForm' => $form,
        ]);
    }

	#[Route('/{id}', name: 'app_inbox_show', methods: ['GET', 'POST'])]
    public function show(Conversation $conversation, EntityManagerInterface $entityManager, FriendRequestRepository $friendRequestRepository): Response
    {
		/** @var $appUser User */
		$appUser = $this->getUser();
		
		if (isset($_POST['content'])) {
			$message = new Message;
			// Double check: is this user allowed to send messages?
			if ($appUser->isMessageAllowed() === false) {
				throw $this->createAccessDeniedException('You are banned from sending messages!');
			}
			// Add fields to new message as we are not using the form
			$message->setUserFrom($this->getUser());
			$message->setUserTo($conversation->getOtherUser($this->getUser()));
			$message->setCreatedOn(new DateTimeImmutable('now'));
			$message->setContent(htmlspecialchars($_POST['content']));
			$message->setUnread(true);
			// Double check: are the sender and the recipient (still) friends?
			if ($message->getUserTo()->getFriendStatus($this->getUser(), $friendRequestRepository) != 3) {
				throw $this->createAccessDeniedException('You can only send messages to friends!');
			}
			// Add this message to the current conversation
			$conversation->addMessage($message);
			// persist & flush data
			$entityManager->persist($message);
            $entityManager->flush();
			// redirect to conversation page
            return $this->redirectToRoute('app_inbox_show', ['id' => $conversation->getId()], Response::HTTP_SEE_OTHER);
		}

		$conversation->markAsReadFor($this->getUser());
		$entityManager->flush();

        return $this->render('inbox/show.html.twig', [
            'conversation' => $conversation,
			'messages' => $conversation->getMessages(),
			'user' => $conversation->getOtherUser($this->getUser()),
			'friendStatus' => $conversation->getOtherUser($this->getUser())->getFriendStatus($this->getUser(), $friendRequestRepository), // 0 = Not friends, 1 = Sent friend request, 2 = Received friend request, 3 = Friends
        ]);
    }

	public function handleFriendRequest(EntityManagerInterface $entityManager, int $requestId, string $response): static
	{
		$friendRequest = $entityManager->getRepository(FriendRequest::class)->findOneBy([ 'id' => $requestId ]);
		if ($friendRequest->getUserTo() != $this->getUser()) {
			throw $this->createAccessDeniedException('You are not authorized to handle this friend request!');
		} elseif ($response === 'accept') {
			$friendRequest->setAccepted(true);
			$friendRequest->getUserFrom()->addFriend($friendRequest->getUserTo());
			$friendRequest->getUserTo()->addFriend($friendRequest->getUserFrom());
		} elseif ($response === 'refuse') {
			$entityManager->remove($friendRequest);
		}
		$entityManager->flush();

		return $this;
	}
}
