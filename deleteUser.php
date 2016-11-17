<!DOCTYPE html>
<html>
<head>
    <title>Create/Edit User</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css"/>
</head>
<body>

<?php
include_once('db.php');
$db = DB::getConnection();

if (isset($_GET['id'])) {
    $sth = $db->prepare("DELETE FROM Users WHERE id=?");
    $sth->execute([$_GET['id']]);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?deleteSuccess=1');
    die;
}

if (isset($_GET['deleteSuccess']) && $_GET['deleteSuccess'] == 1) {
    ?>
    <div class="alert alert-success"><?php echo 'User successfully deleted'; ?> </div>
    <footer><a href="index.php">Go home</a></footer>
<?php }

include('footer.php'); ?>
