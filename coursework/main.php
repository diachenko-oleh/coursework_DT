<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>eLibrary</title>
    <style>
        .titleBox{
            text-align: center;
            align-items: center;
            display: flex;
            flex-direction: column;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .title{
            font-size: 75px;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif ;
            display: inline-flex;
            flex-direction: row;
            margin: 0px;
        }
        .titleText{
            margin-top: 0px;
            margin-bottom: 0px;
            font-size: large;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif ;
        }
        .buttonBox{
            text-align: center;
            align-items: center;
            display: flex;
            flex-direction: column;
        }
        .button {
        cursor: pointer;
        text-align: center;
        align-items: center;
        height: 100px;
        font-size: large;
        font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif ;
        width: 350px;
        border-radius: 100px;
        
        }
        .mainButton{
        cursor: pointer;
        margin: 20px; 
        padding: 20px; 
        }
         .normalButton{
        cursor: pointer;
        margin: 10px; 
        padding: 20px; 
        }

    </style>
</head>
<body>
    <div class="titleBox">
         <p class="title">
            <span style="font-style: oblique;">e</span>
            <span style="font-weight: normal;">Library</span>
        </p>
        <p class="titleText">Сервіс для каталогізації, пошуку та контролю доступу електронних навчальних та художніх матеріалів</p>
    </div>

   <div class="buttonBox">
        <button class="button mainButton" onclick="location.href='new_rental.php'">Отримати книгу</button>    
        <button class="button mainButton" onclick="location.href='library.php'">Переглянути книги</button>
   </div>
    
   <hr>

   <div>
    <p class="titleText" style="text-align: center; color: red; margin: 10px;">Адміністрування бази даних</p>
   </div>

    <div class="buttonBox">    
            <button class="button normalButton" onclick="location.href='users.php'">Перейти до керування користувачами</button>
            <button class="button normalButton" onclick="location.href='genres.php'">Перейти до керування жанрами</button>
            <button class="button normalButton" onclick="location.href='publishers.php'">Перейти до керування видавництвами</button>
            <button class="button normalButton" onclick="location.href='authors.php'">Перейти до керування авторами</button>
            <button class="button normalButton" onclick="location.href='books.php'">Перейти до керування книгами</button>
            <button class="button normalButton" onclick="location.href='rentals.php'">Перейти до керування орендами</button>
    </div>
</body>
</html>
