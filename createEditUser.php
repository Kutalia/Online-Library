<!DOCTYPE html>
<html>
<head>
    <title>Create/Edit User</title>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css"/>
</head>
<body>

<?php include_once('db.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['createSuccess'])) {
    if ($_GET['createSuccess'] == 1) { ?>
        <div class="alert alert-success"><?php echo 'User successfully created'; ?> </div>
    <?php }
} elseif (isset($_GET['updateSuccess'])) {
    if ($_GET['updateSuccess'] == 1) {
        ?>
        <div class="alert alert-success"><?php echo 'User successfully updated'; ?> </div>
    <?php }
}

$db = DB::getConnection();

$st2h = $db->prepare("SELECT Countries.name AS country, Countries.id AS country_id, Cities.name AS city, Cities.id AS city_id
    FROM Countries JOIN Cities
    ON Countries.id=Cities.country_id");
$st2h->execute();
$addresses = $st2h->fetchAll(PDO::FETCH_ASSOC);

if ($_POST != null) {
    $user = $_POST;
    if (isset($user['create'])) {
        $sth = $db->prepare("INSERT INTO Users(first_name, last_name, private_id, phone, email, gender, birthday, country_id, city_id)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $sth->execute([$user['firstName'], $user['lastName'], $user['privateId'], $user['phone'], $user['email'],
            $user['gender'], $user['birthday'], $user['countryId'], $user['cityId']]);

        header('Location: ' . $_SERVER['PHP_SELF'] . '?createSuccess=1');
    } elseif (isset($user['update'])) {
        $sth = $db->prepare("UPDATE Users
        SET first_name=?, last_name=?, private_id=?, phone=?, email=?, gender=?, birthday=?, country_id=?, city_id=?
        WHERE id=?");
        $sth->execute([$user['firstName'], $user['lastName'], $user['privateId'], $user['phone'], $user['email'],
            $user['gender'], $user['birthday'], $user['countryId'], $user['cityId'], $user['id']]);

        header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $user['id'] . '&updateSuccess=1');
    }
    die;
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (filter_var($id, FILTER_VALIDATE_INT) && $id > 0) {
        $id = $id;
        $sth = $db->prepare("SELECT Users.id, Users.first_name AS firstName, Users.last_name AS lastName, Users.private_id AS privateId,
          Users.phone, Users.email, Users.gender, Users.birthday, Countries.name AS country, Cities.name AS city
        FROM Users JOIN Countries
        ON Users.country_id=Countries.id
        JOIN Cities
        ON Users.city_id=Cities.id
        WHERE Users.id=?");
        $sth->execute([$id]);
        $user = $sth->fetch();
        ?>

        <h1>Edit User</h1>
        <form action="" method="post" class="container">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input id="firstName" class="form-control" name="firstName" autocomplete="off" required="required"
                       value="<?php echo $user['firstName']; ?>">
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input id="lastName" class="form-control" name="lastName" autocomplete="off" required="required"
                       value="<?php echo $user['lastName']; ?>">
            </div>
            <div class="form-group">
                <label for="privateId">Private ID number</label>
                <input id="privateId" class="form-control" name="privateId" autocomplete="off" required="required"
                       value="<?php echo $user['privateId']; ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone number</label>
                <input id="phone" class="form-control" name="phone" autocomplete="off" required="required"
                       value="<?php echo $user['phone']; ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input id="email" class="form-control" name="email" type="email" autocomplete="off"
                       value="<?php echo $user['email']; ?>">
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" class="form-control" name="gender">
                    <option value="M" <?php
                    if ($user['gender'] == 'M') {
                        echo 'selected="selected"';
                    }
                    ?>
                    >Male
                    </option>
                    <option value="F" <?php
                    if ($user['gender'] == 'F') {
                        echo 'selected="selected"';
                    }
                    ?>
                    >Female
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="birthday">Birthday</label>
                <input id="birthday" class="form-control" name="birthday"
                       type="date"
                       pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))"
                       autocomplete="off" value="<?php echo $user['birthday']; ?>">
            </div>
            <div class="form-group">
                <label for="country">Country:</label>
                <select id="country" name="countryId" class="form-control" required="required">
                    <?php
                    foreach ($addresses as $address) {
                        ?>
                        <option
                            value="<?php echo $address['country_id']; ?>" <?php if ($user['city'] == $address['city']) { ?> selected="selected" <?php } ?> >
                            <?php echo $address['country']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="city">City:</label>
                <select id="city" name="cityId" class="form-control">
                    <?php
                    foreach ($addresses as $address) {
                        ?>
                        <option
                            value="<?php echo $address['city_id']; ?>" <?php if ($user['city'] == $address['city']) { ?> selected="selected" <?php } ?> >
                            <?php echo $address['city']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <input type="hidden" name="update" value="1">
            <button type="submit" class="btn btn-default">Edit</button>
            <button type="reset" class="btn btn-default">Reset</button>
        </form>

    <?php }
} else {
    ?>

    <h1>Create an User</h1>

    <form action="createEditUser.php" method="post" class="container">
        <div class="form-group">
            <label for="firstName">First Name</label>
            <input id="firstName" class="form-control" name="firstName" autocomplete="off" required="required">
        </div>
        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input id="lastName" class="form-control" name="lastName" autocomplete="off" required="required">
        </div>
        <div class="form-group">
            <label for="privateId">Private ID number</label>
            <input id="privateId" class="form-control" name="privateId" autocomplete="off" required="required">
        </div>
        <div class="form-group">
            <label for="phone">Phone number</label>
            <input id="phone" class="form-control" name="phone" autocomplete="off" required="required">
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input id="email" class="form-control" name="email" type="email" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" class="form-control" name="gender">
                <option selected="selected" disabled="disabled"></option>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="birthday">Birthday</label>
            <input id="birthday" class="form-control" name="birthday"
                   type="date"
                   pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))"
                   autocomplete="off">
        </div>
        <div class="form-group">
            <label for="country">Country:</label>
            <select id="country" name="countryId" class="form-control" required="required">
                <?php
                foreach ($addresses as $address) {
                    ?>
                    <option value="<?php echo $address['country_id']; ?>">
                        <?php echo $address['country']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="city">City:</label>
            <select id="city" name="cityId" class="form-control">
                <?php
                foreach ($addresses as $address) {
                    ?>
                    <option value="<?php echo $address['city_id']; ?>">
                        <?php echo $address['city']; ?></option>
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