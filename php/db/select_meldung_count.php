<!-- Dieses SQL Statement gibt die Anzahl der unbearbeiteten Meldungen aus -->
<?php
$pdo = new PDO('mysql:host=localhost;dbname=tbec', 'root', '');
$statement = $pdo->prepare(
       "SELECT count(id)
         FROM meldung m
         WHERE status = 0"
);
$result = $statement->execute();
$meldungCount = $statement->fetch();
$pdo = NULL;