<?php include_once('createEditStoreHeader.html');
      include_once('db.php'); // because one does not simply declare class several times

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['createSuccess']) && $_GET['createSuccess'] == 1){
 ?>
    <div class="alert alert-success"><?php echo 'Store successfully created'; ?> </div>
  <?php }
elseif(isset($_GET['updateSuccess']) && $_GET['updateSuccess'] == 1) {
  ?>
    <div class="alert alert-success"><?php echo 'Store successfully updated'; ?> </div>
  <?php }

// inserting into the Stores table
// let's make Country and City parameters selectable options using values from the database
// address need validation, whether a city is located in a selected country. This can be done using SQL function (as Bakur says at least)

$db = DB::getConnection();

$st2h = $db->prepare("SELECT Countries.name AS country, Countries.id AS country_id, Cities.name AS city, Cities.id AS city_id
    FROM Countries JOIN Cities
    ON Countries.id=Cities.country_id");
$st2h->execute();
$addresses = $st2h->fetchAll(PDO::FETCH_ASSOC);

if($_POST != null)
{
  $store = $_POST;
  if(isset($store['create']))
  {
      $sth = $db->prepare("INSERT INTO Stores(name, country_id, city_id)
      VALUES (?, ?, ?)");
      $sth->execute([$store['name'], $store['country'], $store['city']]);

      header('Location: ' . $_SERVER['PHP_SELF']. '?createSuccess=1');
  }
  elseif(isset($_POST['update']))
  {
      $sth = $db->prepare("UPDATE Stores
        SET name=?, country_id=?, city_id=?
        WHERE id=?");
      $sth->execute([$store['name'], $store['country'], $store['city'], $store['id']]);

      header('Location: ' . $_SERVER['PHP_SELF']. '?id=' . $store['id'] . '&updateSuccess=1');
  }
  die;
}

if(isset($_GET['id']))
{
$id = $_GET['id'];
 if(filter_var($id, FILTER_VALIDATE_INT) && $id>0)
    {
        $id = $id;
        $sth = $db->prepare("SELECT Stores.id AS id, Stores.name AS name, Countries.name AS country, Cities.name AS city
        FROM Stores JOIN Countries
        ON Stores.country_id=Countries.id
        JOIN Cities
        ON Stores.city_id=Cities.id
        WHERE Stores.id=?");
        //$sth->bindParam(':id', $id);
        $sth->execute([$id]);
        $store = $sth->fetch();
?>

<h1>Edit a Store</h1>

<form action="createEditStore.php" method="post" class="container">
  <div class="form-group">
    <label for="name">Name</label>
    <input id="name" class="form-control" name="name" autocomplete="off" required="required" value="<?php echo $store['name']; ?>">
  </div>
  <div class="form-group">
    <label for="country">Country:</label>
        <select id="country" name="country" class="form-control" required="required">
        <?php
        foreach($addresses as $address)
        {
        ?>
            <option value="<?php echo $address['country_id']; ?>" <?php if($store['city'] == $address['city']){ ?> selected="selected" <?php } ?> >
            <?php echo $address['country']; ?></option>
        <?php } ?>
        </select>
  </div>
  <div class="form-group">
    <label for="city">City:</label>
        <select id="city" name="city" class="form-control" required="required">
        <?php
        foreach($addresses as $address)
        {
        ?>
            <option value="<?php echo $address['city_id']; ?>" <?php if($store['city'] == $address['city']){ ?> selected="selected" <?php } ?> >
            <?php echo $address['city']; ?></option>
        <?php } ?>
        </select>
   </div>
   <input type="hidden" name="id" value="<?php echo $store['id']; ?>">
   <input type="hidden" name="update" value="1">
  <button type="submit" class="btn btn-default">Edit</button>
  <button type="reset" class="btn btn-default">Reset</button>
</form>

<?php }
}

else
{
?>

<h1>Create a Store</h1>

<form action="createEditStore.php" method="post" class="container">
  <div class="form-group">
    <label for="name">Name</label>
    <input id="name" class="form-control" name="name" autocomplete="off" required="required">
  </div>
  <div class="form-group">
    <label for="country">Country:</label>
        <select id="country" name="country" class="form-control" required="required">
        <?php
        foreach($addresses as $address)
        {
        ?>
            <option value="<?php echo $address['country_id']; ?>"><?php echo $address['country']; ?></option>
        <?php } ?>
        </select>
  </div>
  <div class="form-group">
    <label for="city">City:</label>
        <select id="city" name="city" class="form-control" required="required">
        <?php
        foreach($addresses as $address)
        {
        ?>
            <option value="<?php echo $address['city_id']; ?>"><?php echo $address['city']; ?></option>
        <?php } ?>
        </select>
   </div>
   <input type="hidden" name="create" value="1">
  <button type="submit" class="btn btn-default">Create</button>
  <button type="reset" class="btn btn-default">Reset</button>
</form>

<?php } ?>

<footer><a href="index.php">Go home</a></footer>

<?php include('footer.php'); ?>


😂     😂   😂 😂
  😂 😂     😂    😂
     😂        😂     😂
  😂 😂     😂    😂
😂      😂  😂 😂﻿