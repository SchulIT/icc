<?php

namespace App\Message\Poll;

use App\Entity\Message;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(AssignmentStrategyInterface::AUTOCONFIGURE_TAG)]
interface AssignmentStrategyInterface {
    public const string AUTOCONFIGURE_TAG = 'app.message.poll_vote_assignment_strategy';

    public function assign(Message $message): AssignmentResult|null;

    public function getTranslationKey(): string;

    public function getHelpTranslationKey(): string;
}