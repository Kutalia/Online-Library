<!DOCTYPE html>
<html>
<head>
    <title>Lend book</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css"/>
</head>
<body>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once('db.php');
$db = DB::getConnection();

if(isset($_POST) && isset($_POST['privateId']) && isset($_POST['id']))
{
    $taken = date("Y-m-d");
    $deadline = date("Y-m-d", strtotime($taken) + 2592000);

    $sth = $db->prepare("SELECT Stored_Books.book_id AS id, Users.id AS userId
        FROM Stored_Books JOIN Users
        ON Users.private_id=?
        WHERE Stored_Books.book_id=?
        AND Stored_Books.store_id=?");
    $sth->execute([$_POST['privateId'], $_POST['id'], $_POST['storeId']]);
    $lentBook = $sth->fetch(PDO::FETCH_ASSOC); // not fetchAll, we just need a single row
    //echo '<pre>'; var_dump($_POST);
    //echo '<pre>'; var_dump($lentBook);

    try{
    $sth2 = $db->prepare("INSERT INTO Lent_Books(stored_book_id, user_id, taken, deadline)
        VALUES(?,?,?,?)");
    $sth2->execute([$lentBook['id'], $lentBook['userId'], $taken, $deadline]);

    $sth3 = $db->prepare("UPDATE Stored_Books
    SET quantity=quantity-1
    WHERE store_id=?
    AND book_id=?");
    $sth3->execute([$_POST['storeId'], $_POST['id']]);

    header("Location: " . $_SERVER['PHP_SELF'] . '?lendBookSuccess=1');
    }

    catch(Exception $ex){
        ?>
        <div class="alert alert-warning"><?php echo "Did you already lend that book?"; ?> </div>
        <a href="<?php echo $_SERVER['PHP_SELF'] ?>" >Go, lend another book</a>
        <?php
    }
}

else
{
    if(isset($_GET['lendBookSuccess']) && $_GET['lendBookSuccess']==1)
    {
        ?>
        <div class="alert alert-success"><?php echo "You've successfully lent a book"; ?> </div>
        <?php
    }

    $sth = $db->prepare("SELECT first_name AS firstName, last_name AS lastName, private_id AS privateId
        FROM Users");
    $sth->execute();
    $users = $sth->fetchAll(PDO::FETCH_ASSOC);

    $sth2 = $db->prepare("SELECT DISTINCT Books.id, Books.title, Authors.name AS author
        FROM Books JOIN Authors
        ON Books.author_id = Authors.id
        JOIN Stored_Books
        ON Books.id=Stored_Books.book_id");
    $sth2->execute();
    $storedBooks = $sth2->fetchAll(PDO::FETCH_ASSOC);

    $sth3 = $db->prepare("SELECT DISTINCT Stores.id, Stores.name, Countries.name AS country, Cities.name AS city
        FROM Stores JOIN Countries
        ON Stores.country_id=Countries.id
        JOIN Cities
        ON Stores.city_id=Cities.id
        JOIN Stored_Books
        ON Stores.id=Stored_Books.store_id");
    $sth3->execute();
    $stores = $sth3->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h1>Lend a book</h1>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="container">
      <div class="form-group">
        <label for="privateId">Lend as</label>
            <select id="privateId" name="privateId" class="form-control" required="required">
                <option disabled="disabled" selected="selected"/>
            <?php
            foreach($users as $user)
            {
            ?>
                <option value="<?php echo $user['privateId']; ?>">
                <?php echo $user['firstName'] . ' ' . $user['lastName'] . ', id: ' . $user['privateId']; ?></option>
            <?php } ?>
            </select>
      </div>
      <div class="form-group">
        <label for="id">Book:</label>
            <select id="id" name="id" class="form-control">
                <option disabled="disabled" selected="selected"/>
            <?php
            foreach($storedBooks as $storedBook)
            {
            ?>
                <option value="<?php echo $storedBook['id']; ?>">
                <?php echo $storedBook['title'] . ' by ' . $storedBook['author']; ?></option>
            <?php } ?>
            </select>
       </div>
      <div class="form-group">
        <label for="storeId">Store:</label>
            <select id="storeId" name="storeId" class="form-control">
                <option disabled="disabled" selected="selected"/>
            <?php
            foreach($stores as $store)
            {
            ?>
                <option value="<?php echo $store['id']; ?>">
                <?php echo $store['id'] . ') ' . $store['name'] . ' (' . $store['city'] . ', ' . $store['country'] . ')'; ?></option>
            <?php } ?>
            </select>
       </div>
       <button type="reset" class="btn btn-default">Reset</button>
      <button type="submit" class="btn btn-default">Lend</button>
    </form>

<footer><a href="lentBooks.php">Se all lent books</a></footer>
<footer><a href="index.php">Go home</a></footer>

<?php } ?>

</body>
</html>