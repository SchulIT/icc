<?php

namespace App\Markdown\Element;

use League\CommonMark\Inline\Element\Link;

class AnchorLink extends Link {
    private $id;

    public function __construct($id, $label = null, $title = '') {
        parent::__construct(sprintf('#%s', $id), $label, $title);

        $this->id = $id;
        $this->data['attributes']['id'] = $id;
    }

    public function getId() {
        return $this->id;
    }
}