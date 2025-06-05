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

    if (isset($_POST['add_author'])) {
        $first = trim($_POST['first_name']);
        $second = trim($_POST['second_name']);

        if ($first !== '' && $second !== '') {
            $insert = pg_query_params($conn,"INSERT INTO authors (first_name, second_name) VALUES ($1, $2)",array($first, $second));
            
            if ($insert) {
                $_SESSION['success_message'] = "Автора додано.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
            else {
                $_SESSION['error_message'] = "Помилка при додаванні.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }

        }
    }

    if (isset($_POST['delete_author'])) {
        $id = $_POST['delete_id'];

        $delete = pg_query_params($conn, "DELETE FROM authors WHERE id = $1", array($id));

            if ($delete && pg_affected_rows($delete) > 0) {
                $_SESSION['success_message'] = "Автора видалено.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            } 
            else {
                $_SESSION['error_message'] = "Автора з таким ID не існує.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }
    }

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Керування авторами</title>
    <style>
         table {
            margin-top: 10px;
            width: 50%;
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
        th:nth-child(2), td:nth-child(2) { width: 30%; } 
        th:nth-child(3), td:nth-child(3) { width: 30%; } 
        th:nth-child(4), td:nth-child(4) { width: 30%; }
        .titleBox{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .titleText{
            flex: 1;
            text-align: center;
            font-size: 50px;
            font-family: 'Trebuchet MS';
            margin-top: 0px;
            margin-bottom: 0px;
        }
        .titleBox button {
            height: 40px;
            font-size: large;
            border-radius: 10px;
            cursor: pointer;
        }
        .actionText{
            font-size: xx-large;
            font-family: 'Trebuchet MS';
            margin-top: 0px;
            margin-bottom: 0px;
        }
        .inputText{
            font-family: 'Trebuchet MS';
            font-size: large;
            margin: 10px;
        }
        .submitButton{
            height: 30px;
            margin: 5px;
            border-radius: 10px;
            text-align: center;
        }
        .inputRow{
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .inputRow input{
            width: 200px;
            height: 25px;
            font-size: large;
            font-family: 'Trebuchet MS';
        }
        .resultText{
            font-family: 'Trebuchet MS';
            font-size: normal;
            margin-left: 10px; 
        }

    </style>
</head>
<body>
    <div class="titleBox">
        <p class="titleText">Керування авторами</p>
        <a href="main.php"><button>На головну</button></a>
    </div>

    <details style="margin-top: 10px; margin-bottom: 10px;">
        <summary class="actionText">Додати автора</summary>
            <form method="post" class="inputText">
                <div class="inputRow">
                    <label style="width: 100px;">Ім’я: </label>
                    <input type="text" name="first_name" required>

                </div>
                <div class="inputRow">
                    <label style="width: 100px;">Прізвище:</label>
                    <input type="text" name="second_name" required>

                </div>
                <input class="submitButton" style="width: 300px;" type="submit" name="add_author" value="Додати автора">
            </form>
    </details>
    
     <details style="margin-top: 10px; margin-bottom: 10px;">
        <summary class="actionText">Видалити автора</summary>
            <form method="post" class="inputText">
                <div class="inputRow">
                    <label style="width: 100px;">ID:</label>
                    <input type="number" min="0" name="delete_id" required>
                </div>
                <input class="submitButton" style="width: 300px;" type="submit" name="delete_author" value="Видалити автора"> 
            </form>
    </details>
    
    <hr>
    <div>
        <p class="actionText">Список авторів</p>
        <table>
           <tr><th>ID</th><th>Ім’я</th><th>Прізвище</th></tr>
            <?php
                $res = pg_query($conn, "SELECT * FROM authors ORDER BY id");

                while ($row = pg_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['second_name']) . "</td>";
                    echo "</tr>";
                }
            ?>
        </table>
    </div>
    <hr>
    <div class="inputText">
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
