<?php

/*
 * For Getting Email Form Data
 * note: wordpress $wpdb won't work since form will be outside of wordpress
 * and won't recognize the database connection
 */



// configuration
$dbtype = "sqlite";
$dbhost = "localhost";
$dbname = "vidhire_wrdp1";
$dbuser = "vidhire_wrdp1";
$dbpass = "O0hOt9g2yyCivgbo";

// database connection
$link = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);


if (!$link) {

    die('Could not connect: ' . mysql_error());
}

global $wpdb;

$resume_id = $_POST['resume_id'];
$reference_name = $_POST['reference_name'];
$performance = $_POST['performance'];
$attitude = $_POST['attitude'];
$dependability = $_POST['depend'];
$team_player = $_POST['team_player'];
$learning_speed = $_POST['learning_speed'];
$flexibility = $_POST['flexibility'];
$creativity = $_POST['creativity'];

echo $team_player;

$query = "SELECT resume_id,reference_name FROM wp_references_responses WHERE resume_id in (?) AND reference_name in (?)";

$s = $link->prepare($query);
$s->execute(array(
    $resume_id,
    $reference_name
));

$result = $s->fetch(PDO::FETCH_ASSOC);

if ($s->rowCount() == 0) {

    $sql = "INSERT INTO wp_references_responses (resume_id,reference_name,performance,attitude,dependability,team_player,learning_speed,flexibility,creativity) VALUES (?,?,?,?,?,?,?,?,?)";

    $q = $link->prepare($sql);
    $q->execute(array(
        $resume_id,
        $reference_name,
        $performance,
        $attitude,
        $dependability,
        $team_player,
        $learning_speed,
        $flexibility,
        $creativity
    ));
} else {

    $sql = "UPDATE wp_references_responses SET performance=?,attitude=?,dependability=?,team_player=?,learning_speed=?,flexibility=?,creativity=? WHERE resume_id in (?) AND reference_name in (?)";

    $q = $link->prepare($sql);
    $q->execute(array(
        $performance,
        $attitude,
        $dependability,
        $team_player,
        $learning_speed,
        $flexibility,
        $creativity,
        $resume_id,
        $reference_name,
    ));
}
