<?php
    include 'database.php';
    $genres = pg_query($conn, "SELECT id, name FROM genres ORDER BY name");
    $publishers = pg_query($conn, "SELECT id, name FROM publishers ORDER BY name");
    $authors = pg_query($conn, "SELECT id, first_name, second_name FROM authors ORDER BY second_name");

    function isSelected($value, $selected) {
        return $value == $selected ? 'selected' : '';
    }
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Пошук книги</title>
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
        .titleBox input{
            height: 40px;
            font-size: large;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
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
            margin-top: -10px;
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
        <p class="titleText">Пошук книги за фільтрами</p>
        <a href="main.php"><button>На головну</button></a>
    </div>

    <div class="titleBox" style="justify-content: end;">
        <form method="post">
            <button type="submit" name="show_all">Показати всі книги</button>
        </form>
    </div>
    <hr>


    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: -10px;">
        <form method="post" class="inputText">
            <span class="actionText">Пошук за жанром</span><br><br>
            <div class="inputRow">
                <label style="width: 120px;">Жанр: </label>
                <select name="genre_id">
                    <option value="">Оберіть жанр</option>
                    <?php
                        $GenreOnly = $_POST['genre_id'] ?? '';
                        while ($g = pg_fetch_assoc($genres)) {
                            echo "<option value='{$g['id']}' " . isSelected($g['id'], $GenreOnly) . ">" . htmlspecialchars($g['name']) . "</option>";
                        }
                        pg_result_seek($genres, 0);
                    ?>
                </select>
                <br><br>
            </div>
        
            <details style="margin-top: 10px; margin-bottom: 10px;">
                <summary class="actionText">Пошук за кількома жанрами</summary>
                   <?php
                    $selectedGenres = $_POST['genre_ids'] ?? [];
                    while ($g = pg_fetch_assoc($genres)) {
                        $checked = in_array($g['id'], $selectedGenres) ? 'checked' : '';
                        echo "<label><input type='checkbox' name='genre_ids[]' value='{$g['id']}' $checked> " . htmlspecialchars($g['name']) . "</label><br>";
                    }
                    pg_result_seek($genres, 0);
                    ?>
                <br>
            </details>
        
            <span class="actionText">Пошук за автором</span><br><br>
            <div class="inputRow">
                <label style="width: 120px;">Автор: </label>
                <select name="author_id">
                    <option value="">Оберіть автора</option>
                    <?php
                        $AuthorOnly = $_POST['author_id'] ?? '';
                        while ($a = pg_fetch_assoc($authors)) {
                            echo "<option value='{$a['id']}' " . isSelected($a['id'], $AuthorOnly) . ">" . htmlspecialchars($a['first_name'] . ' ' . $a['second_name']) . "</option>";
                        }
                        pg_result_seek($authors, 0);
                    ?>
                </select>
                <br><br>
            </div>

            <span class="actionText">Пошук за видавництвом</span><br><br>
            <div class="inputRow">
                <label style="width: 120px;">Видавництво: </label>
                <select name="publisher_id">
                    <option value="">Оберіть видавництво</option>
                    <?php
                        $selectedPublisher = $_POST['publisher_id'] ?? '';
                        while ($p = pg_fetch_assoc($publishers)) {
                            echo "<option value='{$p['id']}' " . isSelected($p['id'], $selectedPublisher) . ">" . htmlspecialchars($p['name']) . "</option>";
                        }
                        pg_result_seek($publishers, 0);
                    ?>
                </select>
                <br><br>
            </div>
            
            <span class="actionText">Пошук за датою</span><br><br>
            <div class="inputRow">
                Від: <input style="margin: 10px;" type="date" name="from_date" value="<?= $_POST['from_date'] ?? '' ?>">
                До: <input style="margin: 10px;" type="date" name="to_date" value="<?= $_POST['to_date'] ?? '' ?>">
                <br><br>
            </div>
            
            
            
            <span class="actionText">Пошук по назві</span><br>
            <div class="inputRow">
                <input type="text" name="search_term" placeholder="Назва книги" value="<?= htmlspecialchars($_POST['search_term'] ?? '') ?>"><br><br>
                <br><br>
            </div>
            
            <span class="actionText">Сортування результатів</span><br><br>
            <div class="inputRow">Сортувати за:
                <select style="margin: 10px;" name="sort_by">
                    <option value="">Не сортувати</option>
                    <option value="books.name" <?= ($_POST['sort_by'] ?? '') == 'books.name' ? 'selected' : '' ?>>Назвою</option>
                    <option value="genres.name" <?= ($_POST['sort_by'] ?? '') == 'genres.name' ? 'selected' : '' ?>>Жанром</option>
                    <option value="publishers.name" <?= ($_POST['sort_by'] ?? '') == 'publishers.name' ? 'selected' : '' ?>>Видавництвом</option>
                    <option value="books.release_date" <?= ($_POST['sort_by'] ?? '') == 'books.release_date' ? 'selected' : '' ?>>Датою випуску</option>
                    <option value="authors.second_name" <?= ($_POST['sort_by'] ?? '') == 'authors.second_name' ? 'selected' : '' ?>>Автором</option>
                </select>

                <select name="sort_dir">
                    <option value="asc" <?= ($_POST['sort_dir'] ?? '') == 'asc' ? 'selected' : '' ?>>За зростанням</option>
                    <option value="desc" <?= ($_POST['sort_dir'] ?? '') == 'desc' ? 'selected' : '' ?>>За спаданням</option>
                </select>
                <br>
            </div>

            <div class="titleBox">
                <input type="submit" name="search_combined" value="Шукати">
            </div>

    </form>
        <div class="titleBox" style="justify-content: end; margin-top: 10px;">
            <button type="button" onclick="resetFilters()">Скинути фільтри</button>
        </div>
    </div>
<hr>

<?php
    $where = [];
    $params = [];
    $paramIndex = 1;
    $res = null;
    $sql = "SELECT books.id, books.name, genres.name AS genre_name, publishers.name AS publisher_name, books.release_date, authors.first_name || ' ' || authors.second_name AS author_name
            FROM books
            JOIN genres ON books.genre_id = genres.id
            JOIN publishers ON books.publisher_id = publishers.id
            JOIN authors ON books.author_id = authors.id";

    if (isset($_POST['show_all'])) {
        $res = pg_query($conn, $sql);
        echo "<h4>Усі книги:</h4>";
    } 
    elseif (isset($_POST['search_combined'])) {

        //ПОШУК
        if (!empty($_POST['genre_id'])) {               //жанр
            $where[] = "genre_id = $" . $paramIndex++;
            $params[] = $_POST['genre_id'];
        }
        if (!empty($_POST['publisher_id'])) {           //видавництво
            $where[] = "publisher_id = $" . $paramIndex++;
            $params[] = $_POST['publisher_id'];
        }
        if (!empty($_POST['author_id'])) {              //автор
            $where[] = "author_id = $" . $paramIndex++;
            $params[] = $_POST['author_id'];
        }
        if (!empty($_POST['genre_ids']) && is_array($_POST['genre_ids'])) {     //декілька жанрів
            $placeholders = [];
            foreach ($_POST['genre_ids'] as $gid) {
                $placeholders[] = "$" . $paramIndex++;
                $params[] = $gid;
            }
            $where[] = "genre_id IN (" . implode(",", $placeholders) . ")";
        }
        if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {         //проміжок дати
            $where[] = "release_date BETWEEN $" . $paramIndex++ . " AND $" . $paramIndex++;
            $params[] = $_POST['from_date'];
            $params[] = $_POST['to_date'];
        }
        if (!empty($_POST['search_term'])) {            //назва
            $where[] = "books.name ILIKE $" . $paramIndex++;
            $params[] = '%' . $_POST['search_term'] . '%';
        }

        //СОРТУВАННЯ
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $validSortColumns = ['books.name', 'genres.name', 'books.release_date', 'authors.second_name', 'publishers.name'];
        $sortBy = 'books.id';
        if (!empty($_POST['sort_by']) && in_array($_POST['sort_by'], $validSortColumns)) {
            $sortBy = $_POST['sort_by'];
        }
        $sortDir = (strtolower($_POST['sort_dir'] ?? '') === 'desc') ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $sortBy $sortDir";

        $res = pg_query_params($conn, $sql, $params);
        echo '<div class="inputText" style="align-items:start;">';
        echo '<p>Результати пошуку:</p>';
        echo '</div>';
    }


        if ($res && pg_num_rows($res) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Назва</th><th>Жанр</th><th>Видавництво</th><th>Дата</th><th>Автор</th></tr>";
            while ($row = pg_fetch_assoc($res)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['genre_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['publisher_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['release_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['author_name']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        elseif ($res){
            echo "<p>Нічого не знайдено.</p>";
        } 
?>


<script>
    function resetFilters() {
        const form = document.forms[1]; // друга форма на сторінці
        const inputs = form.querySelectorAll("input[type='text'], input[type='date']");
        inputs.forEach(input => input.value = '');
        const selects = form.querySelectorAll("select");
        selects.forEach(select => select.selectedIndex = 0);
        const checkboxes = form.querySelectorAll("input[type='checkbox']");
        checkboxes.forEach(cb => cb.checked = false);
    }   
</script>


</body>
</html>