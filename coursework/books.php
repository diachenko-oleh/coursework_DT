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
    $genres = pg_query($conn, "SELECT id, name FROM genres ORDER BY name");
    $publishers = pg_query($conn, "SELECT id, name FROM publishers ORDER BY name");
    $authors = pg_query($conn, "SELECT id, first_name, second_name FROM authors ORDER BY second_name");
   

    if (isset($_POST['add_book'])) {
        $name = $_POST['name'];
        $genre_id = $_POST['genre_id'];
        $publisher_id = $_POST['publisher_id'];
        $release_date = $_POST['release_date'];
        $author_id = $_POST['author_id'];

        $insert = pg_query_params($conn, "INSERT INTO books (name, genre_id, publisher_id, release_date, author_id) 
        VALUES ($1, $2, $3, $4, $5)", array($name, $genre_id, $publisher_id, $release_date, $author_id));

        if ($insert) {
            $_SESSION['success_message'] = "Книгу додано.";
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $_SESSION['error_message'] = "Помилка при додаванні.";
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    if (isset($_POST['delete_book'])) {
    $id = $_POST['delete_id'];
    $delete = pg_query_params($conn, "DELETE FROM books WHERE id = $1", array($id));

    if ($delete && pg_affected_rows($delete) > 0) {
        $_SESSION['success_message'] = "Книгу видалено.";
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        $_SESSION['error_message'] = "Книги з таким ID не існує.";
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }
    }
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Керування книгами</title>
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
        .inputRow select{
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
        <p class="titleText">Керування книгами</p>
        <a href="main.php"><button>На головну</button></a>
    </div>


    <details style="margin-top: 10px; margin-bottom: 10px;">
        <summary class="actionText">Додати книгу</summary>
        <form method="post" class="inputText">
            <div class="inputRow">
                <label style="width: 120px;">Назва: </label>
                <input type="text" name="name" required>
            </div>

            <div class="inputRow">
                <label style="width: 120px;">Жанр: </label>
                <select name="genre_id" required>
                    <?php while ($g = pg_fetch_assoc($genres)) {
                        echo "<option value='{$g['id']}'>" . htmlspecialchars($g['name']) . "</option>";
                    } ?>
                </select>
            </div>

            <div class="inputRow">
                <label style="width: 120px;">Автор: </label>
                <select name="author_id" required>
                    <?php while ($a = pg_fetch_assoc($authors)) {
                        $full = htmlspecialchars($a['first_name'] . ' ' . $a['second_name']);
                        echo "<option value='{$a['id']}'>$full</option>";
                    } ?>
                </select>
            </div>

            <div class="inputRow">
                <label style="width: 120px;">Видавництво: </label>
                <select name="publisher_id" required>
                    <?php while ($p = pg_fetch_assoc($publishers)) {
                        echo "<option value='{$p['id']}'>" . htmlspecialchars($p['name']) . "</option>";
                    } ?>
                </select>
            </div>

            <div class="inputRow">
                <label style="width: 120px;">Дата виходу:  </label>
                <input type="date" name="release_date" required>
            </div>

            <input class="submitButton" style="width: 320px;" type="submit" name="add_book" value="Додати книгу">
        </form>
    </details>

    <details style="margin-top: 10px; margin-bottom: 10px;">
        <summary class="actionText">Видалити книгу</summary>
            <form method="post" class="inputText">
                <div class="inputRow">
                    <label style="width: 100px;">ID книги:</label>
                    <input type="number" min="0" name="delete_id" required>
                </div>
                <input class="submitButton" style="width: 300px;" type="submit" name="delete_book" value="Видалити книгу"> 
            </form>
    </details>

    <hr>
    <div>
        <p class="actionText">Список книг</p>
        <table>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Жанр</th>
                <th>Автор</th>
                <th>Видавництво</th>
                <th>Дата виходу</th>
            </tr>
            <?php
                $query = 
                    "SELECT
                    b.id,
                    b.name,
                    g.name AS genre,
                    a.first_name || ' ' || a.second_name AS author,
                    p.name AS publisher,
                    b.release_date
                    FROM books b
                    LEFT JOIN genres g ON b.genre_id = g.id
                    LEFT JOIN authors a ON b.author_id = a.id
                    LEFT JOIN publishers p ON b.publisher_id = p.id
                    ORDER BY b.id";

                $res = pg_query($conn, $query);

                while ($row = pg_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['genre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['author']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['publisher']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['release_date']) . "</td>";
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
