<?php
session_start();


if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$request_id = isset($_GET["request_id"]) ? intval($_GET["request_id"]) : 0;
$action = isset($_GET["action"]) ? $_GET["action"] : "";
$worker_email = isset($_GET["worker_email"]) ? $_GET["worker_email"] : "";


if ($action == "" || $request_id == 0 || $worker_email == "") {
    header("Location: my_requestsS.php");
    exit();
}


$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
$applications = isset($_SESSION["applications"]) ? $_SESSION["applications"] : [];


$request = null;
foreach ($requests as $req) {
    if ($req["id"] == $request_id) {
        $request = $req;
        break;
    }
}


if (!$request) {
    header("Location: my_requestsS.php");
    exit();
}


if ($request["buyer_email"] != $user_email) {
    header("Location: my_requestsS.php");
    exit();
}


if ($action == "accept") {
    
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] == $worker_email) {
            $app["status"] = "accepted";
        }
    }
    
   
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] != $worker_email && $app["status"] == "pending") {
            $app["status"] = "rejected";
        }
    }
    
    
    foreach ($_SESSION["requests"] as &$req) {
        if ($req["id"] == $request_id) {
            $req["status"] = "completed";
            break;
        }
    }
    
   
    header("Location: deal_confirmedS.php?request_id=" . $request_id . "&worker_email=" . urlencode($worker_email));
    exit();
    
} elseif ($action == "reject") {
   
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] == $worker_email) {
            $app["status"] = "rejected";
            break;
        }
    }
    
    
    $has_pending = false;
    foreach ($_SESSION["applications"] as $app) {
        if ($app["request_id"] == $request_id && $app["status"] == "pending") {
            $has_pending = true;
            break;
        }
    }
    
    
    if (!$has_pending) {
        foreach ($_SESSION["requests"] as &$req) {
            if ($req["id"] == $request_id) {
                $req["status"] = "pending";
                break;
            }
        }
    }
    
    header("Location: my_requestsS.php?rejected=success");
    exit();
    
} else {
    header("Location: my_requestsS.php");
    exit();
}
?>