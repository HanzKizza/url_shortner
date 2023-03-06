<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$servername = "localhost";
$user = "root";
$db = "url_shortener";
$pwd = "";
$domain = "http://localhost/s";

$con = new mysqli($servername, $user, $pwd, $db);
try{
    if($con->connect_error){
        die("");
    }
}
catch(Exception $e){
    exit;
}

// retrieve
if(isset($_GET['s'])){
    $url = $_GET['s'];
    $original_url = dencryptlink2($url);
    echo $original_url;
}

//enc
else if(isset($_GET['m'])){
    $url = $_GET['m'];
    $shortenedUrl = encryptlink2($url);
    echo $shortenedUrl;
}

else{
   echo "Url not found";
}



function dencryptlink2($link){
    global $con;
    $sql = $con->prepare("SELECT original_url FROM short_urls WHERE url_id = ?");
    $sql->bind_param("s", $link);
    if($sql->execute()){
        $result = $sql->get_result();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $original_url = $row["original_url"];
                header("Location: ".$original_url);
            }
        }
    }
}



function encryptlink2($link){
    global $con;
    global $domain;
    $sql = $con->prepare("INSERT into short_urls values (DEFAULT, ?)");
    $sql->bind_param("s", $link);
    if($sql->execute()){
        $link_id = $con->insert_id;
        $link = $link.$link_id;
        $encryptedlink = $domain."/s.php?s=".$link_id;
        return $encryptedlink;
    }
    else{
        return "";
    }
}
?>