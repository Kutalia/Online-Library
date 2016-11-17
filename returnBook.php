<!DOCTYPE html>
<html>
<head>
    <title>Lend book</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css"/>
</head>
<body>

<?php
/**
 * Created by PhpStorm.
 * User: Kote Kutalia
 * Date: 11/9/2016
 * Time: 5:19 PM
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['returnBookSuccess']) && $_GET['returnBookSuccess'] == 1)
{
    ?>
    <div class="alert alert-success"><?php echo "Book successfully returned"; ?> </div>
    <footer><a href="lendBook.php">Lend another book</a></footer>
    <footer><a href="lentBooks.php">All lent books</a></footer>
    <?php
}

elseif(isset($_GET['lentBookId']) && filter_var($_GET['lentBookId'], FILTER_VALIDATE_INT)) {
    include_once('db.php');
    $db = DB::getConnection();

    $returned = date("Y-m-d");

    $sth = $db->prepare("UPDATE Lent_Books
    SET returned=?
    WHERE id=? AND returned IS NULL");
    $sth->execute([$returned, $_GET['lentBookId']]);

    $sth2 = $db->prepare("SELECT stored_book_id
    FROM Lent_Books
    WHERE id=?");
    $sth2->execute([$_GET['lentBookId']]);
    $storedBookId = $sth2->fetch();

    $sth3 = $db->prepare("UPDATE Stored_Books
    SET quantity=quantity+1
    WHERE id=?");
    $sth3->execute([$storedBookId[0]]);

    header("Location: " . $_SERVER['PHP_SELF'] . '?returnBookSuccess=1');
} ?>

</body>
</html>