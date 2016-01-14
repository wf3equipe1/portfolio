<?php//Page d'accueil du site
session_start();
require_once'composants/db.php';

?>

<!DOCTYPE html>
<html>
<head>
   <title>Page principale</title>
   <meta charset="utf-8">
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body>
    <main>
       <?php include_once 'composants/menugauche.php'; //MENU DE GAUCHE?>

       <section id="rightSide">
           <p>Hello world</p>
       </section>
    </main>
</body>
</html>
