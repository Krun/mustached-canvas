<?php
class CanvasStats {
    private $user;
    private $all_stickers = array();
    private $stickercount = 0;
    private $numposts = 0;
    private $pointscount = 0;
    private $api_calls = 0;
    private $error = FALSE;

    private static $weight = array("fuckyeah" => 150,
                      "number-oneocle" => 100,
                      "glove" => 50,
                      "super-lol" => 30,
                      "tacnayn" => 25,
                      "nyancat" => 25,
                      "hipster" => 20,
                      "kawaii" => 15,
                      "forever-alone" => 10,
                      "banana" => 5,
                      "cool" => 5 );

    const perpage = 100; #number of posts returned for each call to canvas API
    const user_api = 'https://canv.as/public_api/users/';
    const post_api = 'https://canv.as/public_api/posts/';
    const api_limit_message = "Slow down there, cowboy!";

    const error_undefined = "There was an unexpected failure";
    const error_api_limit = "We have reached API request limit :(";

    /**
     * Class constructor
     *
     * This will construct an object of class CanvasStats and load user stats
     */

    public function __construct($name) {
        $this->user = $name;
        $skip = 0;
        while(true){
            $data_string = '{"ids": [{"skip": '.$skip.', "user": "'.$this->user.'"}]}';
            $ch = curl_init(self::user_api);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );                                                                                                                   
             
            $result = curl_exec($ch);
            $this->api_calls++;
            curl_close($ch);
            $res = json_decode($result,true);
            if ($res["success"] == false){
              $this->error = self::error_undefined;
              if ($res["reason"] == self::api_limit_message) {
                $this->error = self::error_api_limit;
              }
            }

            $posts = $res["users"][0]["posts"];
            $posts_page = array();
            if (count($posts) > 0) {
                $skip += self::perpage;
                $this->numposts += count($posts);
            } else {
                break;
            }
            foreach ($posts as $post) {
                $posts_page[] = $post["id"];
            }
            
            $postids = json_encode($posts_page);

            $data_string = '{"ids": '.$postids.'}';
            $ch = curl_init(self::post_api);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );                                                                                                                   
             
            $result = curl_exec($ch);
            $this->api_calls++;
            curl_close($ch);
            $res = json_decode($result,true);
            foreach($res["posts"] as $post){
                $stickers = $post["stickers"];
                foreach ($stickers as $sticker){
                    $this->all_stickers[$sticker["name"]] += $sticker["count"];
                }
            }

        }
        foreach($this->all_stickers as $sticker) {
          $this->stickercount += $sticker;
        }

        foreach($this->all_stickers as $name => $value) {
            $this->pointscount += $value*self::weight($name);
        }
    }
    /**
     * getAllStickers()
     *
     * This method returns a list with detailed sticker count.
     *
     * Return value is an array of the form [(str)sticker_type] => (int)amount
     */
    public function getAllStickers() {
        return $this->all_stickers;    
    }
    
    /**
     * getNumStickers()
     *
     * Returns total number of stickers for the user
     */
    public function getNumStickers() {
        return $this->stickercount;   
    }

    /**
     * getNumPosts()
     *
     * Returns total number of posts for the user
     */    
    public function getNumPosts() {
        return $this->numposts;
    }

    /**
     * getPoints()
     *
     * Returns total number of points
     */    
    public function getPoints() {
        return $this->pointscount;
    }

    /**
     * getAvgPoints()
     *
     * Returns average points per post
     */    
    public function getAvgPoints() {
        return round($this->getPoints()/$this->getNumPosts(),2);
    }

    /**
     * getApiCalls()
     *
     * Returns number of calls made to Canv.as API
     */
    public function getApiCalls() {
        return $this->api_calls;
    }

    /**
     * error()
     *
     * FALSE if user stats were loaded correctly
     * Error message otherwise
     */
    public function error() {
        return $this->error;
    }

    /**
     * weight($type)
     *
     * Returns number of points for the given sticker type.
     *
     * Example: weight("fuck-yeah") = 150.
     */
    public static function weight($type) {
        if (isset(self::$weight[$type])){
            return self::$weight[$type];
        } else {
            return 1;
        }
    }
}

?>
