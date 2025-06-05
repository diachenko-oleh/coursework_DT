<?php
    session_start(); 
    include 'database.php';

    $error_message = '';
    $success_message = '';
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

    if (isset($_POST['add_genre'])) {
        $name = trim($_POST['genre_name']);

        if ($name !== '') {
            $insert = pg_query_params($conn,"INSERT INTO genres (name) VALUES ($1)", array($name));

            if ($insert) {
                $_SESSION['success_message'] = "Жанр додано.";
            } else {
                $error = pg_last_error($conn);
                if (strpos($error, 'unique') !== false) {
                    $_SESSION['error_message'] = "Такий жанр вже існує.";
                } else {
                    $_SESSION['error_message'] = "Помилка при додаванні.";
                }
            } 
        }
    }

    if (isset($_POST['delete_genre'])) {
        $id = $_POST['delete_id'];

        $delete = pg_query_params($conn,"DELETE FROM genres WHERE id = $1", array($id));

        if ($delete && pg_affected_rows($delete) > 0) {
            $_SESSION['success_message'] = "Жанр видалено.";
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $_SESSION['error_message'] = "Жанру з таким ID не існує.";
        }

        
    }
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Керування жанрами</title>
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
        <p class="titleText">Керування жанрами</p>
        <a href="main.php"><button>На головну</button></a>
    </div>

    <details style="margin-top: 10px; margin-bottom: 10px;">
        <summary class="actionText">Додати жанр</summary>
            <form method="post" class="inputText">
                <div class="inputRow">
                    <label style="width: 100px;">Назва жанру:</label>
                    <input type="text" name="genre_name" required>

                </div>
                <input class="submitButton" style="width: 300px;" type="submit"  name="add_genre" value="Додати жанр">
            </form>
    </details>

    <details style="margin-top: 10px; margin-bottom: 10px;">
        <summary class="actionText">Видалити жанр</summary>
            <form method="post" class="inputText">
                <div class="inputRow">
                    <label style="width: 100px;">ID жанру:</label>
                    <input type="number" min="0" name="delete_id" required>
                </div>
                <input class="submitButton" style="width: 300px;" type="submit" name="delete_genre" value="Видалити жанр"> 
            </form>
    </details>
    <hr>
    <div>
        <p class="actionText">Список жанрів</p>
        <table>
            <tr><th>ID</th><th>Назва</th></tr>
            <?php
                $res = pg_query($conn, "SELECT * FROM genres ORDER BY id");
                while ($row = pg_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
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
