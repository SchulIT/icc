<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class NewChat {

    #[Assert\Count(min: 1)]
    public array $recipients = [ ];

    #[Assert\NotBlank(allowNull: true)]
    public ?string $topic;

    #[Assert\NotBlank]
    public ?string $message;
}