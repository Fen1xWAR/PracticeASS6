<?php
require "queryService.php";

/**
 * Проверка данных для входа.
 *
 * @param string $email Электронная почта пользователя.
 * @param string $password Пароль пользователя.
 * @return array|string Массив с данными пользователя при успешной проверке, или строка в случае ошибки.
 */
function CheckLoginData(string $email, string $password): array|string
{
    try {
        // Запрос для получения данных пользователя по email
        $query = "SELECT user_id, password, roles.role FROM users JOIN roles ON users.role_id = roles.role_id WHERE users.email = :email";

        // Выполнение запроса и получение результата
        $result = executeQuery($query, [":email" => $email], false);

        // Если пользователь найден и пароль верен
        if ($result && password_verify($password, $result['password'])) {
            return [
                "userID" => $result['user_id'],
                "userRole" => $result['role']
            ];
        }
    } catch (PDOException $e) {
        // Логируем ошибку и возвращаем код ошибки
        error_log($e->getMessage());
        http_response_code(500);
        echo json_encode(["message" => "Внутренняя ошибка сервера"], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Возвращаем пустую строку в случае ошибки
    return '';
}

/**
 * Сохранение лога входа пользователя.
 *
 * @param int $userId ID пользователя, для которого сохраняется лог.
 */
function saveLog(int $userId): void
{
    // Запрос для сохранения времени входа пользователя
    $query = "INSERT INTO userLogs (user_id, logIn_dateTime) VALUES(:user_id, :log_time)";
    executeQuery($query, [":user_id" => $userId, ":log_time" => date("Y-m-d H:i:s")]);
}

/**
 * Проверка наличия пользователя с таким email в базе данных.
 *
 * @param string $email Электронная почта пользователя.
 * @return string Сообщение об ошибке, если пользователь существует, или пустая строка.
 */
function checkRegData(string $email): string
{
    // Запрос для проверки наличия пользователя с данным email
    $query = "SELECT user_id FROM users WHERE email = :email";
    $result = executeQuery($query, [":email" => $email], false);

    // Если пользователь найден, возвращаем сообщение об ошибке
    return $result ? "Пользователь с таким Email уже существует!" : "";
}

// Обработчик регистрации пользователя
if (isset($_POST['register'])) {
    $registerData = $_POST['register'];
    $email = $registerData['email'];

    // Проверка наличия пользователя с таким email
    $data = checkRegData($email);

    if ($data != '') {
        http_response_code(404);
        echo json_encode(["message" => $data], JSON_UNESCAPED_UNICODE);
    } else {
        // Регистрация нового пользователя
        $password = $registerData['password'];
        register($registerData);

        // Вход после регистрации
        login($email, $password);
        http_response_code(200);
    }
}

// Обработчик редактирования данных пользователя
if (isset($_POST['editUser'])) {
    $editedData = $_POST['editUser'];

    // Обновление данных пользователя (роль и email)
    $query = "UPDATE users SET role_id = :role_id, email = :email WHERE user_id = :user_id";
    executeQuery($query, [
        ":role_id" => $editedData['userRole'],
        ":email" => $editedData['userEmail'],
        ":user_id" => $editedData['userId']
    ]);

    // Если указан новый пароль, обновляем его
    if ($editedData['userPassword'] != '') {
        $query = "UPDATE users SET password = :password WHERE user_id = :user_id";
        executeQuery($query, [
            ":password" => password_hash($editedData['userPassword'], PASSWORD_DEFAULT),
            ":user_id" => $editedData['userId']
        ]);
    }

    // Получаем обновленные данные пользователя
    $query = "SELECT * FROM users WHERE user_id = :user_id";
    $result = executeQuery($query, [":user_id" => $editedData['userId']], false);

    // Обновление данных пользователя в таблице human_data
    $query = "UPDATE human_data SET name = :name, surname = :surname, lastname = :lastname WHERE data_id = :data_id";
    executeQuery($query, [
        ":data_id" => $result['data_id'],
        ":name" => $editedData['userName'],
        ":surname" => $editedData['userSurname'],
        ":lastname" => $editedData['userLastname']
    ]);

    http_response_code(200);
}

// Обработчик удаления пользователя
if (isset($_POST['removeUser'])) {
    $userId = $_POST['removeUser'];

    // Получаем data_id пользователя для удаления
    $query = "SELECT data_id FROM users WHERE user_id = :user_id";
    $result = executeQuery($query, [":user_id" => $userId], false);
    $dataId = $result['data_id'];

    // Удаляем пользователя и его данные
    executeQuery("DELETE FROM users WHERE user_id = :user_id", [":user_id" => $userId]);
    executeQuery("DELETE FROM human_data WHERE data_id = :data_id", [":data_id" => $dataId]);
}

// Обработчик логина пользователя
if (isset($_POST['login'])) {
    $logData = $_POST['login'];
    $email = $logData['email'];
    $password = $logData['password'];
    login($email, $password);
}

// Обработчик данных для редиректа на страницу регистрации
if (isset($_POST['dataToRegisterRedirect'])) {
    $dataToRegisterRedirect = $_POST['dataToRegisterRedirect'];
    setcookie("dataToRegisterRedirect", json_encode($dataToRegisterRedirect), time() + 20, "/");
    http_response_code(200);
}

// Обработчик выхода пользователя
if (isset($_POST['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    exit();
}

/**
 * Регистрация нового пользователя.
 *
 * @param array $dataToRegister Данные пользователя для регистрации.
 */
function register(array $dataToRegister): void
{
    global $dbh;

    // Добавление данных в таблицу human_data
    $query = "INSERT INTO human_data (name, surname, lastname) VALUES (:name, :surname, :lastname)";
    executeQuery($query, [
        ":name" => $dataToRegister['name'],
        ":surname" => $dataToRegister['surname'],
        ":lastname" => $dataToRegister['lastname']
    ]);

    // Получаем ID добавленного пользователя
    $dataIdQuery = $dbh->query('SELECT data_id FROM human_data ORDER BY data_id DESC LIMIT 1');
    $dataId = $dataIdQuery->fetch()['data_id'];

    // Добавление данных в таблицу users
    $query = "INSERT INTO users (email, password, role_id, data_id) VALUES (:email, :password, :roleId, :dataId)";
    executeQuery($query, [
        ":dataId" => $dataId,
        ":email" => $dataToRegister['email'],
        ":password" => password_hash($dataToRegister['password'], PASSWORD_DEFAULT),
        ":roleId" => $dataToRegister['role']
    ]);

    // Получаем ID пользователя
    $userIdQuery = $dbh->query("SELECT user_id FROM users ORDER BY user_id DESC LIMIT 1");
    $userId = $userIdQuery->fetch()['user_id'];

    // Вставляем в таблицу соответствующую роль (учитель или студент)
    switch ($dataToRegister['role']) {
        case '2':
            executeQuery('INSERT INTO teachers (user_id) VALUES (:userId)', [":userId" => $userId]);
            break;
        default:
            executeQuery('INSERT INTO students (user_id, group_id) VALUES (:userId, :groupId)', [
                ":userId" => $userId,
                ":groupId" => $dataToRegister['group_id']
            ]);
            break;
    }
}

/**
 * Вход пользователя в систему.
 *
 * @param string $email Электронная почта пользователя.
 * @param string $password Пароль пользователя.
 */
function login(string $email, string $password): void
{
    $data = CheckLoginData($email, $password);

    // Если данные неверны, возвращаем ошибку
    if (is_string($data)) {
        http_response_code(400);
        echo json_encode(["message" => "Неверная почта или пароль!"], JSON_UNESCAPED_UNICODE);
    } else {
        // Иначе создаем сессию
        $role = $data['userRole'];
        $id = $data['userID'];

        session_start();
        saveLog($id);
        $_SESSION['userRole'] = $role;
        $_SESSION['userID'] = $id;

        http_response_code(200);
    }
}
