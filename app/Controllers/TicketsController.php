<?php
namespace App\Controllers;

use App\Controllers\Helpers\StatusEnum;
use Phalcon\Mvc\Controller;
use App\Models\Tickets;

class TicketsController extends BaseController
{
    public function index()
    {
        $tickets = Tickets::find();
        return self::success($tickets);
    }

    /**
     * @param int $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function findAction(int $id){
        $tickets = Tickets::findFirst($id);
        if ($tickets === false){
            return self::notFound("Тикет не найден");
        } else {
            return self::success($tickets);
        }
    }


    /**
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function createAction(){
        $requestJsonBody = $this->request->getJsonRawBody(true);

        $ticket = new Tickets($requestJsonBody);

        if ($ticket->save() === false) {
            $messages = $ticket->getMessages();

            $errors = [];

            foreach ($messages as $message) {
                $errors[] = $message->getMessage();
            }
            return self::error($errors);
        } else {
            return self::success($ticket);
        }
    }

    /**
     * @param int $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function deleteAction(int $id){
        $ticket = Tickets::findFirst($id);
        if ($ticket === false){
            return self::notFound("Тикет не найден");
        } else {
            if ($ticket->delete() === false){
                return self::error("При удалении возника ошибка");
            } else {
                return self::success(null,"Элемент успешно удален.");
            }
        }
    }


    /**
     * @param int $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function updateAction(int $id){
        $ticket = Tickets::findFirst($id);
        if ($ticket === false){
            return self::notFound("Тикет не найден.");
        } else {
            $requestJsonBody = $this->request->getJsonRawBody(true);
            if ($ticket->update($requestJsonBody) === false){
                return self::error("Не удалось обновить элемент.");
            } else {
                return self::success(null,"Элемент успешно обновлен.");
            }
        }
    }

}