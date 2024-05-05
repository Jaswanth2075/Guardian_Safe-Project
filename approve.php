<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if this page is accessed by a parent
$type = $_SESSION['account_type'];
if ($type === "parent") {
    header("Location: transaction.php");
    exit();
}

// Get the requested child's information (e.g., from a database)
$childName = "Child's Name"; // Replace with actual child's name
$amount = 100; // Replace with the requested amount

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept'])) {
        // Parent accepts the request; proceed to validate UPI PIN
        $enteredUpiPin = $_POST['upi_pin'];

        // Establish a database connection
        $mysqli = new mysqli("localhost", "root", "", "test");
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        // Fetch the child's parent_token_id from the children table based on username
        $childUsername = $_SESSION['username'];
        $query = "SELECT parent_token_id FROM children WHERE username = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $childUsername);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($dbParentTokenId);

        if ($stmt->fetch() && $dbParentTokenId !== null) {
            // Parent token exists; now fetch the parent's UPI PIN based on parent_token_id
            $parentTokenId = $dbParentTokenId;
            $query = "SELECT upi_pin FROM parents WHERE token_id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $parentTokenId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($parentUpiPin);

            if ($stmt->fetch()) {
                // UPI PIN fetched; now validate it
                if ($enteredUpiPin === $parentUpiPin) {
                    // UPI PIN is correct; proceed to transaction.php
                    header("Location: transaction.php");
                    exit();
                } else {
                    // UPI PIN is incorrect; show an error message
                    $error = "Invalid UPI PIN. Please try again.";
                }
            } else {
                // UPI PIN not found; show an error message
                $error = "UPI PIN not found. Please try again.";
            }
        } else {
            // Parent token not found; show an error message
            $error = "Parent token not found. Please try again.";
        }

        // Close the database connection
        $stmt->close();
        $mysqli->close();
    } elseif (isset($_POST['reject'])) {
        // Parent rejects the request; redirect to dashboard.php
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Request Approval</title>
    <link rel="icon" type="image" href="Yellow Minimalist Round Shaped Cafe Logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:ital,wght@0,500;0,700;1,400&family=Roboto+Slab:wght@300;400&family=Roboto:wght@500&display=swap" rel="stylesheet">
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        .container{
  position: relative;
  max-width: 1250px;
  width: 100%;
  max-height: 600px;
  height: 100%;
  background: #fff;
  padding: 40px 30px;
  box-shadow: 0 5px 10px rgba(0,0,0,0.2);
  perspective: 2700px;
}
        body{
            font-family: 'Poppins',sans-serif;
            background: #9400ff;
        }
        nav{
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 70px;
        }
        nav ul{
            display: flex;
            justify-content: center;
        }
        nav ul li;{
            font-size: 1.2rem;
            list-style: none;
            margin: 0 25px;
        }
        .left{
            font-size: 2rem;
        }
        .name1{
            color: white;
        }
        .name2{
            color: #27005D   }
        .one{
            color: black;
        }
        .two{
            width: 220px;
            height: 70px;
            cursor: pointer;
            border-radius: 7px;
            border-style: solid white;
            border-color: white;
            border-width: 3.5px;
            font-size: 1.3rem;
            font-family: 'Poppins',sans-serif;
            font-weight: bold;
            color: white;
            background-color: #9400FF;
            position:absolute;
            /* text-align:right; */
            margin: left 65px;
            

            
        }
        .one:hover{
            color: #27005D;
        }
        .two:hover{
            color: #27005D ;
            background-color: #EEEEEE;
        }
        a{
            text-decoration: none;
        }
        header{
            position: sticky;
            background-color: #9400ff;
            height: 150px;
        }
        .main{
            background-color: white;
            height: 500px;
            padding-top: 30px;
            font-size: 25px;
            text-align:left;
            
        }
        .name{
            color: #27005D;
            text-align: left;
            
        }
        .but1{
            margin-top:60px ;
            width: 220px;
            height: 70px;
            cursor: pointer;
            border-radius: 7px;
            border-style: solid;
            border-width: 3.5px;
            color: black;
            font-size: 1.3rem;
            font-family: 'Poppins',sans-serif;
            font-weight: bold;
            color:#9400FF;
            background-color:#EEEEEE;
            /* text-align: center; */
            
        }
        .but2{
            margin-top:60px ;
            width: 220px;
            height: 70px;
            cursor: pointer;
            border-radius: 7px;
            border-style: solid;
            border-width: 3.5px;
            color: black;
            font-size: 1.3rem;
            font-family: 'Poppins',sans-serif;
            font-weight: bold;
            color:#9400FF;
            background-color:#EEEEEE;
            /* text-align: center; */
            
        }
        .but1:hover{
            color:#EEEEEE;
            background-color:#5D9C59 ;
        }
        .but2:hover{
            color:#EEEEEE;
            background-color:red;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="name" > 
                <p style="color:white; font-size:60px; margin-top: 60px; margin-right: 0px;">Guardian Save</p>
                </div>

             <ul>
                    <!-- <a href="index.html"><li class="one">Home</li></a>
                    <a href="abouthtml"><li class="two">About</li></a>
                    <a href="#"><li class="one">Projects</li></a> -->
                    <a href="#"><button class="two">Logout</button></a>
                </ul>
            </div>
        </nav>
    </header>
    <center><div class="container">
        <div class="main" "> 
            <h2>Money Request Approval</h2><br>
            <p style="font-size :2rem;">You got a money request from <?php echo $childName; ?>:</p>
            <p style="font-size:2rem;" >Do you want to accept or reject this request?</p>
            
            <form action="approve.php" method="post">
                <?php if (isset($error)) { ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php } ?>
                <br><br><label for="upi_pin" style="font: size 1.8rem">Enter Your UPI PIN:</label>
                <input style="height:1.8rem;" type="password" name="upi_pin" required><br><br>
                <input class="but1" style="text-align: center; align-content: left;" type="submit" name="accept" value="Accept">
                <input class="but2" style="text-align: center; align-content: left;" type="submit" name="reject" value="Reject">
            </form>
            
            
            
            
        </div>
    </div>
    </center>
    
</body>
</html>