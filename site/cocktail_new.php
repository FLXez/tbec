<?php
include('../php/sessioncheck.php');
$activeHead = "cocktail";

if($angemeldet){
$pdo = new PDO('mysql:host=localhost;dbname=dbprog','root','');

	if(isset($_POST['upload'])){
	$file_name = $_FILES['file']['name'];
	$file_type = $_FILES['file']['type'];
	$file_size = $_FILES['file']['size'];
	$file_tem_loc = $_FILES['file']['tmp_name'];
	$file_store = "../z_testkram/".$file_name;

	
	move_uploaded_file($file_tem_loc, $file_store);

	}

	if(isset($_GET['cocktailZuEtabAdd'])){
	
	}

	if(isset($_GET['newCock'])){
	
	}
	
	
}

?>
<!doctype html>
<html lang="de">



<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Felix Pause, Cedrick Bargel, Philipp Potraz">
    <title>Cocktail - Neuer Cocktail</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- FontAwesome (icons) -->
    <script defer src="https://use.fontawesome.com/releases/v5.8.1/js/all.js" integrity="sha384-g5uSoOSBd7KkhAMlnQILrecXvzst9TdC09/VM+pjDTCM+1il8RHz5fKANTFFb+gQ" crossorigin="anonymous"></script>
    <!-- CSS Toolbox -->
    <link href="../css/csstoolbox.css" rel="stylesheet">
</head>

<body>
    <header>
        <?php
        include('../php/buildHeader.php');
        ?>
    </header>
    <main role="main">
    <main role="main">
        <div class="mt-5 ml-5 mr-5">
            <div class="card card-body">
                <h2 class="ml-4">Neuen Cocktail</h2>
				<?php
						if($angemeldet){
						echo '<div class="mr-5 ml-5 mt-2">

						
						<div class="form-group">
							<label for="nameCock"> Name </label>
							<input type="text" maxlength="50" class="form-control" id="nameCock" name="nameCock"  placeholder="Cocktail">
						</div>
						<div class="form-group">
							<label for="zutatenCock"> ZutatenCock </label>
							<input type="text" maxlength="50" class="form-control" id="zutatenCock" name="zutatenCock"  placeholder="Wie wir das l�sen bereden wir noch">
						</div>
						<form action="?" method="POST" enctype="multipart/form-data">
						<div class="form-group-2">
							<label for="image"> Bild </label>
							<br>
							<input type="file" name="file">
							<input type="submit" name="upload" value="Upload Image"> 
							
						</div>
						<form action="?cocktailZuEtabAdd=1" method="POST">
						<div class="form-group-3">
						<br>
							<label for="etab"> Etablissements </label>
							<select class="form-control" id="EtablissementAdder">
							<option>hier muss eine schleife �ber die datenbank gehen</option>
							<option>die itteriert �ber alle existenten etabs</option>
							<option> und l�sst den user diese dann hier einf�gen direkt so lol</option>
							</select>
							<button type="submit" class="btn btn-primary">Hinzuf�gen</button>
						</div>

						<form action="?newCock=1" method="post">
						<div class="form-group">
							<br>
							<button type="submit" class="btn btn-primary"> Erstellen</button>
						</div>
						
						';



						}else{
							echo '<h2 class="ml-4 ct-text-center">Bitte zuerst <a class="ct-panel-group" href="signin.php">Anmelden</a>.</h2>';
						}					
						?>
                <hr>
            </div>
        </div>
    </main>
    <hr class="ct-hr-divider ml-5 mr-5">
    <footer role="footer" class="container">
        <?php
        include('../php/buildFooter.php');
        ?>
    </footer>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>