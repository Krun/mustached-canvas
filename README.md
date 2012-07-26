Canv.as stats script
===

Usage
---
CanvasStats is a PHP class which provides methods to get stats about canv.as users.

Objects of class CanvasStats contain stats for a single user, passed as a parameter to the class constructor.

Functions
===
new CanvasStats($user)
---
Creates a new CanvasStats object and loads stats for username $user.

CanvasStats objects provide the following functions:

$obj->getAllStickers()
---
This method returns a list with detailed sticker count.

Return value is an array of the form [(str)sticker_type] => (int)amount

$obj->getNumStickers()
---
Returns total number of stickers for the user

$obj->getNumPosts()
---
Returns total number of posts for the user

$obj->getPoints()
---
Returns total number of points

$obj->getAvgPoints()
---
Returns average points per post

$obj->getApiCalls()
---
Returns number of calls made to Canv.as API

$obj->error()
---
FALSE if user stats were loaded correctly
Error message otherwise

CanvasStats::weight()
---
Static function which returns number of point for the given sticker type.

Example: weight("fuck-yeah") = 150

Example
===
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
