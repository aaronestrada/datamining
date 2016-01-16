<?php

class Lab6Controller extends Controller {
    //Saves word counter list
    private $tweetWordCounter = array();
    private $tweetWordCounterIndex = array();

    /**
     * Suffix stripping method for stemming
     * @param $word Word to be stemmed
     * @return string Stemmed word
     */
    private function suffixStripping($word) {
        if(strlen($word) > 4) {
            $fword = substr($word, -2);

            if(($fword == 'ed') || ($fword == 'ly'))
                return substr($word, 0, strlen($word) - 2);
            if(substr($word, -3) == 'ing')
                return substr($word, 0, strlen($word) - 3);
        }
        return $word;
    }

    /**
     * Deletes clean tweets from Memcache
     */
    private function clearCleanTweets() {
        $memcache = Yii::app()->cache;
        $memcache->delete('tweetWordCounter');
        $memcache->delete('tweetWordCounterIndex');
        $memcache->delete('tweetWordCounterSet');
    }

    /**
     * Set key to Memcache
     * @param $key Key to store
     * @param $value Value to store
     */
    private function setKeyToMemcache($key, $value) {
        $memcache = Yii::app()->cache;
        $memcache->set($key, $value);
    }

    /**
     * Obtain a key from Memcache
     * @param $key
     * @return mixed
     */
    private function getKeyFromMemcache($key) {
        $memcache = Yii::app()->cache;
        return $memcache->get($key);
    }

    /**
     * Calculate tweet distance with Minkowski algorithm
     * @param $tweetFrom From Tweet ID
     * @param $tweetTo To Tweet ID
     * @param int $powerValue
     * @return float Distance value
     */
    private function tweetMinkowskiDistance($tweetFromObject, $tweetToObject, $powerValue = 2) {
        //get union list of words
        $tweetFromObjectKeys = array_keys($tweetFromObject);
        $tweetToObjectKeys = array_keys($tweetToObject);
        $wordList = array_unique(array_merge($tweetFromObjectKeys,$tweetToObjectKeys));

        $objectDistance = 0;
        foreach($wordList as $wordItem) {
            $fromAttributeValue = isset($tweetFromObject[$wordItem]) ? $tweetFromObject[$wordItem] : 0;
            $toAttributeValue = isset($tweetToObject[$wordItem]) ? $tweetToObject[$wordItem] : 0;
            $objectDistance = $objectDistance + pow(abs($fromAttributeValue - $toAttributeValue), $powerValue);
        }

        //validate that power is bigger than zero
        if($powerValue > 0) {
            if ($powerValue == 1)
                return $objectDistance;

            return pow($objectDistance, 1 / $powerValue);
        }
        return 1;
    }

    /**
     * Based on a list of tweets, calculate the centroid (AVG value)
     * @param $tweetIndexList Tweet list (index of tweets)
     * @return string JSON encode centroid
     */
    private function calculateNewCentroid($tweetIndexList) {
        //obtain unique list of words for list of tweets
        $wordList = array();
        foreach($tweetIndexList as $tweetIndex) {
            $tweetObject = isset($this->tweetWordCounterIndex[$tweetIndex]) ? $this->tweetWordCounterIndex[$tweetIndex] : array();
            $wordList = array_merge($wordList, $tweetObject);
        }
        $wordList = array_unique($wordList);

        //calculate new centroid value
        $centroidValues = array();
        foreach($wordList as $wordItem) {
            $wordItemCounter = 0;
            foreach($tweetIndexList as $tweetIndex) {
                $tweetObject = isset($this->tweetWordCounter[$tweetIndex]) ? $this->tweetWordCounter[$tweetIndex] : array();
                if(isset($tweetObject[$wordItem]))
                    $wordItemCounter = $wordItemCounter + $tweetObject[$wordItem];
            }
            $centroidValues[$wordItem] = $wordItemCounter / count($tweetIndexList);
        }
        return $centroidValues;
    }


