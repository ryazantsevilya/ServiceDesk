<?php


namespace App\Models;


use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class Tickets extends Model
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    public function validation()
    {
        $validator = new Validation();

        $validator->add('content', new PresenceOf())
            ->add('title', new PresenceOf());
        return $this->validate($validator);
    }
}