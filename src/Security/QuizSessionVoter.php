<?php


namespace App\Security;


use App\Entity\QuizAccess;
use App\Entity\QuizSession;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class QuizSessionVoter extends Voter
{

    const EDIT = 'edit';
    const SUBMIT_QUESTIONS = 'edit.questions';
    const VIEW = 'view';
    const VIEW_REPORT = 'view.report';
    private $security;
    private $accessDecisionManager;
    private $entityManager;

    public function __construct(Security $security, AccessDecisionManagerInterface $accessDecisionManager,EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->entityManager = $entityManager;

    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT,self::SUBMIT_QUESTIONS,self::VIEW,self::VIEW_REPORT])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof QuizSession) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        /** @var User $user */
        $user = $token->getUser();

        /** make sure user is logged in */
        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(User::ROLE_ADMIN)) {
            return true;
        }


        /** @var QuizSession  $quiz_session */
        $quiz_session = $subject;

        switch ($attribute){
            case self::EDIT:
                return $this->canSubmitQuestion($quiz_session,$user);
            case self::VIEW:
                return $this->canViewQuestions($quiz_session,$user);
            case self::SUBMIT_QUESTIONS:
                return $this->canSubmitQuestionsSession($quiz_session,$user);
            case self::VIEW_REPORT:
                return $this->canViewReport($quiz_session,$user);

        }
        throw new \LogicException('This code should not be reached!');
    }

    public function canViewReport(QuizSession $session, User $user ){
        $activeSession = $user->getActiveSession();
        if ($activeSession->count() > 0) {
            return false;
        }
        if($session->getOwner() === $user && $session->getSubmittedAt() !== null){
            return true;
        }
        return false;
    }

    private function canSubmitQuestionsSession(QuizSession $session, User $user)
    {
        /** make sure the user is the owner and the exam isn't submitted */
        if($session->getOwner() === $user && $session->getSubmittedAt() === null){
            return true;
        }
        return false;
    }

    private function canViewQuestions(QuizSession $session, User $user)
    {
        $activeSession = $user->getActiveSession();
        if ($activeSession->count() > 0) {
            if ($session->getOwner() === $user && $user->getActiveSession()->contains($session)) {
                return true;
            }
        } else {
            if ($session->getOwner() === $user) {
                return true;
            }
        }
        return false;
    }

    private function canSubmitQuestion(QuizSession $session, User $user){
        if($session->getOwner() === $user){
            return true;
        }
        return false;
    }
}