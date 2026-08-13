<?php
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$request_id = isset($_GET["request_id"]) ? intval($_GET["request_id"]) : 0;
$action = isset($_GET["action"]) ? $_GET["action"] : "";
$worker_email = isset($_GET["worker_email"]) ? $_GET["worker_email"] : "";

// Validate inputs
if ($action == "" || $request_id == 0 || $worker_email == "") {
    header("Location: my_requestsS.php");
    exit();
}

// Get all requests and applications
$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
$applications = isset($_SESSION["applications"]) ? $_SESSION["applications"] : [];

// Find the request
$request = null;
foreach ($requests as $req) {
    if ($req["id"] == $request_id) {
        $request = $req;
        break;
    }
}

// If request not found
if (!$request) {
    header("Location: my_requestsS.php");
    exit();
}

// Check if this buyer owns this request
if ($request["buyer_email"] != $user_email) {
    header("Location: my_requestsS.php");
    exit();
}

// Process the action
if ($action == "accept") {
    // Accept the worker
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] == $worker_email) {
            $app["status"] = "accepted";
        }
    }
    
    // Mark all other applications for this request as rejected
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] != $worker_email && $app["status"] == "pending") {
            $app["status"] = "rejected";
        }
    }
    
    // Mark request as completed
    foreach ($_SESSION["requests"] as &$req) {
        if ($req["id"] == $request_id) {
            $req["status"] = "completed";
            break;
        }
    }
    
    // Redirect to deal confirmation
    header("Location: deal_confirmedS.php?request_id=" . $request_id . "&worker_email=" . urlencode($worker_email));
    exit();
    
} elseif ($action == "reject") {
    // Reject the worker
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] == $worker_email) {
            $app["status"] = "rejected";
            break;
        }
    }
    
    // Check if there are any other pending applications
    $has_pending = false;
    foreach ($_SESSION["applications"] as $app) {
        if ($app["request_id"] == $request_id && $app["status"] == "pending") {
            $has_pending = true;
            break;
        }
    }
    
    // If no pending applications, mark request as pending again
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