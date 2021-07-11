<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book/comment")
 * @IsGranted("ROLE_BOOK_ENTRY_CREATOR")
 */
class BookCommentController extends AbstractController {



    /**
     * @Route("/add", name="add_book_comment")
     */
    public function add() {

    }

    /**
     * @Route("/{uuid}/edit", name="edit_book_comment")
     */
    public function edit() {

    }

    /**
     * @Route("/{uuid}/remove", name="remove_book_student")
     */
    public function remove() {

    }
}