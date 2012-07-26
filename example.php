<html>
<head>
<title>Canv.as stats example</title>
</head>
<body>
<?php
if (!isset($_GET["user"])) { ?>
    <form method="GET">
    <input type="text" name="user" placeholder="Canvas username">
    <input type="submit">
    </form>
<?php
} else {
    $user = htmlspecialchars($_GET["user"]);
    include("CanvasStats.php");
    $canvas_stats = new CanvasStats($user);
    if ($canvas_stats->error()){
        die($canvas_stats->error());
    }
    echo "<h1>Sticker list:</h1>\r\n";
    echo "<ul>";
    foreach($canvas_stats->getAllStickers() as $type => $amount) {
        echo "<li><b>$type:</b> $amount</li>\r\n";
    }
    echo "</ul>\r\n\r\n";
    echo "<h1>Additional stats:</h1>\r\n";
    echo "<p><b>Number of stickers:</b> ".$canvas_stats->getNumStickers()."</p>\r\n";
    echo "<p><b>Number of posts:</b> ".$canvas_stats->getNumPosts()."</p>\r\n";
    echo "<p><b>Total points:</b> ".$canvas_stats->getPoints()."</p>\r\n";
    echo "<p><b>Average points per post:</b> ".$canvas_stats->getAvgPoints()."</p>\r\n";
    echo "<p><i>A total of ".$canvas_stats->getApiCalls()." calls to Canv.as api were needed to get these stats.</i></p>\r\n";

}?>

</body>
</html>
