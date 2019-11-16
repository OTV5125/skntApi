<?php
/**
 * Created by PhpStorm.
 * User: OTV
 * Date: 15.11.2019
 * Time: 13:25
 */

namespace Api\Api;


abstract class Api
{
    public $startIndex; //Индекс запускаемого класса в url
    protected $mysql;
    protected $method = ''; //GET|POST|PUT|DELETE
    public $requestUri = [];
    public $requestParams = [];
    protected $action = ''; //Название метод для выполнения

    /**
     * Api constructor.
     * @param $index индекс найденного класса в url'e
     */
    public function __construct($index)
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        //Массив GET параметров разделенных слешем
        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $this->requestParams = $_REQUEST;

        //Поиск модели по вызванному методу
        $className = explode('\\', get_called_class());
        $className = array_pop($className);
        $modelClass = '\Api\Mysql\Mysql' . substr($className, 3);
        $this->mysql = new $modelClass();

        //Определение метода запроса
        $this->method = $_SERVER['REQUEST_METHOD'];

        //Запоминаем индекс точки входа
        $this->startIndex = $index;
    }

    /**
     * Запуск приложения
     * @return mixed
     */
    public function run()
    {
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                $this->outputJsonResult(['result' => 'error', 'message' => 'Forbidden method']);
            }
        }

        //Определение действия для обработки
        $this->action = $this->getAction();

        //Если метод(действие) определен в дочернем классе API
        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            $this->outputJsonResult(['result' => 'error', 'message' => 'Invalid method']);
        }
    }

    /**
     * Вывод json из array
     * @param $arr
     */
    protected function outputJsonResult($arr)
    {
        if (!is_array($arr)) {
            echo json_encode(['result' => 'error', 'message' => 'error json result, var not array']);
        } else {
            echo json_encode($arr);
        }

        exit;
    }

    /**
     * Получение действия из заспроса
     * @return null|string
     */
    protected function getAction()
    {
        $method = $this->method;
        switch ($method) {
            case 'GET':
                if ($this->requestUri) {
                    return 'viewAction';
                } else {
                    return 'indexAction';
                }
                break;
            case 'POST':
                return 'createAction';
                break;
            case 'PUT':
                return 'updateAction';
                break;
            case 'DELETE':
                return 'deleteAction';
                break;
            default:
                return null;
        }
    }
}