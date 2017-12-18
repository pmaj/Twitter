<?php
    class Tweet
    {
        private $id;
        private $content;
        private $user_id;
        private $userName = ''; //to get owner of tweet
        private $creationDate;
        public static function loadTweetById(PDO $conn, $id) {
            $query = "SELECT * FROM Tweets WHERE id = :id";
            $stmt = $conn->prepare($query);
            $result = $stmt->execute([':id' => $id]);
            if ($result && $stmt->rowCount() == 1) {
                $currentTweet = $stmt->fetch(PDO::FETCH_ASSOC);
                $tweet = new Tweet();
                $tweet->id = $currentTweet['id'];
                $tweet->content = $currentTweet['content'];
                $tweet->user_id = $currentTweet['user_id'];
                $tweet->creationDate = $currentTweet['posted_time'];
                return $currentTweet;
            } else {
                return NULL;
            }
        }
        public static function loadAllTweetsByUserId(PDO $conn, $user_id) {
            $query = "SELECT * FROM Users  JOIN Tweets ON Users.id=Tweets.user_id WHERE user_id = :user_id";
            $stmt = $conn->prepare($query);
            $result = $stmt->execute([':user_id' => $user_id]);
            $tweets = [];
            if ($result) {
                if($stmt->rowCount() > 0)  {
                    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $currentTweet) {
                        $tweet = new Tweet();
                        $tweet->id = $currentTweet['id'];
                        $tweet->content = $currentTweet['content'];
                        $tweet->user_id = $currentTweet['user_id'];
                        $tweet->creationDate = $currentTweet['posted_time'];
                        $tweets [] = $tweet;
                    }
                } else {
                    $tweets [] = "Nie masz jeszcze żadnego tweeta..";
                }
                return $tweets;
            }
        }
        public static function loadAllTweets(PDO $conn) {
            $query = "SELECT Tweets.id, user_id, username, content, posted_time FROM Users JOIN Tweets WHERE Users.id=Tweets.user_id ORDER BY posted_time DESC";
            $result = $conn->query($query);;
            $tweets = [];
            if ($result && $result->rowCount() > 0) {
                foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $item) {
                    $tweet = new Tweet();
                    $tweet->id = $item['id'];
                    $tweet->content = $item['content'];
                    $tweet->user_id = $item['user_id'];
                    $tweet->creationDate = $item['posted_time'];
                    $tweet->userName = $item['username'];
                    $tweets [] = $tweet;
                }
            }
            return $tweets;
        }
        /**
         * Tweet constructor.
         */
        public function __construct()
        {
            $this->id = -1;
            $this->content = '';
            $this->user_id = '';
            $this->creationDate = '';
            $this->userName = '';
        }
        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->id;
        }
        /**
         * @return mixed
         */
        public function getContent()
        {
            return $this->content;
        }
        /**
         * @return mixed
         */
        public function getUserName()
        {
            return $this->userName;
        }
        /**
         * @return mixed
         */
        public function getCreationDate()
        {
            return $this->creationDate;
        }
        /**
         * @param mixed $tweet
         */
        public function setContent($tweet)
        {
            $this->content = $tweet;
        }
        /**
         * @param mixed $user_id
         */
        public function setUserId($user_id)
        {
            $this->user_id = $user_id;
        }
        public function saveToDB(PDO $conn)
        {
            $query = "INSERT INTO Tweets(content, user_id, posted_time)
                      VALUES (:content, :user_id, NOW())";
            $stmt = $conn->prepare($query);
            $result = $stmt->execute([
               ':content' => $this->content,
               ':user_id' => $this->user_id,
            ]);
            if ($result) {
                return true;
           }
        }
    }
