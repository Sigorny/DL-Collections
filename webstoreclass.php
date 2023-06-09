<?php
class WebStore

{
  // Database
  private $server = "mysql:host=localhost;dbname=d;port=3306";
  private $user = "root";
  private $pass = "";
  private $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ];
  protected $con;

  public function openConnection()
  {
    try {
      $this->con = new PDO(
        $this->server,
        $this->user,
        $this->pass,
        $this->options
      );
      return $this->con;
    } catch (PDOException $e) {
      echo "There is some problem in the connection :" . $e->getMessage();
    }
  }

  public function closeConnection()
  {
    $this->con = null;
  }

  // login
  public function login()
  {
    if (isset($_POST["login"])) {
      $email = $_POST["email"];
      $password = md5($_POST["password"]);

      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "SELECT * FROM account_table WHERE email = ? AND password = ?"
      );
      $stmt->execute([$email, $password]);
      $row = $stmt->fetch();
      $count = $stmt->rowCount();

      if ($count > 0) {
        $this->set_userdata($row);
        if (isset($_SESSION["userdata"])) {
          if ($_SESSION["userdata"]["access"] != "admin") {
            header("Location: index.php");
          } else {
            header("Location: dashboard.php");
          }
        }
      } else {
        header("Location: login.php?error=Invalid Email Address or Password.");
      }
    }
  }

  // login form validation
  public function loginValidation()
  {
    function validate($data)
    {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }

    if (isset($_POST["email"]) && isset($_POST["password"])) {
      $email = validate($_POST["email"]);
      $password = validate($_POST["password"]);
      if (empty($email) && empty($password)) {
        header(
          "Location: login.php?error=Email Address and Password are required."
        );
        exit();
      } elseif (empty($password)) {
        header("Location: login.php?error=Password is required.");
        exit();
      } elseif (empty($email)) {
        header("Location: login.php?error=Email Address is required.");
        exit();
      }
    }
  }

  // check email if already exists
  public function checkEmail($email)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare("SELECT * FROM account_table WHERE email = ?");
    $stmt->execute([$email]);
    $count = $stmt->rowCount();
    return $count;
  }

  // user sign up
  public function signup()
  {
    if (isset($_POST["signup"])) {
      $firstName = $_POST["firstName"];
      $lastName = $_POST["lastName"];
      $email = $_POST["email"];
      $password = md5($_POST["password"]);
      $access = $_POST["access"];

      if ($this->checkEmail($email) == 0) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO account_table (`firstName`, `lastName`, `email`, `password`, `access`) VALUES (?,?,?,?,?)"
        );
        $stmt->execute([$firstName, $lastName, $email, $password, $access]);
        header("Location: login.php");
      } else {
        header("Location: signup.php?emailError=Email Already Exists");
      }
    }
  }

  // sign up form validation
  public function signupValidation()
  {
    if (
      isset($_POST["firstName"]) &&
      isset($_POST["lastName"]) &&
      isset($_POST["email"]) &&
      isset($_POST["password"]) &&
      isset($_POST["confirmPassword"])
    ) {
      function validate($data)
      {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
      }
      $firstName = $_POST["firstName"];
      $lastName = $_POST["lastName"];
      $email = $_POST["email"];
      $password = $_POST["password"];
      $confirmPassword = $_POST["confirmPassword"];

      // Validate password strength
      // $uppercase = preg_match('@[A-Z]@', $password);
      // $lowercase = preg_match('@[a-z]@', $password);
      // $number    = preg_match('@[0-9]@', $password);
      // $specialChars = preg_match('@[^\w]@', $password);

      if (empty($firstName)) {
        header("Location: signup.php?firstNameError=First Name is required.");
        exit();
      } elseif (empty($lastName)) {
        header("Location: signup.php?lastNameError=Last Name is required.");
        exit();
      } elseif (empty($email)) {
        header("Location: signup.php?emailError=Email Address is required.");
        exit();
      } elseif (empty($password)) {
        header("Location: signup.php?passwordError=Password is required.");
        exit();
      } elseif (empty($confirmPassword)) {
        header(
          "Location: signup.php?confirmPasswordError=Confirm Password is required."
        );
        exit();
      } elseif (strlen($password) < 8) {
        header(
          "Location: signup.php?passwordError=Password must be at least 8 characters."
        );
        exit();
        // else if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8){
        //     header("Location: signup.php?passwordError=Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.");
        //     exit();
      } elseif ($password !== $confirmPassword) {
        header(
          "Location: signup.php?confirmPasswordError=Password does not match."
        );
        exit();
      }
    }
  }

  // set user data
  public function set_userdata($array)
  {
    if (!isset($_SESSION)) {
      session_start();
    }

    $_SESSION["userdata"] = [
      "ID" => $array["ID"],
      "firstName" => $array["firstName"],
      "lastName" => $array["lastName"],
      "email" => $array["email"],
      "access" => $array["access"],
    ];
    return $_SESSION["userdata"];
  }

  // get user data
  public function get_userdata()
  {
    if (!isset($_SESSION)) {
      session_start();
    }
    if (isset($_SESSION["userdata"])) {
      return $_SESSION["userdata"];
    } else {
      return null;
    }
  }

  // for profile page
  public function setProfile()
  {
    $ID = $_GET["ID"];
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT * FROM account_table WHERE ID = '$ID'"
    );
    $stmt->execute();
    $user = $stmt->fetch();
    $userCount = $stmt->rowCount();

    if ($userCount > 0) {
      return $user;
    } else {
      return $this->show_404();
    }
  }

  // update user profile
  public function update_userdata()
  {
    if (isset($_POST["update"])) {
      $firstName = $_POST["firstName"];
      $lastName = $_POST["lastName"];
      $email = $_POST["email"];
      $contactNumber = $_POST["contactNumber"];
      $newPass = md5($_POST["newPass"]);
      $confirmPass = md5($_POST["confirmPass"]);
      $avatarImg = $_FILES["avatarImg"]["name"];

      if (empty($firstName) || empty($lastName) || empty($email)) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } elseif ($newPass !== $confirmPass) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Password does not match.',
        });
        </script>";
      } elseif (
        empty($_POST["newPass"]) &&
        empty($_POST["confirmPass"]) &&
        empty($avatarImg)
      ) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `firstName` = ?, `lastName` = ?, `contactNumber` = ? WHERE email = '$email'"
        );
        $stmt->execute([$firstName, $lastName, $contactNumber]);
        $row = $stmt->fetch();
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return $row;
      } elseif (
        (empty($_POST["newPass"]) || empty($_POST["confirmPass"])) &&
        !empty($avatarImg)
      ) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `firstName` = ?, `lastName` = ?, `contactNumber` = ?, `profileImg` = ? WHERE email = '$email'"
        );
        $stmt->execute([$firstName, $lastName, $contactNumber, $avatarImg]);
        if (!empty($_POST["oldAvatarImg"])) {
          unlink("assets/img/" . $_POST["oldAvatarImg"]);
        }
        move_uploaded_file(
          $_FILES["avatarImg"]["tmp_name"],
          "assets/img/" . $avatarImg
        );
        $row = $stmt->fetch();
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return $row;
      } elseif (strlen($_POST["newPass"]) < 8) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Password must be at least 8 characters.',
        });
        </script>";
      } elseif (
        (!empty($_POST["newPass"]) || !empty($_POST["newPass"])) &&
        empty($avatarImg)
      ) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `firstName` = ?, `lastName` = ?, `password` = ?, `contactNumber` = ? WHERE email = '$email'"
        );
        $stmt->execute([$firstName, $lastName, $newPass, $contactNumber]);
        $row = $stmt->fetch();
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return $row;
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `firstName` = ?, `lastName` = ?, `password` = ?, `contactNumber` = ?, `profileImg` = ? WHERE email = '$email'"
        );
        $stmt->execute([
          $firstName,
          $lastName,
          $newPass,
          $contactNumber,
          $avatarImg,
        ]);
        if (!empty($_POST["oldAvatarImg"])) {
          unlink("assets/img/" . $_POST["oldAvatarImg"]);
        }
        move_uploaded_file(
          $_FILES["avatarImg"]["tmp_name"],
          "assets/img/" . $avatarImg
        );
        $row = $stmt->fetch();
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return $row;
      }
    }
  }

  // delete account
  public function delete_userdata()
  {
    if (isset($_POST["emailCustomer"])) {
      $email = $_POST["emailCustomer"];
      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "DELETE FROM account_table WHERE email = '$email'"
      );
      $stmt->execute();
      if (!empty($_POST["deleteOldAvatarImg"])) {
        unlink("assets/img/" . $_POST["deleteOldAvatarImg"]);
      }
      $this->logout();
      header("Location: index.php");
      exit();
    }
  }

  // add address
  public function add_addresses()
  {
    if (isset($_POST["addNewAddresses"])) {
      $acctID = $_POST["acctID"];
      $addressID = $_POST["addressID"];
      $firstName = $_POST["addFirstName"];
      $lastName = $_POST["addLastName"];
      $address1 = $_POST["addAddress1"];
      $address2 = $_POST["addAddress2"];
      $city = $_POST["addCity"];
      $postalCode = $_POST["addPostalCode"];
      if (isset($_POST["addRegion"])) {
        $region = $_POST["addRegion"];
      }
      if (isset($_POST["addCountry"])) {
        $country = $_POST["addCountry"];
      }
      $phoneNumber = $_POST["addPhoneNumber"];
      if (isset($_POST["addPrimaryAddress"])) {
        $primaryAddress = $_POST["addPrimaryAddress"];
      }

      if (
        empty($firstName) ||
        empty($lastName) ||
        empty($address1) ||
        empty($city) ||
        empty($postalCode) ||
        empty(isset($_POST["addRegion"])) ||
        empty(isset($_POST["addCountry"])) ||
        empty($phoneNumber)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } elseif (empty(isset($_POST["addPrimaryAddress"]))) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO address_table (`addressID`, `firstName`, `lastName`, `address1`, `address2`, `city`, `postalCode`, `region`, `country`, `phoneNumber`, `addressType`, `accountID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
          $addressID,
          $firstName,
          $lastName,
          $address1,
          $address2,
          $city,
          $postalCode,
          $region,
          $country,
          $phoneNumber,
          "",
          $acctID,
        ]);
        if (!isset($_SESSION)) {
          session_start();
        }
        if (isset($_SESSION["userdata"])) {
          $ID = $_SESSION["userdata"]["ID"];
          header("Location: addresses.php?ID=$ID");
        }
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO address_table (`addressID`, `firstName`, `lastName`, `address1`, `address2`, `city`, `postalCode`, `region`, `country`, `phoneNumber`, `addressType`, `accountID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
          $addressID,
          $firstName,
          $lastName,
          $address1,
          $address2,
          $city,
          $postalCode,
          $region,
          $country,
          $phoneNumber,
          $primaryAddress,
          $acctID,
        ]);
        if (!isset($_SESSION)) {
          session_start();
        }
        if (isset($_SESSION["userdata"])) {
          $ID = $_SESSION["userdata"]["ID"];
          header("Location: addresses.php?ID=$ID");
        }
      }
    }
  }

  // add address
  public function update_addresses()
  {
    if (isset($_POST["updateAddresses"])) {
      $acctID = $_POST["editAcctID"];
      $addressID = $_POST["editAddressID"];
      $firstName = $_POST["editFirstName"];
      $lastName = $_POST["editLastName"];
      $hiddenAddress1 = $_POST["hiddenAddress1"];
      $hiddenAddress2 = $_POST["hiddenAddress2"];
      $hiddenCity = $_POST["hiddenCity"];
      $hiddenPostalCode = $_POST["hiddenPostalCode"];
      $hiddenRegion = $_POST["hiddenRegion"];
      $hiddenCountry = $_POST["hiddenCountry"];
      $hiddenPhone = $_POST["hiddenPhoneNumber"];
      $hiddenPrimary = $_POST["hiddenAddressType"];
      $fName = $_POST["editFName"];
      $lName = $_POST["editLName"];
      $address1 = $_POST["editAddress1"];
      $address2 = $_POST["editAddress2"];
      $city = $_POST["editCity"];
      $postalCode = $_POST["editPostalCode"];
      if (isset($_POST["editRegion"])) {
        $region = $_POST["editRegion"];
      }
      if (isset($_POST["editCountry"])) {
        $country = $_POST["editCountry"];
      }
      $phoneNumber = $_POST["editPhoneNumber"];
      if (isset($_POST["editPrimaryAddress"])) {
        $primaryAddress = $_POST["editPrimaryAddress"];
      }

      if (
        empty($firstName) ||
        empty($lastName) ||
        empty($address1) ||
        empty($city) ||
        empty($postalCode) ||
        empty(isset($_POST["editRegion"])) ||
        empty(isset($_POST["editCountry"])) ||
        empty($phoneNumber)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } elseif (empty(isset($_POST["editPrimaryAddress"]))) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE address_table SET `firstName` = ?, `lastName` = ?, `address1` = ?, `address2` = ?, `city` = ?, `postalCode` = ?, `region` = ?, `country` = ?, `phoneNumber` = ?, `addressType` = ? WHERE addressID = ? AND firstName = ? AND lastName = ? AND address1 = ? AND address2 = ? AND city = ? AND postalCode = ? AND region = ? AND country = ? AND phoneNumber = ? AND addressType = ? AND accountID = ?"
        );
        $stmt->execute([
          $fName,
          $lName,
          $address1,
          $address2,
          $city,
          $postalCode,
          $region,
          $country,
          $phoneNumber,
          "",
          $addressID,
          $firstName,
          $lastName,
          $hiddenAddress1,
          $hiddenAddress2,
          $hiddenCity,
          $hiddenPostalCode,
          $hiddenRegion,
          $hiddenCountry,
          $hiddenPhone,
          $hiddenPrimary,
          $acctID,
        ]);
        $row = $stmt->fetch();
        if (!isset($_SESSION)) {
          session_start();
        }
        if (isset($_SESSION["userdata"])) {
          $ID = $_SESSION["userdata"]["ID"];
          header("Location: addresses.php?ID=$ID");
        }
        return $row;
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE address_table SET `firstName` = ?, `lastName` = ?, `address1` = ?, `address2` = ?, `city` = ?, `postalCode` = ?, `region` = ?, `country` = ?, `phoneNumber` = ?, `addressType` = ? WHERE addressID = ? AND firstName = ? AND lastName = ? AND address1 = ? AND address2 = ? AND city = ? AND postalCode = ? AND region = ? AND country = ? AND phoneNumber = ? AND addressType = ? AND accountID = ?"
        );
        $stmt->execute([
          $fName,
          $lName,
          $address1,
          $address2,
          $city,
          $postalCode,
          $region,
          $country,
          $phoneNumber,
          $primaryAddress,
          $addressID,
          $firstName,
          $lastName,
          $hiddenAddress1,
          $hiddenAddress2,
          $hiddenCity,
          $hiddenPostalCode,
          $hiddenRegion,
          $hiddenCountry,
          $hiddenPhone,
          $hiddenPrimary,
          $acctID,
        ]);
        $row = $stmt->fetch();
        if (!isset($_SESSION)) {
          session_start();
        }
        if (isset($_SESSION["userdata"])) {
          $ID = $_SESSION["userdata"]["ID"];
          header("Location: addresses.php?ID=$ID");
        }
        return $row;
      }
    }
  }

  // delete address
  public function delete_address()
  {
    if (
      isset($_POST["addressID"]) &&
      isset($_POST["firstName"]) &&
      isset($_POST["lastName"]) &&
      isset($_POST["address1"]) &&
      isset($_POST["city"]) &&
      isset($_POST["postalCode"]) &&
      isset($_POST["region"]) &&
      isset($_POST["country"]) &&
      isset($_POST["phoneNumber"]) &&
      isset($_POST["addressType"])
    ) {
      $addressID = $_POST["addressID"];
      $firstName = $_POST["firstName"];
      $lastName = $_POST["lastName"];
      $address1 = $_POST["address1"];
      $address2 = $_POST["address2"];
      $city = $_POST["city"];
      $postalCode = $_POST["postalCode"];
      $region = $_POST["region"];
      $country = $_POST["country"];
      $phoneNumber = $_POST["phoneNumber"];
      $addressType = $_POST["addressType"];
      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "DELETE FROM address_table WHERE addressID = ? AND firstName = ? AND lastName = ? AND address1 = ? AND address2 = ? AND city = ? AND postalCode = ? AND region = ? AND country = ? AND phoneNumber = ? AND addressType = ?"
      );
      $stmt->execute([
        $addressID,
        $firstName,
        $lastName,
        $address1,
        $address2,
        $city,
        $postalCode,
        $region,
        $country,
        $phoneNumber,
        $addressType,
      ]);
      header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
  }

  // logout
  public function logout()
  {
    if (!isset($_SESSION)) {
      session_start();
    }
    $_SESSION["userdata"] = null;
    $_SESSION["checkout"] = null;
    unset($_SESSION["userdata"]);
    unset($_SESSION["checkout"]);
  }

  // display admin
  public function get_admin()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT * FROM account_table WHERE access = 'admin' ORDER BY ID DESC"
    );
    $stmt->execute();
    $admins = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $admins;
    } else {
      return false;
    }
  }

  // update admin
  public function update_admin()
  {
    if (isset($_POST["updateAdmin"])) {
      $adminID = $_POST["adminID"];
      $firstName = $_POST["firstName"];
      $lastName = $_POST["lastName"];
      $email = $_POST["email"];
      $contactNumber = $_POST["contactNumber"];
      $newPass = md5($_POST["newPass"]);
      $confirmPass = md5($_POST["confirmPass"]);
      $profileImg = $_FILES["profileImg"]["name"];

      if (empty($firstName) || empty($lastName) || empty($email)) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } elseif ($newPass !== $confirmPass) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Password does not match.',
        });
        </script>";
      } elseif (
        empty($_POST["newPass"]) &&
        empty($_POST["confirmPass"]) &&
        empty($profileImg)
      ) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `firstName` = ? , `lastName` = ? , `email` = ?, `contactNumber` = ? WHERE ID = '$adminID'"
        );
        $stmt->execute([$firstName, $lastName, $email, $contactNumber]);
        $row = $stmt->fetch();
        echo "<script>  
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Admin Updated',
          showConfirmButton: false,
          timer: 1000
        });
      </script>";
        return $row;
      } elseif (
        (empty($_POST["newPass"]) || empty($_POST["confirmPass"])) &&
        !empty($profileImg)
      ) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `firstName` = ? , `lastName` = ? , `email` = ?, `contactNumber` = ?, `profileImg` = ? WHERE ID = '$adminID'"
        );
        $stmt->execute([
          $firstName,
          $lastName,
          $email,
          $contactNumber,
          $profileImg,
        ]);
        if (!empty($_POST["oldProfileImg"])) {
          unlink("assets/img/" . $_POST["oldProfileImg"]);
        }
        move_uploaded_file(
          $_FILES["profileImg"]["tmp_name"],
          "assets/img/" . $profileImg
        );
        $row = $stmt->fetch();
        echo "<script>  
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Admin Updated',
          showConfirmButton: false,
          timer: 1000
        });
      </script>";
        return $row;
      } elseif (strlen($_POST["newPass"]) < 8) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Password must be at least 8 characters.',
        });
        </script>";
      } elseif (
        (!empty($_POST["newPass"]) || !empty($_POST["newPass"])) &&
        empty($profileImg)
      ) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `firstName` = ? , `lastName` = ? , `email` = ?, `password` = ?,`contactNumber` = ? WHERE ID = '$adminID'"
        );
        $stmt->execute([
          $firstName,
          $lastName,
          $email,
          $newPass,
          $contactNumber,
        ]);
        $row = $stmt->fetch();
        echo "<script>  
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Admin Updated',
          showConfirmButton: false,
          timer: 1000
        });
      </script>";
        return $row;
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `firstName` = ? , `lastName` = ? , `email` = ? , `password` = ?,`contactNumber`= ?, `profileImg` = ? WHERE ID = '$adminID'"
        );
        $stmt->execute([
          $firstName,
          $lastName,
          $email,
          $newPass,
          $contactNumber,
          $profileImg,
        ]);
        if (!empty($_POST["oldProfileImg"])) {
          unlink("assets/img/" . $_POST["oldProfileImg"]);
        }
        move_uploaded_file(
          $_FILES["profileImg"]["tmp_name"],
          "assets/img/" . $profileImg
        );
        $row = $stmt->fetch();
        echo "<script>
            Swal.fire({
              position: 'center',
              icon: 'success',
              title: 'Admin Updated',
              showConfirmButton: false,
              timer: 1000
            });
          </script>";
        return $row;
      }
    }
  }

  // add product category
  public function add_category()
  {
    if (isset($_POST["addCategory"])) {
      $productCategory = $_POST["categoryName"];

      if (empty($productCategory)) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO category_table (`categoryName`) VALUES (?)"
        );
        $stmt->execute([$productCategory]);
        echo "<script>  
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Material Added',
          showConfirmButton: false,
          timer: 1000
        });
      </script>";
      }
    }
  }

  // display product categories
  public function get_categories()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT * FROM category_table ORDER BY ID DESC"
    );
    $stmt->execute();
    $categories = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $categories;
    } else {
      return false;
    }
  }

  // add products
  public function add_products()
  {
    if (isset($_POST["addProduct"])) {
      if (isset($_POST["category"])) {
        $category = $_POST["category"];
      }
      $productName = $_POST["productName"];
      $productDescription = $_POST["productDescription"];
      $coverPhoto = $_FILES["coverPhoto"]["name"];
      $image1 = $_FILES["image1"]["name"];
      $image2 = $_FILES["image2"]["name"];
      $image3 = $_FILES["image3"]["name"];
      $sizeGuide = $_FILES["sizeGuide"]["name"];
      $availability = $_POST["availability"];
      if (
        empty(isset($_POST["category"])) ||
        empty($productName) ||
        empty($productDescription) ||
        empty($coverPhoto)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO product_table (`categoryID`, `productName`, `productDescription`, `coverPhoto`, `image1`, `image2`, `image3`, `sizeGuide`, `availability`) VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
          $category,
          $productName,
          $productDescription,
          $coverPhoto,
          $image1,
          $image2,
          $image3,
          $sizeGuide,
          $availability,
        ]);
        move_uploaded_file(
          $_FILES["coverPhoto"]["tmp_name"],
          "assets/img/" . $coverPhoto
        );
        move_uploaded_file(
          $_FILES["image1"]["tmp_name"],
          "assets/img/" . $image1
        );
        move_uploaded_file(
          $_FILES["image2"]["tmp_name"],
          "assets/img/" . $image2
        );
        move_uploaded_file(
          $_FILES["image3"]["tmp_name"],
          "assets/img/" . $image3
        );
        move_uploaded_file(
          $_FILES["sizeGuide"]["tmp_name"],
          "assets/img/" . $sizeGuide
        );

        header("Location: products.php");
      }
    }
  }

  // add variation
  public function add_variation()
  {
    if (isset($_POST["addVariant"])) {
      $productID = $_POST["productID"];
      $variationImg = $_FILES["variationImg"]["name"];

      $count = count($_POST["variationName"]);
      for ($i = 0; $i < $count; $i++) {
        $variantName = $_POST["variationName"][$i];
        $variantImg = $_FILES["variationImg"]["name"][$i];
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO variation_table ( `productID`, `variantName`, `variantImage`) VALUES (?,?,?)"
        );
        $stmt->execute([$productID, $variantName, $variantImg]);
        move_uploaded_file(
          $_FILES["variationImg"]["tmp_name"][$i],
          "assets/img/" . $_FILES["variationImg"]["name"][$i]
        );
      }
      header("Location: products.php");
    }
  }

  // get variation per product
  public function get_variation($ID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT * FROM variation_table WHERE productID = ?"
    );
    $stmt->execute([$ID]);
    $variant = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $variant;
    } else {
      return $this->show_dashboard404();
    }
  }

  // add costing
  public function costing()
  {
    if (isset($_POST["finishProduct"])) {
      $raw = isset($_POST["raw"]);
      if (isset($_POST["materialID"])) {
        $count = count($_POST["materialID"]);
      }
      $laborFee = $_POST["laborFee"];
      $laborFeeQty = $_POST["laborFeeQty"];
      $layoutFee = $_POST["layoutFee"];
      $layoutFeeQty = $_POST["layoutFeeQty"];
      $expenseFee = $_POST["expenseFee"];
      $expenseFeeQty = $_POST["expenseFeeQty"];
      $totalCost = $_POST["totalCostAmount"];
      $salesAmount = $_POST["salesAmount"];
      $salesDiscount = $_POST["salesDiscount"];
      $netSales = $_POST["netSales"];
      $productCost = $_POST["productCost"];
      $gross = $_POST["gross"];
      $expenses = $_POST["expenses"];
      $netIncome = $_POST["netIncome"];
      $productID = $_POST["productID"];

      if (
        empty($raw) ||
        empty($netSales) ||
        empty($productCost) ||
        empty($netIncome)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $count = count($_POST["materialID"]);
        for ($i = 0; $i < $count; $i++) {
          $materialID = $_POST["materialID"][$i];
          $materialQty = $_POST["qty"][$i];
          $connection = $this->openConnection();
          $stmt = $connection->prepare(
            "INSERT INTO costing_table ( `productID`, `materialID`, `materialQty`, `laborFee`, `laborQty`, layoutFee, layoutQty, expenseFee, expenseQty, `productCost`, `totalCost`, `salesAmount`, `salesDiscount`, `netSales`, `grossProfit`, `expenses`, `netIncome`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
          );
          $stmt->execute([
            $productID,
            $materialID,
            $materialQty,
            $laborFee,
            $laborFeeQty,
            $layoutFee,
            $layoutFeeQty,
            $expenseFee,
            $expenseFeeQty,
            $productCost,
            $totalCost,
            $salesAmount,
            $salesDiscount,
            $netSales,
            $gross,
            $expenses,
            $netIncome,
          ]);
        }
        header("Location: products.php");
      }
    }
  }

  //display single product ID
  public function get_singleID($ID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare("SELECT * FROM product_table WHERE ID = ?");
    $stmt->execute([$ID]);
    $product = $stmt->fetch();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $product;
    } else {
      return $this->show_dashboard404();
    }
  }

  // add stock qty
  public function stock_in()
  {
    if (isset($_POST["addStock"])) {
      $productID = $_POST["productID"];

      $noSize = $_POST["noSize"];

      if (empty($noSize)) {
        $count = count($_POST["variationID"]);
        for ($i = 0; $i < $count; $i++) {
          $variantID = $_POST["variationID"][$i];
          $wholeStock = $_POST["wholeStock"][$i];
          $skuNoSize = $_POST["skuNoSize"][$i];
          $connection = $this->openConnection();
          $stmt = $connection->prepare(
            "INSERT INTO stocks_table ( `productID`, `variantID`, `stock`, `sku`) VALUES (?,?,?,?)"
          );
          $stmt->execute([$productID, $variantID, $wholeStock, $skuNoSize]);
        }
      } else {
        $count = count($_POST["variantID"]);
        for ($i = 0; $i < $count; $i++) {
          $variantID = $_POST["variantID"][$i];
          $size = $_POST["size"][$i];
          $stock = $_POST["stocks"][$i];
          $sku = $_POST["sku"][$i];
          $connection = $this->openConnection();
          $stmt = $connection->prepare(
            "INSERT INTO stocks_table ( `productID`, `variantID`, `size`, `stock`, `sku`) VALUES (?,?,?,?,?)"
          );
          $stmt->execute([$productID, $variantID, $size, $stock, $sku]);
        }
      }
      header("Location: products.php");
    }
  }

  // show 404 page
  public function show_404()
  {
    http_response_code(404);
    header("Location: 404.php");
    die();
  }

  // show 404 page in dashboard
  public function show_dashboard404()
  {
    http_response_code(404);
    header("Location: dashboard404.php");
    die();
  }

  // display all products
  public function get_products()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT product.ID, productName, categoryName, coverPhoto, image1, image2, image3, salesAmount, salesDiscount, netSales, productCost, netIncome, availability, variantName FROM (SELECT * FROM product_table) product LEFT JOIN category_table category ON product.categoryID = category.ID LEFT JOIN costing_table costing ON product.ID = costing.productID LEFT JOIN variation_table variant ON product.ID = variant.productID GROUP BY product.ID ORDER BY product.ID DESC"
    );
    $stmt->execute();
    $products = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $products;
    } else {
      return false;
    }
  }

  //display edit product
  public function get_editproduct($ID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT product.ID as ID, productName, productDescription, categoryID, categoryName, coverPhoto, image1, image2, image3, sizeGuide, netSales, netIncome, productCost FROM product_table product LEFT JOIN category_table category ON product.categoryID = category.ID LEFT JOIN costing_table costing ON product.ID = costing.productID WHERE product.ID = ? GROUP BY product.ID"
    );
    $stmt->execute([$ID]);
    $product = $stmt->fetch();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $product;
    } else {
      return $this->show_dashboard404();
    }
  }

  //publish product
  public function publish_product()
  {
    if (isset($_POST["publishProductID"])) {
      $ID = $_POST["publishProductID"];
      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "UPDATE product_table SET `availability` = 'Available' WHERE ID = '$ID'"
      );
      $stmt->execute();
      $row = $stmt->fetch();
      header("Location: products.php");
      return $row;
    }
  }

  //delist product
  public function delist_product()
  {
    if (isset($_POST["delistProductID"])) {
      $ID = $_POST["delistProductID"];
      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "UPDATE product_table SET `availability` = 'Unavailable' WHERE ID = '$ID'"
      );
      $stmt->execute();
      $row = $stmt->fetch();
      header("Location: products.php");
      return $row;
    }
  }

  // update products
  public function update_product($ID)
  {
    if (isset($_POST["updateProduct"])) {
      if (isset($_POST["category"])) {
        $category = $_POST["category"];
      }
      $productName = $_POST["productName"];
      $productDescription = $_POST["productDescription"];
      $productColor = $_POST["productColor"];
      $coverPhoto = $_FILES["coverPhoto"]["name"];
      $image1 = $_FILES["image1"]["name"];
      $image2 = $_FILES["image2"]["name"];
      $image3 = $_FILES["image3"]["name"];
      $sizeGuide = $_FILES["sizeGuide"]["name"];
      if (
        empty(isset($_POST["category"])) ||
        empty($productName) ||
        empty($productDescription) ||
        empty($productColor)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } elseif (
        empty($coverPhoto) ||
        empty($image1) ||
        empty($image2) ||
        empty($image3) ||
        empty($sizeGuide)
      ) {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE product_table SET `categoryID` = ?, `productName` = ?, `productDescription` = ?, `productColor` = ? WHERE ID = ?"
        );
        $stmt->execute([
          $category,
          $productName,
          $productDescription,
          $productColor,
          $ID,
        ]);
        $row = $stmt->fetch();
        header("Location: products.php");
        return $row;
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE product_table SET `categoryID` = ?, `productName` = ?, `productDescription` = ?, `productColor` = ?, `coverPhoto` = ?, `image1` = ?, `image2` = ?, `image3` = ?, `sizeGuide` = ? WHERE ID = ?"
        );
        $stmt->execute([
          $category,
          $productName,
          $productDescription,
          $productColor,
          $coverPhoto,
          $image1,
          $image2,
          $image3,
          $sizeGuide,
          $ID,
        ]);
        unlink("assets/img/" . $_POST["hiddenCover"]);
        if (!empty($_POST["hiddenImage1"])) {
          unlink("assets/img/" . $_POST["hiddenImage1"]);
        }
        if (!empty($_POST["hiddenImage2"])) {
          unlink("assets/img/" . $_POST["hiddenImage2"]);
        }
        if (!empty($_POST["hiddenImage3"])) {
          unlink("assets/img/" . $_POST["hiddenImage3"]);
        }
        move_uploaded_file(
          $_FILES["coverPhoto"]["tmp_name"],
          "assets/img/" . $coverPhoto
        );
        move_uploaded_file(
          $_FILES["image1"]["tmp_name"],
          "assets/img/" . $image1
        );
        move_uploaded_file(
          $_FILES["image2"]["tmp_name"],
          "assets/img/" . $image2
        );
        move_uploaded_file(
          $_FILES["image3"]["tmp_name"],
          "assets/img/" . $image3
        );
        move_uploaded_file(
          $_FILES["sizeGuide"]["tmp_name"],
          "assets/img/" . $sizeGuide
        );
        $row = $stmt->fetch();
        header("Location: products.php");
        return $row;
      }
    }
  }

  // update costing
  public function update_costing($ID)
  {
    if (isset($_POST["updateProduct"])) {
      if (isset($_POST["materialID"])) {
        $count = count($_POST["materialID"]);
      }
      $laborFee = $_POST["laborFee"];
      $laborFeeQty = $_POST["laborFeeQty"];
      $layoutFee = $_POST["layoutFee"];
      $layoutFeeQty = $_POST["layoutFeeQty"];
      $expenseFee = $_POST["expenseFee"];
      $expenseFeeQty = $_POST["expenseFeeQty"];
      $totalCost = $_POST["totalCostAmount"];
      $salesAmount = $_POST["salesAmount"];
      $salesDiscount = $_POST["salesDiscount"];
      $netSales = $_POST["netSales"];
      $productCost = $_POST["productCost"];
      $gross = $_POST["gross"];
      $expenses = $_POST["expenses"];
      $netIncome = $_POST["netIncome"];

      if (
        empty($salesAmount) ||
        empty($netSales) ||
        empty($productCost) ||
        empty($netIncome)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $count = count($_POST["materialID"]);
        for ($i = 0; $i < $count; $i++) {
          $materialID = $_POST["materialID"][$i];
          $materialQty = $_POST["qty"][$i];
          $connection = $this->openConnection();
          $stmt = $connection->prepare(
            "UPDATE costing_table SET `materialID` = ?, `materialQty` = ?, `laborFee` = ?, `laborQty` = ?, layoutFee = ?, layoutQty = ?, expenseFee = ?, expenseQty = ?, `productCost` = ?, `totalCost` = ?, `salesAmount` = ?, `salesDiscount` = ?, `netSales` = ?, `grossProfit` = ?, `expenses` = ?, `netIncome` = ? WHERE productID = ? AND materialID = ?"
          );
          $stmt->execute([
            $materialID,
            $materialQty,
            $laborFee,
            $laborFeeQty,
            $layoutFee,
            $layoutFeeQty,
            $expenseFee,
            $expenseFeeQty,
            $productCost,
            $totalCost,
            $salesAmount,
            $salesDiscount,
            $netSales,
            $gross,
            $expenses,
            $netIncome,
            $ID,
            $materialID,
          ]);
        }
        $row = $stmt->fetch();
        header("Location: products.php");
        return $row;
      }
    }
  }

  // display random products
  public function get_random_products()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT product.ID, productName, coverPhoto, availability, netSales, salesAmount, salesDiscount FROM (SELECT * FROM product_table) product LEFT JOIN costing_table costing ON product.ID = costing.productID GROUP BY product.ID ORDER BY RAND()"
    );
    $stmt->execute();
    $randomProducts = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $randomProducts;
    } else {
      return false;
    }
  }

  //display single product
  public function get_singleproduct($ID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT product.ID, productName, productDescription, categoryName, salesAmount, salesDiscount,netSales, netIncome, coverPhoto, image1, image2, image3, sizeGuide FROM (SELECT * FROM product_table WHERE product_table.ID = ?) product LEFT JOIN category_table category ON product.categoryID = category.ID LEFT JOIN costing_table costing ON product.ID = costing.productID"
    );
    $stmt->execute([$ID]);
    $product = $stmt->fetch();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $product;
    } else {
      return $this->show_404();
    }
  }

  //display costing to edit product page
  public function view_all_cost($ID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT materialID, materialName, unitPrice, materialQty, laborFee, laborQty, layoutFee, layoutQty, expenseFee, expenseQty, productCost, totalCost, salesAmount, salesDiscount, netSales, grossProfit, expenses, netIncome FROM costing_table costing LEFT JOIN rawmaterials_table material ON costing.materialID = material.ID WHERE costing.productID = ?"
    );
    $stmt->execute([$ID]);
    $cost = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $cost;
    } else {
      return false;
    }
  }

  //display product sizes and stocks every single product
  public function view_all_stocks($productID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT *, stocks.ID FROM product_table product LEFT JOIN stocks_table stocks ON product.ID = stocks.productID LEFT JOIN variation_table variant ON stocks.variantID = variant.ID LEFT JOIN return_table returns ON stocks.sku = returns.returnSku WHERE product.ID = ?"
    );
    $stmt->execute([$productID]);
    $stocks = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $stocks;
    } else {
      return false;
    }
  }

  //display product sizes and stocks every single variant
  public function view_all_stocks_per_variant($productID, $variantID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT * FROM stocks_table WHERE productID = ? AND variantID = ?"
    );
    $stmt->execute([$productID, $variantID]);
    $stockVar = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $stockVar;
    } else {
      return false;
    }
  }

  // delete product
  public function delete_product()
  {
    if (isset($_POST["deleteProductID"])) {
      $ID = $_POST["deleteProductID"];
      $coverPhoto = $_POST["deleteCover"];
      $image1 = $_POST["deleteImage1"];
      $image2 = $_POST["deleteImage2"];
      $image3 = $_POST["deleteImage3"];
      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "DELETE FROM product_table WHERE ID = '$ID'"
      );
      $stmt->execute();
      unlink("assets/img/" . $coverPhoto);
      if (!empty($_POST["deleteImage1"])) {
        unlink("assets/img/" . $image1);
      }
      if (!empty($_POST["deleteImage2"])) {
        unlink("assets/img/" . $image2);
      }
      if (!empty($_POST["deleteImage3"])) {
        unlink("assets/img/" . $image3);
      }
    }
  }

  // check if the customer signed in or not before checkout
  public function checkout()
  {
    if (isset($_POST["checkout"])) {
      if (!isset($_SESSION)) {
        session_start();
      }
      $_SESSION["checkout"] = null;
      unset($_SESSION["checkout"]);
      if (isset($_SESSION["userdata"])) {
        $ID = $_SESSION["userdata"]["ID"];
        header("Location: checkoutInfo.php?ID=$ID");
      } else {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Login Required',
          text: 'Please login first',
        });
        </script>";
      }
    }
  }

  // list of regions
  public function region()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare("SELECT * FROM provinces");
    $stmt->execute();
    $regions = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $regions;
    } else {
      return false;
    }
  }

  // checkout process
  public function checkout_process()
  {
    if (isset($_POST["proceedShip"])) {
      if (!isset($_SESSION)) {
        session_start();
      }
      $_SESSION["checkout"]["method"] = null;
      $_SESSION["checkout"]["sf"] = null;
      unset($_SESSION["checkout"]["method"]);
      unset($_SESSION["checkout"]["sf"]);
      $addressID = $_POST["addressID"];
      $firstName = $_POST["firstName"];
      $lastName = $_POST["lastName"];
      $address1 = $_POST["address1"];
      $address2 = $_POST["address2"];
      $city = $_POST["city"];
      $postalCode = $_POST["postalCode"];
      if (isset($_POST["region"])) {
        $region = $_POST["region"];
      }
      if (isset($_POST["country"])) {
        $country = $_POST["country"];
      }
      $phoneNumber = $_POST["phoneNumber"];
      if (isset($_POST["primaryAddress"])) {
        $primaryAddress = $_POST["primaryAddress"];
      }
      if (
        empty($firstName) ||
        empty($lastName) ||
        empty($address1) ||
        empty($city) ||
        empty($postalCode) ||
        empty(isset($_POST["region"])) ||
        empty(isset($_POST["country"])) ||
        empty($phoneNumber)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $_SESSION["checkout"]["addressID"] = $addressID;
        $_SESSION["checkout"]["firstName"] = $firstName;
        $_SESSION["checkout"]["lastName"] = $lastName;
        $_SESSION["checkout"]["address1"] = $address1;
        $_SESSION["checkout"]["address2"] = $address2;
        $_SESSION["checkout"]["city"] = $city;
        $_SESSION["checkout"]["postalCode"] = $postalCode;
        $_SESSION["checkout"]["region"] = $region;
        $_SESSION["checkout"]["country"] = $country;
        $_SESSION["checkout"]["phoneNumber"] = $phoneNumber;
        $_SESSION["checkout"]["primaryAddress"] = $primaryAddress;
        if (isset($_SESSION["userdata"])) {
          $ID = $_SESSION["userdata"]["ID"];
          header("Location: checkoutship.php?ID=$ID");
        }
      }
    }
    if (isset($_POST["proceedPayment"])) {
      if (!isset($_SESSION)) {
        session_start();
      }
      if (isset($_POST["delivery"])) {
        $delivery = $_POST["delivery"];
      }
      $sf = $_POST["sFee"];
      if (empty(isset($_POST["delivery"]))) {
        echo "<script> Swal.fire({
            icon: 'error',
            title: 'Empty Field',
            text: 'Please select shipping method',
          });
          </script>";
      } else {
        $_SESSION["checkout"]["method"] = $delivery;
        $_SESSION["checkout"]["sf"] = $sf;
        if (isset($_SESSION["userdata"])) {
          $ID = $_SESSION["userdata"]["ID"];
          header("Location: checkoutpayment.php?ID=$ID");
        }
      }
    }
    if (isset($_POST["backShip"])) {
      if (!isset($_SESSION)) {
        session_start();
      }
      $_SESSION["checkout"]["method"] = null;
      $_SESSION["checkout"]["sf"] = null;
      unset($_SESSION["checkout"]["method"]);
      unset($_SESSION["checkout"]["sf"]);
      if (isset($_SESSION["userdata"])) {
        $ID = $_SESSION["userdata"]["ID"];
        header("Location: checkoutship.php?ID=$ID");
      }
    }
    if (isset($_POST["proceedReview"])) {
      if (!isset($_SESSION)) {
        session_start();
      }
      if (isset($_POST["payment"])) {
        $payment = $_POST["payment"];
      }
      if (empty(isset($_POST["payment"]))) {
        echo "<script> Swal.fire({
            icon: 'error',
            title: 'Empty Field',
            text: 'Please select payment method',
          });
          </script>";
      } else {
        $_SESSION["checkout"]["payment"] = $payment;
        if (isset($_SESSION["userdata"])) {
          $ID = $_SESSION["userdata"]["ID"];
          header("Location: checkoutreview.php?ID=$ID");
        }
      }
    }
  }

  // get checkout
  public function get_checkout()
  {
    if (!isset($_SESSION)) {
      session_start();
    }
    if (isset($_SESSION["checkout"])) {
      return $_SESSION["checkout"];
    } else {
      return null;
    }
  }

  // sales
  public function sales()
  {
    if (isset($_POST["complete"])) {
      $count = count($_POST["productID"]);
      $orderID = $_POST["orderID"];
      $shipMethod = $_POST["deliveryMethod"];
      $shipFee = $_POST["sf"];
      $paymentMethod = $_POST["payment"];
      $totalAmount = $_POST["totalAmount"];
      $acctID = $_POST["acctID"];
      $paymentStatus = $_POST["paymentStatus"];
      $orderStatus = $_POST["orderStatus"];
      $addressID = $_POST["addressID"];

      for ($i = 0; $i < $count; $i++) {
        $productID = $_POST["productID"][$i];
        $stockID = $_POST["stockID"][$i];
        $salesQty = $_POST["salesQty"][$i];
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO sales_table ( `orderID`,`productID`, `stockID`, `salesQty`, `shipMethod`, `shipFee`, `paymentMethod`, `totalAmount`, `accountId`, `paymentStatus`, `orderStatus`, `addressID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
          $orderID,
          $productID,
          $stockID,
          $salesQty,
          $shipMethod,
          $shipFee,
          $paymentMethod,
          $totalAmount,
          $acctID,
          $paymentStatus,
          $orderStatus,
          $addressID,
        ]);
      }
      if (!isset($_SESSION)) {
        session_start();
      }
      $_SESSION["checkout"] = null;
      unset($_SESSION["checkout"]);
      if (isset($_SESSION["userdata"])) {
        $ID = $_SESSION["userdata"]["ID"];
        header("Location: ordercompleted.php?ID=$ID");
      }
    }
  }

  // shipping address
  public function shipping_address()
  {
    if (isset($_POST["complete"])) {
      $orderID = $_POST["orderID"];
      $acctID = $_POST["acctID"];
      $addressID = $_POST["addressID"];
      $firstName = $_POST["firstName"];
      $lastName = $_POST["lastName"];
      $address1 = $_POST["address1"];
      $address2 = $_POST["address2"];
      $city = $_POST["city"];
      $postalCode = $_POST["postalCode"];
      $region = $_POST["region"];
      $country = $_POST["country"];
      $phoneNumber = $_POST["phoneNumber"];
      if (isset($_POST["primaryAddress"])) {
        $primaryAddress = $_POST["primaryAddress"];
      }

      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "INSERT INTO address_table (`orderID`,`addressID`, `firstName`, `lastName`, `address1`, `address2`, `city`, `postalCode`, `region`, `country`, `phoneNumber`, `addressType`, `accountID`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)"
      );
      $stmt->execute([
        $orderID,
        $addressID,
        $firstName,
        $lastName,
        $address1,
        $address2,
        $city,
        $postalCode,
        $region,
        $country,
        $phoneNumber,
        $primaryAddress,
        $acctID,
      ]);
      if (!isset($_SESSION)) {
        session_start();
      }
      $_SESSION["checkout"] = null;
      unset($_SESSION["checkout"]);
      if (isset($_SESSION["userdata"])) {
        $ID = $_SESSION["userdata"]["ID"];
        header("Location: ordercompleted.php?ID=$ID");
      }
    }
  }

  // get shipping address
  public function get_address($ID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT DISTINCT addressID, firstName, lastName, address1, address2, city, postalCode, region, country, phoneNumber, addressType FROM address_table WHERE accountID = ? ORDER BY ID DESC"
    );
    $stmt->execute([$ID]);
    $address = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $address;
    } else {
      return false;
    }
  }

  // display order
  public function get_orders()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT orderID, accountID, firstName, lastName, email, variantImage, variantName, productName, size, salesQty, totalAmount, paymentMethod, paymentStatus, shipMethod, orderStatus, orderDate, addressID FROM sales_table sales  LEFT JOIN account_table account ON sales.accountID = account.ID LEFT JOIN product_table product ON sales.productID = product.ID LEFT JOIN stocks_table stocks ON sales.stockID = stocks.ID LEFT JOIN variation_table variant ON stocks.variantID = variant.ID ORDER BY orderDate DESC"
    );
    $stmt->execute();
    $orders = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $orders;
    } else {
      return false;
    }
  }

  // get order id for track orders
  public function get_orderID($ID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT orderID FROM sales_table WHERE accountID = ? ORDER BY ID DESC"
    );
    $stmt->execute([$ID]);
    $orderID = $stmt->fetch();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $orderID;
    } else {
      return false;
    }
  }

  // get order by customers
  public function get_order_customer($ID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT *, DATE_FORMAT(orderDate, '%M %d, %Y') as orderDate FROM sales_table WHERE accountID = ? GROUP BY orderID ORDER BY ID DESC"
    );
    $stmt->execute([$ID]);
    $orders = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $orders;
    } else {
      return false;
    }
  }

  // track orders
  public function track_order($acctID, $orderID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT sales.orderID, salesQty, shipMethod, shipFee, paymentMethod, totalAmount, variantImage, variantName, productName, netSales, size, orderStatus, account.firstName as accountFname, account.lastName as accountLname, email, shipAddress.firstName as addressFname, shipAddress.lastName as addressLname, address1, address2, city, postalCode, region, country, phoneNumber FROM sales_table sales LEFT JOIN account_table account ON sales.accountID = account.ID LEFT JOIN product_table product ON sales.productID = product.ID LEFT JOIN stocks_table stock ON sales.stockID = stock.ID  LEFT JOIN variation_table variant ON stock.variantID = variant.ID LEFT JOIN costing_table costing ON sales.productID = costing.productID LEFT JOIN address_table shipAddress ON sales.orderID = shipAddress.orderID WHERE sales.accountID = ?  AND sales.orderID = ? GROUP BY stock.ID"
    );
    $stmt->execute([$acctID, $orderID]);
    $track = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $track;
    } else {
      return $this->show_404();
    }
  }

  // print receipt
  public function invoice($orderID, $addressID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT sales.orderID, account.firstName as firstName, account.lastName as lastName, email, variantImage, productName, variantName, size, sku, salesQty, totalAmount, paymentMethod, netSales, shipMethod, shipFee, orderDate, shipAddress.firstName as addressFname, shipAddress.lastName as addressLname, address1, address2, city, postalCode, region, country, phoneNumber FROM sales_table sales  LEFT JOIN account_table account ON sales.accountID = account.ID LEFT JOIN product_table product ON sales.productID = product.ID LEFT JOIN stocks_table stocks ON sales.stockID = stocks.ID LEFT JOIN variation_table variant ON stocks.variantID = variant.ID LEFT JOIN costing_table costing ON sales.productID = costing.productID LEFT JOIN address_table shipAddress ON sales.orderID = shipAddress.orderID WHERE sales.orderID = ? AND sales.addressID = ? GROUP BY sales.stockID"
    );
    $stmt->execute([$orderID, $addressID]);
    $order = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $order;
    } else {
      return $this->show_dashboard404();
    }
  }

  // count pending orders
  public function count_orders()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT COUNT(DISTINCT(orderID)) FROM sales_table WHERE orderStatus = 'Placed'"
    );
    $stmt->execute();
    $orders = $stmt->fetchColumn();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $orders;
    } else {
      return false;
    }
  }

  // get pending orders
  public function get_pending_orders()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT orderID, DATE_FORMAT(orderDate, '%M %d, %Y - %h:%i %p') as orderDate FROM sales_table WHERE orderStatus = 'Placed' GROUP BY orderID ORDER BY orderDate DESC LIMIT 3"
    );
    $stmt->execute();
    $pendingOrders = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $pendingOrders;
    } else {
      return false;
    }
  }

  // update order status
  public function update_order_status()
  {
    if (isset($_POST["updateOrderStatus"])) {
      $orderID = $_POST["orderID"];
      if (isset($_POST["orderStatus"])) {
        $orderStatus = $_POST["orderStatus"];
      }
      if (empty(isset($_POST["orderStatus"]))) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please select status',
        });
        </script>";
      }
      if (isset($_POST["orderStatus"])) {
        if (
          $_POST["orderStatus"] === "Delivered" &&
          $_POST["paymentMethod"] === "Cash on Delivery (COD)"
        ) {
          $connection = $this->openConnection();
          $stmt = $connection->prepare(
            "UPDATE sales_table SET `paymentStatus` = ?, `orderStatus` = ? WHERE orderID = '$orderID'"
          );
          $stmt->execute(["Paid", $orderStatus]);
          $row = $stmt->fetch();
          echo "<script>
                Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Order Status Updated',
                showConfirmButton: false,
                timer: 1000
                });
            </script>";
          return $row;
        } else {
          $connection = $this->openConnection();
          $stmt = $connection->prepare(
            "UPDATE sales_table SET `orderStatus` = ? WHERE orderID = '$orderID'"
          );
          $stmt->execute([$orderStatus]);
          $row = $stmt->fetch();
          echo "<script>
                  Swal.fire({
                  position: 'center',
                  icon: 'success',
                  title: 'Order Status Updated',
                  showConfirmButton: false,
                  timer: 1000
                  });
              </script>";
          return $row;
        }
      }
      if (isset($_POST["orderStatus"])) {
        if ($_POST["orderStatus"] === "Processing") {
          $connection = $this->openConnection();
          $stmt = $connection->prepare(
            "UPDATE sales_table SET `orderStatus` = ? WHERE orderID = '$orderID'"
          );
          $stmt->execute([$orderStatus]);
          $row = $stmt->fetch();
          echo "<script>
              Swal.fire({
              position: 'center',
              icon: 'success',
              title: 'Order Status Updated',
              showConfirmButton: false,
              timer: 1000
              });
          </script>";
          return $row;
        }
      }
      if (isset($_POST["orderStatus"])) {
        if ($_POST["orderStatus"] === "Shipped") {
          $connection = $this->openConnection();
          $stmt = $connection->prepare(
            "UPDATE sales_table SET `orderStatus` = ? WHERE orderID = '$orderID'"
          );
          $stmt->execute([$orderStatus]);
          $row = $stmt->fetch();
          echo "<script>
              Swal.fire({
              position: 'center',
              icon: 'success',
              title: 'Order Status Updated',
              showConfirmButton: false,
              timer: 1000
              });
          </script>";
          return $row;
        }
      }
    }
  }

  // cancel order
  public function cancel_order()
  {
    if (isset($_POST["paymentMethod"])) {
      if ($_POST["paymentMethod"] === "Cash on Delivery (COD)") {
        $orderID = $_POST["orderID"];
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE sales_table SET `paymentStatus` = ?, `orderStatus` = ? WHERE orderID = '$orderID'"
        );
        $stmt->execute(["Cancelled", "Cancelled"]);
        $row = $stmt->fetch();

        $customerName = $_POST["userName"];
        $mailToCustomer = $_POST["userEmail"];
        $mailTo = "sigornylagdameo@gmail.com";
        $body =
          "We are sorry to hear that you cancelled your Order #:" .
          $orderID .
          ". Please take note that for GCASH and bank payment method refund will take up to 5-7 business days. We are hoping to shop with you soon.";

          $mail = new PHPMailer\PHPMailer\PHPMailer();
          $mail->SMTPDebug = 0;
          $mail->isSMTP();
          $mail->Host = "smtp.gmail.com";
          $mail->SMTPAuth = true;
          $mail->Username = "dlcollections@gmail.com";
          $mail->Password = "admin123";
          $mail->SMTPSecure = "tls";
          $mail->Port = 587;
          $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
          $mail->addAddress("sigornylagdameo@gmail.com", "Sigorny");
          $mail->addCC($mailTo, $customerName);
          $mail->isHTML(true);
          $mail->Subject = "DL-Collections - Order";
          $mail->Body = $body;

        if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
        }

        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return $row;
      } else {
        $orderID = $_POST["orderID"];
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE sales_table SET `orderStatus` = ? WHERE orderID = '$orderID'"
        );
        $stmt->execute(["Cancelled"]);
        $row = $stmt->fetch();

        $customerName = $_POST["userName"];
        $mailToCustomer = $_POST["userEmail"];
        $mailTo = "sigornylagdameo5@gmail.com";
        $body =
          "We are sorry to hear that you cancelled your Order #:" .
          $orderID .
          ". Please take note that for GCASH and bank payment method refund will take up to 5-7 business days. We are hoping to shop with you soon.";

          $mail = new PHPMailer\PHPMailer\PHPMailer();
          $mail->SMTPDebug = 0;
          $mail->isSMTP();
          $mail->Host = "smtp.gmail.com";
          $mail->SMTPAuth = true;
          $mail->Username = "dlcollections@gmail.com";
          $mail->Password = "admin123";
          $mail->SMTPSecure = "tls";
          $mail->Port = 587;
          $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
          $mail->addAddress("sigornylagdameo@gmail.com", "Sigorny");
          $mail->addCC($mailTo, $customerName);
          $mail->isHTML(true);
          $mail->Subject = "DL-Collections - Order";
          $mail->Body = $body;

        if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
        }

        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return $row;
      }
    }
  }

  // display sales
  public function get_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT sales.productID as productID, orderID, productName, variantName, size, salesQty, totalAmount,orderDate FROM sales_table sales LEFT JOIN product_table product ON sales.productID = product.ID LEFT JOIN stocks_table stocks ON sales.stockID = stocks.ID LEFT JOIN variation_table variant ON stocks.variantID = variant.ID WHERE paymentStatus = 'Paid' ORDER BY orderDate DESC"
    );
    $stmt->execute();
    $sales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $sales;
    } else {
      return false;
    }
  }

  // display cod payments
  public function cod_payments()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT orderID, firstName, lastName, totalAmount, orderDate FROM sales_table sales LEFT JOIN account_table account ON sales.accountID = account.ID WHERE paymentMethod = 'Cash on Delivery (COD)'AND paymentStatus = 'Paid'  GROUP BY orderID ORDER BY orderDate DESC"
    );
    $stmt->execute();
    $cod = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $cod;
    } else {
      return false;
    }
  }

  // display online payments
  public function online_payments()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT orderID, firstName, lastName, totalAmount, orderDate FROM sales_table sales LEFT JOIN account_table account ON sales.accountID = account.ID WHERE paymentMethod = 'GCASH or Credit / Debit Card' AND paymentStatus = 'Paid'  GROUP BY orderID ORDER BY orderDate DESC"
    );
    $stmt->execute();
    $cod = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $cod;
    } else {
      return false;
    }
  }

  // display top selling products
  public function get_top_selling_products()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT sales.productID as productID, productName, variantName, size, SUM(salesQty) as salesQty FROM sales_table sales LEFT JOIN product_table product ON sales.productID = product.ID LEFT JOIN stocks_table stocks ON sales.stockID = stocks.ID LEFT JOIN variation_table variant ON stocks.variantID = variant.ID WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND
      MONTH(orderDate) = MONTH(CURRENT_DATE()) GROUP BY sales.stockID ORDER BY SUM(salesQty) DESC LIMIT 5"
    );
    $stmt->execute();
    $topSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $topSales;
    } else {
      return false;
    }
  }

  // display top selling categories
  public function get_top_selling_category()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT categoryName, SUM(salesQty) as salesQty FROM sales_table sales LEFT JOIN product_table product ON sales.productID = product.ID LEFT JOIN category_table category ON product.categoryID = category.ID WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = MONTH(CURRENT_DATE()) GROUP BY categoryName ORDER BY SUM(salesQty) DESC LIMIT 5"
    );
    $stmt->execute();
    $topCategories = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $topCategories;
    } else {
      return false;
    }
  }

  // display total sales today
  public function get_total_sales_today()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount, DATE_FORMAT(orderDate, '%Y-%m-%d') FROM sales_table WHERE paymentStatus = 'Paid' AND DATE(orderDate) = CURDATE() GROUP BY orderID"
    );
    $stmt->execute();
    $totalSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $totalSales;
    } else {
      return false;
    }
  }

  // display total sales this month
  public function get_sales_this_month()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = MONTH(CURRENT_DATE()) GROUP BY orderID"
    );
    $stmt->execute();
    $salesMonth = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $salesMonth;
    } else {
      return false;
    }
  }

  // get total net income
  public function get_total_net_income()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT productID, salesQty as salesQty FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = MONTH(CURRENT_DATE())"
    );
    $stmt->execute();
    $totalIncome = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $totalIncome;
    } else {
      return false;
    }
  }

  // display total sales this jan
  public function get_jan_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 01 GROUP BY orderID"
    );
    $stmt->execute();
    $janSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $janSales;
    } else {
      return false;
    }
  }

  // display total sales this feb
  public function get_feb_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 02 GROUP BY orderID"
    );
    $stmt->execute();
    $febSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $febSales;
    } else {
      return false;
    }
  }

  // display total sales this mar
  public function get_mar_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 03 GROUP BY orderID"
    );
    $stmt->execute();
    $marSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $marSales;
    } else {
      return false;
    }
  }

  // display total sales this apr
  public function get_apr_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 04 GROUP BY orderID"
    );
    $stmt->execute();
    $aprSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $aprSales;
    } else {
      return false;
    }
  }

  // display total sales this may
  public function get_may_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 05 GROUP BY orderID"
    );
    $stmt->execute();
    $maySales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $maySales;
    } else {
      return false;
    }
  }

  // display total sales this jun
  public function get_jun_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 06 GROUP BY orderID"
    );
    $stmt->execute();
    $junSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $junSales;
    } else {
      return false;
    }
  }

  // display total sales this jul
  public function get_jul_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 07 GROUP BY orderID"
    );
    $stmt->execute();
    $julSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $julSales;
    } else {
      return false;
    }
  }

  // display total sales this aug
  public function get_aug_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 08 GROUP BY orderID"
    );
    $stmt->execute();
    $augSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $augSales;
    } else {
      return false;
    }
  }

  // display total sales this sep
  public function get_sep_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 09 GROUP BY orderID"
    );
    $stmt->execute();
    $sepSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $sepSales;
    } else {
      return false;
    }
  }

  // display total sales this oct
  public function get_oct_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 10 GROUP BY orderID"
    );
    $stmt->execute();
    $octSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $octSales;
    } else {
      return false;
    }
  }

  // display total sales this nov
  public function get_nov_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 11 GROUP BY orderID"
    );
    $stmt->execute();
    $novSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $novSales;
    } else {
      return false;
    }
  }

  // display total sales this dec
  public function get_dec_sales()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(DISTINCT totalAmount) as totalAmount FROM sales_table WHERE paymentStatus = 'Paid' AND YEAR(orderDate) = YEAR(CURRENT_DATE()) AND 
      MONTH(orderDate) = 12 GROUP BY orderID"
    );
    $stmt->execute();
    $decSales = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $decSales;
    } else {
      return false;
    }
  }

  // display sold products
  public function sold_products($productID, $stockID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(salesQty) as salesQty FROM sales_table WHERE productID = ? AND stockID = ? AND (NOT orderStatus = 'Cancelled' OR NOT paymentStatus = 'Cancelled') GROUP BY productID AND stockID"
    );
    $stmt->execute([$productID, $stockID]);
    $soldProducts = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $soldProducts;
    } else {
      return false;
    }
  }

  // display added inventory products
  public function get_added_stock_products($productID, $stockID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT stock, SUM(addedQty) as addedQty FROM inventoryproduct_table inventory LEFT JOIN stocks_table stock ON inventory.stockID = stock.ID LEFT JOIN product_table product ON inventory.productID = product.ID WHERE inventory.productID = ? AND inventory.stockID = ? GROUP BY inventory.productID AND inventory.stockID"
    );
    $stmt->execute([$productID, $stockID]);
    $addedInventoryProducts = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $addedInventoryProducts;
    } else {
      return false;
    }
  }

  // add raw materials
  public function add_material()
  {
    if (isset($_POST["addMaterials"])) {
      $materialName = $_POST["materialName"];
      $unitPrice = $_POST["unitPrice"];
      $stockQty = $_POST["stockQty"];
      if (isset($_POST["supplierID"])) {
        $supplierID = $_POST["supplierID"];
      }

      if (
        empty($materialName) ||
        empty($unitPrice) ||
        empty($stockQty) ||
        empty(isset($_POST["supplierID"]))
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO rawmaterials_table (`materialName` , `unitPrice` , `stockQty` , `supplierID`) VALUES (?,?,?,?)"
        );
        $stmt->execute([$materialName, $unitPrice, $stockQty, $supplierID]);
        echo "<script>  
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Material Added',
            showConfirmButton: false,
            timer: 1000
          });
        </script>";
      }
    }
  }

  // display raw materials
  public function get_all_materials()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT materials.ID, materialName, unitPrice, stockQty, supplierID, supplierName, supplierEmail FROM rawmaterials_table materials LEFT JOIN supplier_table supplier ON materials.supplierID = supplier.ID ORDER BY ID DESC"
    );
    $stmt->execute();
    $materials = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $materials;
    } else {
      return false;
    }
  }

  // display used raw materials
  public function used_materials($rawID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT SUM(materialQty) as materialQty FROM costing_table WHERE costing_table.materialID = ? GROUP BY costing_table.materialID"
    );
    $stmt->execute([$rawID]);
    $usedMaterials = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $usedMaterials;
    } else {
      return false;
    }
  }

  // display added inventory raw materials
  public function get_added_stock_materials($rawID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT stockQty, SUM(addedQty) as addedQty FROM inventorymaterial_table inventory LEFT JOIN rawmaterials_table materials ON inventory.materialID = materials.ID WHERE inventory.materialID = ? GROUP BY inventory.materialID"
    );
    $stmt->execute([$rawID]);
    $addedInventoryMaterials = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $addedInventoryMaterials;
    } else {
      return false;
    }
  }

  // update raw materials
  public function update_material()
  {
    if (isset($_POST["updateMaterial"])) {
      $materialID = $_POST["materialID"];
      $materialName = $_POST["materialName"];
      $unitPrice = $_POST["unitPrice"];
      $supplierID = $_POST["supplierID"];

      if (empty($materialName) || empty($unitPrice)) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE rawmaterials_table SET `materialName` = ?, `unitPrice` = ?, `supplierID` = ? WHERE ID = '$materialID'"
        );
        $stmt->execute([$materialName, $unitPrice, $supplierID]);
        $row = $stmt->fetch();
        echo "<script>  
      Swal.fire({
        position: 'center',
        icon: 'success',
        title: 'Material Updated',
        showConfirmButton: false,
        timer: 1000
      });
      </script>";
        return $row;
      }
    }
  }

  // inventory product
  public function add_inventory_products()
  {
    if (isset($_POST["addInventoryProducts"])) {
      $stockID = $_POST["stockID"];
      $addedStockQty = $_POST["addedStockQty"];
      if (isset($_POST["products"])) {
        $products = $_POST["products"];
      }

      if (empty($addedStockQty) || empty(isset($_POST["products"]))) {
        echo "<script> Swal.fire({
            icon: 'error',
            title: 'Empty Field',
            text: 'Please input missing field',
          });
          </script>";
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO inventoryproduct_table (`productID`, `stockID`, `addedQty`) VALUES (?,?,?)"
        );
        $stmt->execute([$products, $stockID, $addedStockQty]);
        echo "<script>  
            Swal.fire({
              position: 'center',
              icon: 'success',
              title: 'Inventory Added',
              showConfirmButton: false,
              timer: 1000
            });
          </script>";
      }
    }
  }

  // display inventory added products
  public function get_inventory_products_added()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT inventory.ID , inventory.productID, inventory.stockID, productName, variantName, size, addedQty, dateTime FROM (SELECT * FROM inventoryproduct_table) inventory LEFT JOIN product_table product ON inventory.productID = product.ID LEFT JOIN stocks_table stocks ON inventory.stockID = stocks.ID LEFT JOIN variation_table variant ON stocks.variantID = variant.ID ORDER BY ID DESC"
    );
    $stmt->execute();
    $inventoryProducts = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $inventoryProducts;
    } else {
      return false;
    }
  }

  // inventory raw material
  public function add_inventory_materials()
  {
    if (isset($_POST["addInventoryMaterials"])) {
      $addedStockQty = $_POST["addedStockQty"];
      if (isset($_POST["material"])) {
        $material = $_POST["material"];
      }

      if (empty($addedStockQty) || empty(isset($_POST["material"]))) {
        echo "<script> Swal.fire({
            icon: 'error',
            title: 'Empty Field',
            text: 'Please input missing field',
          });
          </script>";
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO inventorymaterial_table (`materialID` , `addedQty`) VALUES (?,?)"
        );
        $stmt->execute([$material, $addedStockQty]);
        echo "<script>  
            Swal.fire({
              position: 'center',
              icon: 'success',
              title: 'Inventory Added',
              showConfirmButton: false,
              timer: 1000
            });
          </script>";
      }
    }
  }

  // display inventory added raw materials
  public function get_inventory_materials_added()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT inventory.ID, materialID, materialName, addedQty, dateTime FROM (SELECT * FROM inventorymaterial_table) inventory LEFT JOIN rawmaterials_table materials ON inventory.materialID = materials.ID ORDER BY ID DESC"
    );
    $stmt->execute();
    $inventoryMaterials = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $inventoryMaterials;
    } else {
      return false;
    }
  }

  // add supplier
  public function add_supplier()
  {
    if (isset($_POST["supplier"])) {
      $supplierName = $_POST["supplierName"];
      $supplierEmail = $_POST["supplierEmail"];
      $supplierAddress = $_POST["supplierAddress"];
      $supplierContactNo = $_POST["supplierContactNo"];

      if (
        empty($supplierName) ||
        empty($supplierEmail) ||
        empty($supplierAddress) ||
        empty($supplierContactNo)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO supplier_table (`supplierName` , supplierEmail, `supplierAddress` , `supplierContactNumber`) VALUES (?,?,?,?)"
        );
        $stmt->execute([
          $supplierName,
          $supplierEmail,
          $supplierAddress,
          $supplierContactNo,
        ]);
        echo "<script>  
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Supplier Added',
            showConfirmButton: false,
            timer: 1000
          });
        </script>";
      }
    }
  }

  // display supplier
  public function get_all_supplier()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT * FROM supplier_table ORDER BY ID DESC"
    );
    $stmt->execute();
    $supplier = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $supplier;
    } else {
      return false;
    }
  }

  // update supplier
  public function update_supplier()
  {
    if (isset($_POST["updateSupplier"])) {
      $supplierID = $_POST["supplierID"];
      $supplierName = $_POST["supplierName"];
      $supplierEmail = $_POST["supplierEmail"];
      $supplierAddress = $_POST["supplierAddress"];
      $supplierContactNo = $_POST["supplierContactNo"];

      if (
        empty($supplierName) ||
        empty($supplierEmail) ||
        empty($supplierAddress) ||
        empty($supplierContactNo)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE supplier_table SET `supplierName` = ?, `supplierEmail` = ?, `supplierAddress` = ?, `supplierContactNumber` = ? WHERE ID = '$supplierID'"
        );
        $stmt->execute([
          $supplierName,
          $supplierEmail,
          $supplierAddress,
          $supplierContactNo,
        ]);
        $row = $stmt->fetch();
        echo "<script>  
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Supplier Updated',
          showConfirmButton: false,
          timer: 1000
        });
        </script>";
        return $row;
      }
    }
  }

  //email notification (subscribe)
  public function subscribe()
  {
    if (isset($_POST["subscribe"])) {
      $email = $_POST["email"];
      $mailTo = $email;
      $body =
        "Thank you for subscribing <b>DL-Collections</b> We will make sure to send to you every latest updates. Enjoy DOS Shoppers!";

      if (empty($email)) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please enter email address',
        });
        </script>";
      } else {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "dlcollections@gmail.com";
        $mail->Password = "admin123";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
        $mail->addAddress("sigornylagdameo@gmail.com", "Sigorny");
        $mail->addCC($mailTo, $customerName);
        $mail->isHTML(true);
        $mail->Subject = "DL-Collections - Order";
        $mail->Body = $body;

        if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
          echo "<script>  
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Email has been sent',
            showConfirmButton: false,
            timer: 1000
          });
          </script>";
        }
      }
    }
  }

  // contact us
  public function contact_us()
  {
    if (isset($_POST["contactSubmit"])) {
      $name = $_POST["customerName"];
      $email = $_POST["email"];
      $message = $_POST["message"];

      if (empty($name) || empty($email) || empty($message)) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field.',
        });
        </script>";
      } else {
        $mailTo = "sigornylagdameo@gmail.com";
        $body = $_POST["message"];
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "dlcollections@gmail.com";
        $mail->Password = "admin123";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
        $mail->addAddress("sigornylagdameo@gmail.com", "Sigorny");
        $mail->addCC($mailTo, $customerName);
        $mail->isHTML(true);
        $mail->Subject = "DL-Collections - Order";
        $mail->Body = $body;

        if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
          echo "<script>  
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Message has been sent',
            showConfirmButton: false,
            timer: 1000
          });
          </script>";
        }
      }
    }
  }

  // forgot password
  public function forgot_password()
  {
    if (isset($_POST["sendEmail"])) {
      $email = $_POST["customerEmail"];
      if (empty($email)) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please enter email address',
        });
        </script>";
      } elseif ($this->checkEmail($email) == 0) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Email address is not registered',
        });
        </script>";
      } else {
        $mailTo = $email;
        $code1 = $_POST["code1"];
        $code2 = $_POST["code2"];
        $code3 = $_POST["code3"];
        $code4 = $_POST["code4"];
        $body =
          "Your verificaton code is " . $code1 . $code2 . $code3 . $code4 . ".";
          $mail = new PHPMailer\PHPMailer\PHPMailer();
          $mail->SMTPDebug = 0;
          $mail->isSMTP();
          $mail->Host = "smtp.gmail.com";
          $mail->SMTPAuth = true;
          $mail->Username = "dlcollections@gmail.com";
          $mail->Password = "admin123";
          $mail->SMTPSecure = "tls";
          $mail->Port = 587;
          $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
          $mail->addAddress("sigornylagdameo@gmail.com", "Sigorny");
          $mail->addCC($mailTo, $customerName);
          $mail->isHTML(true);
          $mail->Subject = "DL-Collections - Order";
          $mail->Body = $body;

        if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
          if (!isset($_SESSION)) {
            session_start();
          }
          $_SESSION["code"] = [
            "email" => $email,
            "code1" => $code1,
            "code2" => $code2,
            "code3" => $code3,
            "code4" => $code4,
          ];
          header("Location: verify.php");
        }
      }
    }

    if (isset($_POST["verifyCode"])) {
      if (!isset($_SESSION)) {
        session_start();
      }
      $verifyCode1 = $_POST["verifyCode1"];
      $verifyCode2 = $_POST["verifyCode2"];
      $verifyCode3 = $_POST["verifyCode3"];
      $verifyCode4 = $_POST["verifyCode4"];

      if (
        empty($verifyCode1) ||
        empty($verifyCode2) ||
        empty($verifyCode3) ||
        empty($verifyCode4)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field.',
        });
        </script>";
      } elseif (
        $verifyCode1 === $_SESSION["code"]["code1"] &&
        $verifyCode2 === $_SESSION["code"]["code2"] &&
        $verifyCode3 === $_SESSION["code"]["code3"] &&
        $verifyCode4 === $_SESSION["code"]["code4"]
      ) {
        header("Location: newpassword.php");
      } else {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Error Code',
          text: 'Verification code does not match.',
        });
        </script>";
      }
    }

    if (isset($_POST["resetPass"])) {
      if (!isset($_SESSION)) {
        session_start();
      }
      $newPass = md5($_POST["newPass"]);
      $confirmPass = md5($_POST["confirmPass"]);

      if (empty($_POST["newPass"]) || empty($_POST["confirmPass"])) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field.',
        });
        </script>";
      } elseif ($_POST["newPass"] !== $_POST["confirmPass"]) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Password does not match.',
        });
        </script>";
      } elseif (strlen($_POST["newPass"]) < 8) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Password must be at least 8 characters.',
        });
        </script>";
      } else {
        $email = $_SESSION["code"]["email"];
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "UPDATE account_table SET `password` = ? WHERE email = '$email'"
        );
        $stmt->execute([$newPass]);
        $row = $stmt->fetch();
        $_SESSION["code"] = null;
        unset($_SESSION["code"]);
        header("Location: login.php");
      }
    }
  }

  //return order
  public function return_order()
  {
    if (isset($_POST["returnSubmit"])) {
      $email = $_POST["email"];
      $name = $_POST["userName"];
      $userID = $_POST["userID"];
      $orderID = $_POST["orderID"];
      $returnSku = $_POST["returnSku"];
      $returnQty = $_POST["returnQty"];
      $returnImg = $_FILES["returnImg"]["name"];
      $returnStatus = $_POST["returnStatus"];

      if (isset($_POST["reason"])) {
        $reason = $_POST["reason"];
      }
      $comment = $_POST["comment"];

      if (
        empty($orderID) ||
        empty($returnSku) ||
        empty(isset($_POST["reason"])) ||
        empty($returnQty) ||
        empty($returnImg)
      ) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } elseif (is_numeric($returnQty) || is_int($returnQty)) {
        $mailTo = "sigornylagdameo@gmail.com";
        $body =
          $name .
          " is requesting to return an item/s <br> <br> <b>Order #:</b> " .
          $orderID .
          "<br> <b>SKU:</b> " .
          $returnSku .
          "<br> <b>Reason:</b> " .
          $reason .
          "<br> <b>Quantity:</b> " .
          $returnQty .
          "<br> <b>Comments:</b> " .
          $comment;
          $mail = new PHPMailer\PHPMailer\PHPMailer();
          $mail->SMTPDebug = 0;
          $mail->isSMTP();
          $mail->Host = "smtp.gmail.com";
          $mail->SMTPAuth = true;
          $mail->Username = "dlcollections@gmail.com";
          $mail->Password = "admin123";
          $mail->SMTPSecure = "tls";
          $mail->Port = 587;
          $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
          $mail->addAddress("sigornylagdameo@gmail.com", "Sigorny");
          $mail->addCC($mailTo, $customerName);
          $mail->isHTML(true);
          $mail->Subject = "DL-Collections - Order";
          $mail->Body = $body;

        if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
          echo "<script>
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Request has been sent',
            showConfirmButton: false,
            timer: 1000
          });
          </script>";
        }
        $connection = $this->openConnection();
        $stmt = $connection->prepare(
          "INSERT INTO return_table (`returnOrderId`, `returnSku`, `reason`, `qty`, `comments`, `returnImg`, `userID`, `status`) VALUES (?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
          $orderID,
          $returnSku,
          $reason,
          $returnQty,
          $comment,
          $returnImg,
          $userID,
          $returnStatus,
        ]);
        move_uploaded_file(
          $_FILES["returnImg"]["tmp_name"],
          "assets/img/" . $returnImg
        );
      } else {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Please input number only',
        });
        </script>";
      }
    }
  }

  // display return order
  public function get_return_orders()
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT returns.ID as ID, orderID, firstName, lastName, productName, variantName, variantImage, size, qty, reason, comments, returnImg, status FROM return_table returns LEFT JOIN stocks_table stocks ON returns.returnSku = stocks.sku LEFT JOIN variation_table variant ON stocks.variantID = variant.ID LEFT JOIN product_table product ON stocks.productID = product.ID LEFT JOIN account_table account ON returns.userID = account.ID LEFT JOIN sales_table sales ON returns.returnOrderID = sales.orderID GROUP BY returnSku ORDER BY returns.ID DESC"
    );
    $stmt->execute();
    $return_orders = $stmt->fetchall();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $return_orders;
    } else {
      return false;
    }
  }

  // accept return orders
  public function accept_return_orders()
  {
    if (isset($_POST["acceptReturnID"])) {
      $ID = $_POST["acceptReturnID"];
      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "UPDATE return_table SET `status` = 'Accepted' WHERE ID = '$ID'"
      );
      $stmt->execute();
      $row = $stmt->fetch();
      header("Location: return_orders.php");
      return $row;
    }
  }

  // reject return orders
  public function reject_return_orders()
  {
    if (isset($_POST["rejectReturnID"])) {
      $ID = $_POST["rejectReturnID"];
      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "UPDATE return_table SET `status` = 'Rejected' WHERE ID = '$ID'"
      );
      $stmt->execute();
      $row = $stmt->fetch();
      header("Location: return_orders.php");
      return $row;
    }
  }

  //order notification to admin
  public function placed_order_email()
  {
    if (isset($_POST["complete"])) {
      $orderID = $_POST["orderID"];
      $customerName = $_POST["userName"];
      $mailToCustomer = $_POST["userEmail"];
      $mailTo = "sigornylagdameo@gmail.com";
      $body =
        "Hi, 
      This is DL-Collections Your order with Order #:" .
        $orderID .
        " has been placed. We will process your order as soon as possible. For inquiries, please free to message us. Thank you for shopping with us.";

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "dlcollections@gmail.com";
        $mail->Password = "admin123";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
        $mail->addAddress("sigornylagdameo@gmail.com", "Sigorny");
        $mail->addCC($mailTo, $customerName);
        $mail->isHTML(true);
        $mail->Subject = "DL-Collections - Order";
        $mail->Body = $body;

      if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
      }
    }
  }

  //update order status and email customer
  public function orderStatus_email_notification()
  {
    if (isset($_POST["updateOrderStatus"])) {
      $customerEmail = $_POST["customerEmail"];
      $orderID = $_POST["orderID"];

      $mailTo = $customerEmail;
      if (empty(isset($_POST["orderStatus"]))) {
        echo "<script> Swal.fire({
        icon: 'error',
        title: 'Empty Field',
        text: 'Please select status',
      });
      </script>";
      } else {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "dlcollections@gmail.com";
        $mail->Password = "admin123";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
        $mail->addAddress("sigornylagdameo@gmail.com", "Sigorny");
        $mail->addCC($mailTo, $customerName);
        $mail->isHTML(true);
        $mail->Subject = "DL-Collections - Order";

        if ($_POST["orderStatus"] === "Processing") {
          $mail->Body =
            "<h4> Order# " .
            $orderID .
            " </h4>" .
            "<b>Hi, 
            This is DL-Collections Your order with Order #:" .
            $orderID .
            " is now on processing status.</b>";
        }
        if ($_POST["orderStatus"] === "Shipped") {
          $mail->Body =
            "<h4> Order# " .
            $orderID .
            " </h4>" .
            "Hi, 
            This is DL-Collections Your order with Order #:" .
            $orderID .
            " has been shipped out.";
        }
        if ($_POST["orderStatus"] === "Delivered") {
          $mail->Body =
            "<h4> Order# " .
            $orderID .
            " </h4>" .
            "Hi, 
            This is DL-Collections Your order with Order #:" .
            $orderID .
            " has been delivered. Thank you for supporting  DL-Collections";
        }

        if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
        }
      }
    }
  }

  //contact supplier via email
  public function contact_supplier()
  {
    if (isset($_POST["contactSupplier"])) {
      $supplierEmail = $_POST["supplierEmail"];
      $body = $_POST["message"];
      $pdf = $_FILES["pdf"]["name"];
      $mailTo = $supplierEmail;

      if (empty($supplierEmail) || empty($body) || empty($pdf)) {
        echo "<script> Swal.fire({
          icon: 'error',
          title: 'Empty Field',
          text: 'Please input missing field',
        });
        </script>";
      } else {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "dlcollections@gmail.com";
        $mail->Password = "admin123";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->setFrom("dlcollections@gmail.com", "DL-Collections");
        $mail->addAddress("dlcollections@gmail.com", "Ian");
        $mail->addCC($mailTo, $customerName);
        $mail->isHTML(true);
        $mail->Subject = "DL- Collections - Order";
        $mail->Body = $body;

        if (!$mail->send()) {
          echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
          echo "<script>  
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: 'Message has been sent',
          showConfirmButton: false,
          timer: 1000
        });
        </script>";
        }
      }
    }
  }

  // generate report on supplier
  public function supplier_report($materialID)
  {
    $connection = $this->openConnection();
    $stmt = $connection->prepare(
      "SELECT materials.ID, materialName, supplierName FROM rawmaterials_table materials LEFT JOIN supplier_table supplier ON materials.supplierID = supplier.ID WHERE materials.ID = ?"
    );
    $stmt->execute([$materialID]);
    $report = $stmt->fetch();
    $count = $stmt->rowCount();

    if ($count > 0) {
      return $report;
    } else {
      return $this->show_dashboard404();
    }
  }

  //inserting gmail data
  function insertData($data)
  {
    //check email
    if ($this->checkEmail($data["email"]) == 0) {
      $connection = $this->openConnection();
      $stmt = $connection->prepare(
        "INSERT INTO account_table ( `firstName` , `lastName` , `email` , `access`) VALUES (?,?,?,?)"
      );
      $stmt->execute([
        $data["firstName"],
        $data["lastName"],
        $data["email"],
        $data["access"],
      ]);

      if ($stmt) {
        $selectUser = $connection->prepare(
          "SELECT * FROM account_table WHERE email = ?"
        );
        $selectUser->execute([$data["email"]]);
        $row = $selectUser->fetch();
        $count = $selectUser->rowCount();

        if ($count > 0) {
          $this->set_userdata($row);
          header("Location: index.php");
        }
      }
    } else {
      $connection = $this->openConnection();
      $selectUser = $connection->prepare(
        "SELECT * FROM account_table WHERE email = ?"
      );
      $selectUser->execute([$data["email"]]);
      $row = $selectUser->fetch();
      $count = $selectUser->rowCount();

      if ($count > 0) {
        $this->set_userdata($row);
        header("Location: index.php");
      }
    }
  }
}
$store = new WebStore();
?>
