﻿<?php
include('../php/sessioncheck.php');
$activeHead = "cocktail";
// Musste nach unten geschoben werden = $_SESSION['source']= "Location: ../site/cocktail_details.php?cock_id=" . $cockFetch["id"];

include('../php/db/_openConnection.php');


$statement = $pdo->prepare(
					"SELECT 
						c.id as id,
						c.name as name,
						c.beschreibung as beschreibung,
						c.img as img
					FROM cock c
					WHERE c.id = :cock_id"
);
$result = $statement->execute(array('cock_id' => $_GET['cock_id']));
$cockFetch = $statement->fetch();

// UFF
$_SESSION['source'] = "Location: ../site/cocktail_details.php?cock_id=" . $cockFetch["id"];


$bew = false;
$bew_success = false;
$message = 'Fehler';

if (isset($_GET['etab_zugeordnet'])) {

	$etab_id = $_POST['dasZugeordnete'];

	$statementinsert = $pdo->prepare(
								"INSERT 
								INTO cock_etab(etab_id, cock_id, preis) 
								VALUES(:etab_id, :cock_id, :preis)");					
	$result = $statementinsert->execute(array('etab_id' => $etab_id, 'cock_id' => $_GET['cock_id'], 'preis' => $_POST['preis_cock']));
	$insertErr = $statementinsert->fetch();

	if (!$insertErr) {
		$message = "Erfolgreich hinzugefügt.";
	} else {
		$message = "Insert Error festgestelllt.";
	}
}

if (isset($_GET['bew_abgeben']) && $angemeldet) {
	$bew = true;
	$bew_etab = $_POST['eta'];
	$bew_wert = $_POST['wert'];
	$bew_kommentar = $_POST['kommentar'];

	$statement = $pdo->prepare("
						SELECT * 
						FROM bew_cock 
						WHERE user_id=:user_id 
						  AND etab_id=:etab_id 
						  AND cock_id=:cock_id");
	$result = $statement->execute(array('user_id' => $_SESSION['userid'], 'etab_id' => $bew_etab, 'cock_id' => $_GET['cock_id']));
	$bew_vorhanden = $statement->fetch();

	if ($bew_vorhanden == true) {
		$statement = $pdo->prepare("
							UPDATE bew_cock 
							SET wert=:wert, 
								text=:kommentar 
							WHERE user_id=:user_id 
							  AND etab_id=:etab_id 
							  AND cock_id=:cock_id");
		$result = $statement->execute(array('wert' => $bew_wert, 'kommentar' => $bew_kommentar, 'user_id' => $_SESSION['userid'], 'etab_id' => $bew_etab, 'cock_id' => $_GET['cock_id']));
		$bew_success = $statement->fetch();
		$message = 'Ihre Bewertung wurde Aktualisiert!';
	} else {
		$statement = $pdo->prepare("
							INSERT 
							INTO bew_cock (user_id, etab_id, cock_id, wert, text) 
							VALUES (:user_id, :etab_id, :cock_id, :wert, :kommentar)");
		$result = $statement->execute(array('wert' => $bew_wert, 'kommentar' => $bew_kommentar, 'user_id' => $_SESSION['userid'], 'etab_id' => $bew_etab, 'cock_id' => $_GET['cock_id']));
		$bew_success = $statement->fetch();
		$message = 'Ihre Bewertung wurde gespeichert!';
	}
}

$statement = $pdo->prepare("
					SELECT
						e.id as id,
						e.name as name,
						e.ort as ort,
						ce.preis as preis,
						AVG(bc.wert) as wert
					FROM cock_etab ce
						JOIN etab e ON
							e.id = ce.etab_id
						LEFT JOIN bew_cock bc ON
							ce.cock_id = bc.cock_id AND
							e.id = bc.etab_id
					WHERE ce.cock_id = :cock_id
					GROUP BY
						e.id,
						e.name,
						e.ort,
						ce.preis,
						ce.cock_id");
$result = $statement->execute(array('cock_id' => $_GET['cock_id']));
$etabFetch = $statement->fetchAll();

$statement = $pdo->prepare("
					SELECT
						u.username as username,
						u.id as userid,
						bc.text as text,
						bc.wert as wert,
						bc.timestamp as ts,
						e.name as etab_name,
						bc.etab_id as etab_id
					FROM bew_cock bc
						JOIN user u 
							ON bc.user_id = u.id
						JOIN etab e 
							ON bc.etab_id = e.id
					WHERE bc.cock_id = :cock_id
					ORDER BY e.name");
$result = $statement->execute(array('cock_id' => $_GET['cock_id']));
$bewFetch = $statement->fetchAll();



$statement = $pdo->prepare("
					SELECT 
						e.id as id, 
						e.name as name, 
						e.ort as ort
					FROM etab e
					JOIN cock_etab ce ON
						e.id = ce.etab_id
					WHERE ce.cock_id = :cock_id");
$result = $statement->execute(array('cock_id' => $_GET['cock_id']));
$allEtaFetch = $statement->fetchAll();


$statement = $pdo->prepare("SELECT name FROM cock WHERE id =:cockid");
$result = $statement->execute(array('cockid' => $_GET["cock_id"]));
$cockDaten = $statement->fetch();

$statementEtabs = $pdo->prepare("SELECT id,
												name,
												ort
										FROM etab");
$etabResult = $statementEtabs->execute();
$allEtabsPos = $statementEtabs->fetchAll();

$statement = $pdo->prepare("SELECT etab_id FROM cock_etab WHERE cock_id =:cockid");
$result = $statement->execute(array('cockid' => $_GET["cock_id"]));
$notPossibleEtaIds = $statement->fetchAll();


?>
<!doctype html>
<html lang="de">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Felix Pause, Cedrick Bargel, Philipp Potraz">
	<link rel="shortcut icon" type="image/x-icon" href="../res/favicon.ico">
	<title>Cocktail - Details</title>

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
		<div class="mt-5 ml-5 mr-5">
			<?php
			if ($bew == true && $bew_success == false) {
				echo '<div class="alert alert-info ct-text-center mb-4" role="info">';
				echo $message;
				echo '</div>';
			}
			?>
			<div class="card mb-3" width="100%" style="max-height: 360px;">
				<div class="row no-gutters">
					<div class="col-md-2">
						<?php
						if ($cockFetch["img"] == null)
							echo '<img src="../res/placeholder_no_image.svg" class="card-img-top">';
						else
							echo '<img src="../php/get_img.php?cock_id=' . $cockFetch["id"] . '" class="card-img-top">';
						?>
					</div>
					<div class="col-md-10">
						<div class="card-body d-flex flex-column" style="height: 230px;">
							<div>
								<h1 class="card-title"> <?php echo $cockFetch["name"]; ?> </h1>
								<hr>
							</div>
							<div>
								<p class="card-text"> <?php echo $cockFetch["beschreibung"]; ?> </p>
							</div>
							<div class="mt-auto">
								<?php echo '							 	
								<form action="?cock_id=' . $_GET['cock_id'] . '&etab_zugeordnet=1" method="POST">
								<label for="etab_zugeordnet">Cocktail einem Etablissement zuordnen:</label>
									<div class="form-row">
										<div class="col-4">
										<select class="custom-select" name="dasZugeordnete" id="etab_zugeordnet">';
								for ($i = 0; $i < count($allEtabsPos); $i++) {
									$isValid = true;

									for ($j = 0; $j < count($notPossibleEtaIds); $j++) {
										if ($allEtabsPos[$i][0] == $notPossibleEtaIds[$j][0]) {
											$isValid = false;
										}
									}

									if ($isValid == true) {
										echo '<option value="' . $allEtabsPos[$i][0] . '">' . $allEtabsPos[$i][1] . ', ' . $allEtabsPos[$i][2] . '</option>';
									}
								}
								echo '
										</select>
										</div>
										<div class="col-4">
										<input type="text" maxlength="10" class="form-control" name="preis_cock" id="preis_cock" placeholder="Preis" required>
										</div>
										<div class="col-auto">
											<button type="submit" class="btn btn-primary">Hinzuf&uuml;gen</button>
										</div>
									</div>
								</form>'; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card card-body">
				<ul class="nav nav-pills flex-column flex-sm-row" id="cockDetail-tab" role="tablist">
					<li class="flex-sm-fill text-sm-center nav-item">
						<a class="nav-link active" id="cocktailKarte-tab" data-toggle="pill" href="#cocktailKarte" role="tab" aria-controls="cocktailKarte" aria-selected="true">Cocktailkarte</a>
					</li>
					<li class="flex-sm-fill text-sm-center nav-item">
						<a class="nav-link" id="bewertungen-tab" data-toggle="pill" href="#bewertungen" role="tab" aria-controls="bewertungen" aria-selected="false">Bewertungen</a>
					</li>
					<li class="flex-sm-fill text-sm-center nav-item">
						<a class="nav-link" id="bewerten-tab" data-toggle="pill" href="#bewerten" role="tab" aria-controls="bewerten" aria-selected="false">Bewerten!</a>
					</li>
				</ul>
				<hr>
				<div class="tab-content" id="cockDetail-tabContent">
					<div class="tab-pane fade show active" id="cocktailKarte" role="tabpanel" aria-labelledby="cocktailKarte-tab">
						<?php echo '
						<table class="table">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Etablissement</th>
									<th scope="col">Ort</th>
									<th scope="col">Preis</th>
									<th scope="col">Durchschnittsbewertung</th>
								</tr>
							</thead> 
							<tbody>';

						for ($i = 0; $i < count($etabFetch); $i++) {
							echo '<tr>';
							echo '<th scope="row">' . ($i + 1) . '</th>';
							echo '<td>  <a class="" href="etablissement_details.php?etab_id= ' . $etabFetch[$i]["id"] . '">' . $etabFetch[$i]["name"] . '</a></td>';
							echo '<td>' . $etabFetch[$i]["ort"] . '</td>';
							echo '<td>' . $etabFetch[$i]["preis"] . '</td>';
							echo '<td>' . $etabFetch[$i]["wert"] . '</td>';
							echo '</tr>';
						}
						echo '
							</tbody>
						</table>';
						?>
					</div>
					<div class="tab-pane fade" id="bewertungen" role="tabpanel" aria-labelledby="bewertungen-tab">
						<?php echo '
						<table class="table">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Nutzername</th>
									<th scope="col">Etablissement</th>
									<th scope="col">Bewertung</th>
									<th scope="col">Wert</th>
									<th scope="col">Zeitpunkt</th>
								</tr>
							</thead> 
							<tbody>';

						for ($i = 0; $i < count($bewFetch); $i++) {
							echo '<tr>';
							echo '<th scope="row">' . ($i + 1) . '</th>';
							echo '<td> <a class="" href="../site/profil_other.php?showUser=' . $bewFetch[$i]["userid"] . '">' . $bewFetch[$i]["username"] . '</a></td>';
							echo '<td> <a class="" href="../site/etablissement_details.php?etab_id= ' . $bewFetch[$i]["etab_id"] . '">' . $bewFetch[$i]["etab_name"] . '</a></td>';
							echo '<td>' . $bewFetch[$i]["text"] . '</td>';
							echo '<td>' . $bewFetch[$i]["wert"] . '</td>';
							echo '<td>' . $bewFetch[$i]["ts"] . '</td>';
							echo '</tr>';
						}
						echo '</tbody></table>';
						?>
					</div>
					<div class="tab-pane fade" id="bewerten" role="tabpanel" aria-labelledby="bewerten-tab">
						<?php
						if ($angemeldet) {
							if ($bew_success == false) {
								echo '
								<form class="mr-2 ml-2 mt-2" action="?cock_id=' . $_GET['cock_id'] . '&bew_abgeben=1" method="post">
									<div class="form-group">
										<label for="eta">Wo getrunken?</label>
										<!--<input type="text" class="form-control" id="bew_etab" placeholder="Etablissement ausw&auml;hlen" name="eta">-->
										<select class="custom-select" name="eta" id="bew_etab">';
								for ($i = 0; $i < count($allEtaFetch); $i++) {
									echo '<option value="' . $allEtaFetch[$i]["id"] . '">' . $allEtaFetch[$i]["name"] . ', ' . $allEtaFetch[$i]["ort"] . '</option>';
								}
								echo	'</select>
									</div>
									<div class="form-group">
										<label for="wert">Wie war er?</label>
										<!--<input type="text" class="form-control" id="bew_wert" placeholder="0 Sterne" name="wert">-->
										<select class="custom-select" name="wert" id="bew_etab">
											<option value="1">★☆☆☆☆</option>
											<option value="2">★★☆☆☆</option>
											<option value="3">★★★☆☆</option>
											<option value="4">★★★★☆</option>
											<option value="5">★★★★★</option>
										</select>
									</div>
									<div class="form-group">
										<label for="kommentar">Kommentar!</label>
										<textarea class="form-control" id="bew_kommentar" aria-label="Beispieltext" name="kommentar"></textarea>
									</div>
									<button type="submit" class="btn btn-primary mt-2">Bewertung abschicken!</button>
								</form>';
							} else {
								echo '<h2 class="ml-4 ct-text-center">Bewertung erfolgreich abgegeben!</h2>';
							}
						} else {
							echo '<h2 class="ml-4 ct-text-center">Bitte zuerst <a class="" href="signin.php">Anmelden</a>.</h2>';
						}
						?>
					</div>
				</div>
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