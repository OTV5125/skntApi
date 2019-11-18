# Тестовая работа skntApi

**Описание задания:**<br>
Смотрите в TASK.md</br>

Для установки проекта необходимо выполнить следующие действия:
```bash
git clone https://github.com/OTV5125/skntApi.git
cd skntApi
composer update
```

```bash
nano db_cfg.php
```

```php
<?php
define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_NAME', '');
```

**Логика работы**<br>
В index.php url разбивается на массив, в котором конструкцией if/else происходит поиск класса с которым работает Api
в данном примере точка входа одна 'users'

```php
if($item === 'users'){
        $apiClass = ['name' => '\Api\Api\\Api'.ucfirst($item), 'i' => $i];
        break;
    }
```

Последующие точки входа необходимо прописать в этом же условии, например 'test'

```php
if($item === 'users' || $item === 'test'){
        $apiClass = ['name' => '\Api\Api\\Api'.ucfirst($item), 'i' => $i];
        break;
    }
```

После чего необходимо создать класс для работы с Api и класс для работы с db (сделал это обязательным условием, так как Api подразумевает работу с базой в любом случае)<br>
Класс (ветка) для работы с api должен находиться 
```php
- \Api\Api\
```
 название должно быть строго 
 ```php
- Api{Name}.php
```
В примере 'test' класс должен быть
```php
 \Api\Api\ApiTest.php  
 ```
<br><br>


Класс для работы с mysql должен находиться 
```php
- \Api\Mysql\
```
 название должно быть строго 
 ```php
- Mysql{Name}.php
```
В примере 'test' класс должен быть

```php
 \Api\Mysql\MysqlTest.php  
 ```

Оба класса должны наследовать родителей Api.php и Mysql.php

*ApiTest*
Наследуемый класс получает http метод по которому было обращение, из класса Api. 
Возможные методы 
- viewAction //get
- indexAction //get 
- createAction //post
- updateAction //put
- deleteAction //delete

Соответственно, что бы работать с запросами put, в классе необходимо создать метод
updateAction и всю логику по работе с данным запросом создавать уже в этом методе. 
Если метод не создан или метод неизвестен, происходит вывод ошибки json. 

