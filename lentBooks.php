<!DOCTYPE html>
<html>
<head>
    <title>Lent Books</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once('db.php');
$db = DB::getConnection();

$sth = $db->prepare("SELECT Lent_Books.id AS lentBookId, Books.id,
    Stored_Books.store_id, Books.title, Users.first_name, Users.last_name,
    Lent_Books.taken, Lent_Books.deadline, Lent_books.returned
    FROM Lent_Books JOIN Stored_Books
    ON Lent_Books.stored_book_id=Stored_Books.id
    JOIN Users
    ON Lent_Books.user_id=Users.id
    JOIN Books
    ON Stored_Books.book_id=Books.id");
$sth->execute();
$lentBooks = $sth->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Lent Books</h2>
    <p>Contextual classes can be used to color table rows or table cells. The classes that can be used are: .active,
        .success, .info, .warning, and .danger.</p>
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Book ID</th>
            <th>Store ID
            <th>Book Title</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Taken</th>
            <th>Deadline</th>
            <th>Returned</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($lentBooks as $lentBook) {
            ?>
            <tr
                <?php // this should be done using JavaScript
                if (strtotime($lentBook['returned']) == null && strtotime('now') > strtotime($lentBook['deadline'])) { ?>
                    class="danger" <?php } elseif (strtotime($lentBook['returned']) == null && strtotime('now') <= strtotime($lentBook['deadline'])) { ?>
                    class="active" <?php } elseif (strtotime($lentBook['returned']) > strtotime($lentBook['deadline'])) { ?>
                    class="warning" <?php } else {
                    ?> class="success" <?php } ?> >

                <?php foreach ($lentBook as $item) { ?>
                    <td><?php echo $item; ?></td>
                <?php }
                if($lentBook['returned'] == null){ ?>
                    <td><a href="returnBook.php?lentBookId=<?php echo $lentBook['lentBookId']; ?>">Return the book</a></td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<a href="lendBook.php">Lend book</a>
<footer><a href="index.php">Go home</a></footer>
<?php include('footer.php'); ?>