    /**
     * Extract from original tweet and save in new field
     */
    private function cleanTweets() {
        //verify that tweet counter is in Memcache
        if($this->getKeyFromMemcache('tweetWordCounterSet') == true) {
            $this->tweetWordCounter = $this->getKeyFromMemcache('tweetWordCounter');
            $this->tweetWordCounterIndex = $this->getKeyFromMemcache('tweetWordCounterIndex');
        }
        else {
            //Saves word counter list
            $forbiddenWords = array(
                "a", "able", "about", "across", "after", "all", "almost", "also", "am",
                "among", "an", "and", "any", "are", "as", "at", "be", "because", "been",
                "but", "by", "can", "cannot", "could", "dear", "did", "do", "does", "either",
                "else", "ever", "every", "for", "from", "get", "got", "had", "has", "have",
                "he", "her", "hers", "him", "his", "how", "however",
                "i", "if", "in", "into", "is", "it", "its",
                "just", "least", "let", "like", "likely", "may", "me", "might", "most",
                "must", "my", "neither", "no", "nor", "not", "of", "off", "often", "on",
                "only", "or", "other", "our", "own", "rather", "said", "say", "says",
                "she", "should", "since", "so", "some", "than", "that", "the", "their", "them",
                "then", "there", "these", "they", "this", "tis", "to", "too", "twas", "us", "wants",
                "was", "we", "were", "what", "when", "where", "which", "while", "who", "whom", "why",
                "will", "with", "would", "yet", "you", "your", "im", "e", "i", "o", "u", "oh", "lol",
                "b", "c", "d", "f", "g", "h", "i", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "y", "z", "ok",
                "bit", "ly", "com", "www", "rt", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "&", '@');

            //get all tweets
            $objTweet = Tweetlab6::model()->findAll();
            foreach ($objTweet as $tweet) {
                //step 1: delete symbols from tweets
                $cleanedTweet = str_replace(array(',', '...', '.', ':', ')', '(', '?', "'", '!', '-', ";", "#", '"', '=', '[', ']', '*', '+', '/', '\\', "$"), " ", $tweet->tweet);
                $cleanedTweet = str_replace('&lt', ' ', $cleanedTweet);
                $cleanedTweet = str_replace('&gt', ' ', $cleanedTweet);
                $cleanedTweet = str_replace('&amp', '&', $cleanedTweet);

                //step 2: obtain tweet as array
                $arrNewtweet = array();
                $arrCleanedTweet = explode(" ", $cleanedTweet);
                foreach ($arrCleanedTweet as $cleanedTweetWord) {
                    $cleanedTweetWord = strtolower(trim($cleanedTweetWord));

                    if ($cleanedTweetWord !== '') {
                        //step 3: check if the word exists from the forbidden words to include it as the new tweet
                        if (!in_array($cleanedTweetWord, $forbiddenWords)) {
                            if(substr($cleanedTweetWord, 0, 4) != 'http') {
                                if (!isset($arrNewtweet[$cleanedTweetWord]))
                                    $arrNewtweet[$cleanedTweetWord] = 1;
                                else
                                    $arrNewtweet[$cleanedTweetWord]++;
                            }
                        }
                    }
                }
                //step 4: save the new tweet
                $this->tweetWordCounterIndex[$tweet->id] = array_keys($arrNewtweet);
                $this->tweetWordCounter[$tweet->id] = $arrNewtweet;
            }
            //save calculated values into Memcache
            $this->setKeyToMemcache('tweetWordCounter', $this->tweetWordCounter);
            $this->setKeyToMemcache('tweetWordCounterIndex', $this->tweetWordCounterIndex);
            $this->setKeyToMemcache('tweetWordCounterSet', true);
        }
    }

    /**
     * Clean values from Memcache
     */
    public function actionClean() {
        $this->clearCleanTweets(); //removes from Memcache
    }

