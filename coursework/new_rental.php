<?php
    session_start();
    $error_message = '';
    $success_message = '';
    if (isset($_SESSION['success_message'])) {
        $success_message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        $error_message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
    }

    include 'database.php';
    $users = pg_query($conn, "SELECT id, first_name, second_name FROM users ORDER BY second_name, first_name");
    $books = pg_query($conn, "SELECT id, name FROM books ORDER BY name");
    $res = pg_query($conn, 
            "SELECT 
            rentals.id,
            users.first_name,
            users.second_name,
            books.name AS book_name,
            rentals.date
            FROM rentals
            JOIN users ON rentals.user_id = users.id
            JOIN books ON rentals.book_id = books.id
            ORDER BY rentals.id");

    if (isset($_POST['add_rental'])) {
        $user_id = trim($_POST['user_id']);
        $book_id = trim($_POST['book_id']);
        $date = trim($_POST['date']);

        if ($user_id && $book_id && $date) {
            $insert = pg_query_params($conn, "INSERT INTO rentals (user_id, book_id, date) VALUES ($1, $2, $3)", array($user_id, $book_id, $date));

            if ($insert) {
                $_SESSION['success_message'] = "Оренду додано.";

                $_SESSION['old_user_id'] = $user_id;
                $_SESSION['old_book_id'] = $book_id;
                $_SESSION['old_date'] = $date;

                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
            else {
                $_SESSION['error_message'] = "Помилка при додаванні оренди.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
        else {
            $_SESSION['error_message'] = "Будь ласка, заповніть всі поля.";
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    function isSelected($value, $selected) {
        return $value == $selected ? 'selected' : '';
    }

    $selectedUser = $_SESSION['old_user_id'] ?? '';
    $selectedBook = $_SESSION['old_book_id'] ?? '';
    $selectedDate = $_SESSION['old_date'] ?? '';

    unset($_SESSION['old_user_id'], $_SESSION['old_book_id'], $_SESSION['old_date']);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Взяти книгу в оренду</title>
    <style>
        table {
            margin-top: 10px;
            width: 60%;
            border-spacing: 0;
            border: 3px solid black;
        }
        th, td {
            border: 1px solid black;
            padding: 8px 12px;
            text-align: center;
            font-family: 'Trebuchet MS';
        }
        th:nth-child(1), td:nth-child(1) { width: 10%; }
        th:nth-child(2), td:nth-child(2) { width: 20%; }
        th:nth-child(3), td:nth-child(3) { width: 20%; }
        th:nth-child(4), td:nth-child(4) { width: 20%; }

        .titleBox {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .titleText {
            flex: 1;
            text-align: center;
            font-size: 50px;
            font-family: 'Trebuchet MS';
            margin-top: 0;
            margin-bottom: 0;
        }
        .titleBox button {
            height: 40px;
            font-size: large;
            border-radius: 10px;
            cursor: pointer;
        }
        .actionText {
            font-size: xx-large;
            font-family: 'Trebuchet MS';
            margin-top: 0;
            margin-bottom: 0;
        }
        .inputText {
            font-family: 'Trebuchet MS';
            font-size: large;
            margin: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .submitButton {
            height: 30px;
            margin: 5px;
            border-radius: 10px;
            text-align: center;
        }
        .inputRow {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }
        .inputRow input {
            width: 200px;
            height: 25px;
            font-size: large;
        }
        .inputRow select{
            width: 200px;
            height: 25px;
            font-size: large;
            font-family: 'Trebuchet MS';
        }
        .resultText {
            font-family: 'Trebuchet MS';
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="titleBox">
    <p class="titleText">Взяти книгу в оренду</p>
    <a href="main.php"><button>На головну</button></a>
   
</div><br><br>
<div>
    <form method="post" class="inputText">
        <div class="inputRow">
            <label style="width: 150px;">Користувач:</label>
            <select name="user_id" required>
                <option value="">Оберіть користувача</option>
                <?php
                    while ($u = pg_fetch_assoc($users)) {
                        $fullName = htmlspecialchars($u['first_name'] . ' ' . $u['second_name']);
                        echo "<option value='{$u['id']}' " . isSelected($u['id'], $selectedUser) . ">$fullName</option>";
                    }
                    pg_result_seek($users, 0);
                ?>
            </select>
        </div>

        <div class="inputRow">
            <label style="width: 150px;">Книга:</label>
            <select name="book_id" required>
                <option value="">Оберіть книгу</option>
                <?php
                    while ($b = pg_fetch_assoc($books)) {
                        $bookName = htmlspecialchars($b['name']);
                        echo "<option value='{$b['id']}' " . isSelected($b['id'], $selectedBook) . ">$bookName</option>";
                    }
                    pg_result_seek($books, 0);
                ?>
            </select>
        </div>
    
        <div class="inputRow">
            <label style="width: 150px;">Дата оренди:</label>
            <input type="date" name="date" required value="<?php echo htmlspecialchars($selectedDate); ?>">
        </div>
        <input class="submitButton" style="width: 300px;" type="submit" name="add_rental" value="Додати оренду">
    </form>
<hr>
</div>

<div>
    <p class="actionText">Поточні оренди</p>
    <table>
        <tr>
            <th>ID</th>
            <th>Користувач</th>
            <th>Книга</th>
            <th>Дата</th>
        </tr>
        <?php
            while ($row = pg_fetch_assoc($res)) {
                $userFullName = htmlspecialchars($row['first_name'] . ' ' . $row['second_name']);
                $bookName = htmlspecialchars($row['book_name']);
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>$userFullName</td>";
                echo "<td>$bookName</td>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "</tr>";
            }
        ?>
    </table>

</div>

<hr>
<div class="inputText" style="align-items:start;">
    <p>Технічні повідомлення:</p>
</div>
<?php
    if (!empty($error_message)) {
    echo "<p class='resultText' style='color:red;'>$error_message</p>";
    }
    if (!empty($success_message)) {
    echo "<p class='resultText' style='color:green;'>$success_message</p>";
    }
?>
</body>
</html>