    /**
     * Execute clustering based on K-means
     */
    public function actionCluster() {
        //no limit in execution time
        set_time_limit(0);

        //Step 1: Clean tweets
        $this->cleanTweets();

        //Step 2: Set K random centroids
        $request = Yii::app()->request;
        $clusterNumber = $request->getParam('k', 3); //K=3 default if not defined
        $tweetWordCounterIndex = array_keys($this->tweetWordCounter);

        $centroidItems = array();
        $centroidItemKeys = array();
        for($i = 0; $i < $clusterNumber; $i++) {
            $foundCentroid = false;
            do {
                $newCentroid = rand(0, count($tweetWordCounterIndex) - 1);
                $newCentroidID = $tweetWordCounterIndex[$newCentroid];

                if (!in_array($newCentroidID, array_keys($centroidItemKeys))) {
                    //save centroid key to omit in next random value
                    array_push($centroidItemKeys, $newCentroidID);

                    //save centroid tweet in list
                    array_push($centroidItems, isset($this->tweetWordCounter[$newCentroidID]) ? $this->tweetWordCounter[$newCentroidID] : array());
                    //found centroid to escape cycle
                    $foundCentroid = true;
                }
            } while(!$foundCentroid);
        }

        //erase centroid item keys, not useful later
        unset($centroidItemKeys);

        //Step 3: start clasification for K-means
        $iterationCount = 1;
        $iterationErrorValue = 0;
        $iterationErrorList = array();

        do {
            //Step 3.1: Calculate distances from centroids
            $tweetDistanceMatrix = array();
            foreach ($tweetWordCounterIndex as $tweetTo) {
                $tweetDistanceMatrix[$tweetTo] = array();

                //calculate Minkowski Distance (Eucledian) from centroid to each point
                $clusterIndex = 0;
                foreach ($centroidItems as $centroidObject) {
                    $tweetToObject = isset($this->tweetWordCounter[$tweetTo]) ? $this->tweetWordCounter[$tweetTo] : array();
                    $tweetDistanceMatrix[$tweetTo][$clusterIndex] = $this->tweetMinkowskiDistance($tweetToObject, $centroidObject, 2);
                    $clusterIndex++;
                }
            }

            //Step 3.2: set cluster values based on the closest centroid
            $clusteredIndexValues = array();
            $clusteredDistanceValues = array();
            for ($i = 0; $i < count($centroidItems); $i++) {
                $clusteredIndexValues[$i] = array(); //init cluster for centroid
                $clusteredDistanceValues[$i] = array();
            }

            foreach ($tweetWordCounterIndex as $tweetIndex) {
                $centroidDistances = $tweetDistanceMatrix[$tweetIndex];
                arsort($centroidDistances);

                $closestClusterIndex = array_pop(array_keys($centroidDistances));
                $closestClusterDistance = array_pop($centroidDistances);
                array_push($clusteredIndexValues[$closestClusterIndex], $tweetIndex);
                array_push($clusteredDistanceValues[$closestClusterIndex], $closestClusterDistance);
            }

            //free tweet distance matrix
            unset($tweetDistanceMatrix);

            //Step 3.3: calculate squared error criterion
            $previousError = $iterationErrorValue; //save error from previous iteration

            $iterationErrorValue = 0;
            foreach($clusteredDistanceValues as $clusterDistance) {
                $clusterErrorValue = 0;
                foreach($clusterDistance as $distanceValue)
                    $clusterErrorValue = $clusterErrorValue + pow($distanceValue, 2);
                $iterationErrorValue = $iterationErrorValue + $clusterErrorValue;
            }

            //save iteration error in list
            $iterationErrorList[$iterationCount] = $iterationErrorValue;

            //verify iteration to execute at least one more time
            if($iterationCount == 1)
                $previousError = $iterationErrorValue + 1;

            //Step 3.4: calculate new centroid values
            $centroidItems = array();

            //verify if it is necessary to obtain new centroids based on error convergence
            if(($previousError - $iterationErrorValue) > 0.05)
                foreach ($clusteredIndexValues as $clusteredValues)
                    array_push($centroidItems, $this->calculateNewCentroid($clusteredValues));

            $iterationCount++;
        } while(($previousError - $iterationErrorValue) > 0.05);

        //map tweets to view
        $clusterCount = 1;
        $clusterTweets = array();
        foreach($clusteredIndexValues as $cluster) {
            $tweetCriteria = new CDbCriteria();
            $tweetCriteria->addInCondition('id', array_values($cluster));
            $tweetCriteria->order = 'hashtag ASC';
            $tweetList = Tweetlab6::model()->findAll($tweetCriteria);
            $clusterTweets[$clusterCount] = $tweetList;
            $clusterCount++;
        }

        $this->render('cluster', array(
            'clusterTweets' => $clusterTweets,
            'iterationErrorList' => $iterationErrorList
        ));
    }
}