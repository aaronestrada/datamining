<?php

class Lab6Controller extends Controller {
    //Saves word counter list
    private $tweetWordCounter = array();
    private $tweetWordCounterIndex = array();

    /**
     * Return vowels array
     * @return array
     */
    private function getVowels() {
        return str_split('aeiou');
    }

    /**
     * Return consonants array
     * @return array
     */
    private function getConsonants() {
        return str_split('bcdfghjklmnpqrstvwxyz');
    }

    /**
     * Count bigram sequences [C] (VC)m [V] in word
     * @param $word Word to be counted
     * @return int Number of found sequences
     */
    private function findBigramSequence($word) {
        $vowels = $this->getVowels();
        $consonants = $this->getConsonants();
        $firstLetter = substr($word, 0, 1);

        $initCounter = in_array($firstLetter, $consonants) ? 1 : 0;
        $bigramCounter  = 0;

        //find all the sequences VowelConsonant and count them
        for($i = $initCounter; $i < strlen($word) - 1; $i++) {
            $bigramSeq = str_split(substr($word, $i, 2));
            if(in_array($bigramSeq[0], $vowels) && (in_array($bigramSeq[1], $consonants)))
                $bigramCounter++;
        }
        return $bigramCounter;
    }

    /**
     * Verify if a word finishes with Consonant-Vowel-Consonant sequence
     * @param $word Word to be checked
     * @return bool whether if the word has a final CVC
     */
    private function hasTrigramCVCSequence($word) {
        $vowels = $this->getVowels();
        $consonants = $this->getConsonants();

        if(strlen($word) >= 3) {
            $trigram = str_split(substr($word, -3));

            if ((in_array($trigram[0], $consonants) && in_array($trigram[1], $vowels)
                    && in_array($trigram[2], $consonants)) && !in_array($trigram[2], array('w', 'x', 'y')))
                return true;
        }
        return false;
    }

    /**
     * Find if a word contains at least one vowel
     * @param $word Word to be checked
     * @return bool Whether the word contains a vowel or not
     */
    private function hasVowel($word) {
        $vowels = $this->getVowels();
        foreach($vowels as $vowel)
            if(strpos($word, $vowel) != false)
                return true;
        return false;
    }

    /**
     * Verify if a word finishes in consonant
     * @param $word Word to be checked
     * @return bool Whether the word finishes in a consonant
     */
    private function finishesInConsonant($word, $consonantLetter) {
        $consonants = $this->getConsonants();
        $lastConsonant = substr($word, -1);
        return(in_array($lastConsonant, $consonants) && $lastConsonant == $consonantLetter);
    }

    /**
     * Verify if a word finishes in consonant
     * @param $word Word to be checked
     * @return bool Whether the word finishes in a consonant
     */
    private function finishesInDoubleConsontant($word) {
        $consonants = $this->getConsonants();

        if(strlen($word) >= 2) {
            $lastLetters = str_split(substr($word, -2));
            return (in_array($lastLetters[1], $consonants) && in_array($lastLetters[0], $consonants) && ($lastLetters[0] == $lastLetters[1]));
        }
        return false;
    }

    /**
     * 5-setp algorithm to transform words to base case using suffix stripping stemming
     * @param $word Word to be stripped
     * @return string Stripped word
     */
    private function suffixStrippingStemming($word) {
        //Step 1.1 Delete SSES, IES, SS and S from word
        $ruleList = array(
            'sses' => 'ss',
            'ies' => 'i',
            'ss' => 'ss',
            's' => ''
        );

        foreach($ruleList as $rule => $ruleTransform) {
            if(substr($word, strlen($rule) * -1) == $rule) {
                $word = substr($word, 0, strlen($word) - strlen($rule));
                $word = $word . $ruleTransform;
                break;
            }
        }

        //Step 1.2 Remove EED, ED and ING
        $applyNextStep = false;
        $wordRest = substr($word, 0, strlen($word) - 3);
        if(($this->findBigramSequence($wordRest) > 0) && (substr($word, -3) == 'eed'))
            $word = substr($word, 0, strlen($word) - 1);
        else {
            $wordRest = substr($word, 0, strlen($word) - 2);
            if (($this->hasVowel($wordRest)) && (substr($word, -2) == 'ed')) {
                $word = substr($word, 0, strlen($word) - 2);
                $applyNextStep = true;
            }
            else {
                $wordRest = substr($word, 0, strlen($word) - 3);
                if(($this->hasVowel($wordRest)) && (substr($word, -3) == 'ing')) {
                    $word = substr($word, 0, strlen($word) - 3);
                    $applyNextStep = true;
                }
            }
        }

        /**
         * Step 1.3: if in prior step, ED or ING happens, apply the next step
         * Changes AT, BL, IZ
         */
        if($applyNextStep) {
            $wordEnd = substr($word, -2);
            if(in_array($wordEnd, array('at', 'bl', 'iz')))
                $word = $word . 'e';
            elseif($this->finishesInDoubleConsontant($word) &&
                !($this->finishesInConsonant($word, 's') || $this->finishesInConsonant($word, 'z') || $this->finishesInConsonant($word, 'l'))
            ) {
                $word = substr($word, 0, strlen($word) - 1);
            }
            elseif(($this->findBigramSequence($word) == 1) && ($this->hasTrigramCVCSequence($word)))
                $word = $word . 'e';
        }

        //Step 1.4: change Y for I if word has a vowel
        if($this->hasVowel($word) && $this->finishesInConsonant($word, 'y'))
            $word = substr($word, 0, strlen($word) - 1) . 'i';

        //Step 2: change finishes with the following rules
        $ruleList = array(
            'ational' => 'ate',
            'tional' => 'tion',
            'enci' => 'ence',
            'anci' => 'ance',
            'izer' => 'ize',
            'abli' => 'able',
            'alli' => 'al',
            'entli' => 'ent',
            'eli' => 'e',
            'ousli' => 'ous',
            'ization' => 'ize',
            'ation' => 'ate',
            'ator' => 'ate',
            'alism' => 'al',
            'iveness' => 'ive',
            'fulness' => 'ful',
            'ousness' => 'ous',
            'aliti' => 'al',
            'iviti' => 'ive',
            'biliti' => 'ble'
        );

        foreach($ruleList as $rule => $ruleTransform) {
            $wordPrefix = substr($word, 0, strlen($word) - strlen($rule));
            if(($this->findBigramSequence($wordPrefix) > 0) && (substr($word, strlen($rule) * -1) == $rule)) {
                $word = $wordPrefix . $ruleTransform;
                break;
            }
        }

        //Step 3: change finishes with the following rules
        $ruleList = array(
            'icate' => 'ic',
            'ative' => '',
            'alize' => 'al',
            'iciti' => 'ic',
            'ical' => 'ic',
            'ful' => '',
            'ness' => '',
        );

        foreach($ruleList as $rule => $ruleTransform) {
            $wordPrefix = substr($word, 0, strlen($word) - strlen($rule));
            if(($this->findBigramSequence($wordPrefix) > 0) && (substr($word, strlen($rule) * -1) == $rule)) {
                $word = $wordPrefix . $ruleTransform;
                break;
            }
        }

        //Step 4: change finish of the word with the following rules
        $ruleList = array('al', 'ance', 'ence', 'er', 'ic', 'able', 'ible', 'ant', 'ement', 'ent');
        $foundRule = false;
        foreach($ruleList as $rule) {
            $wordPrefix = substr($word, 0, strlen($word) - strlen($rule));
            if(($this->findBigramSequence($wordPrefix) > 1) && (substr($word, strlen($rule) * -1) == $rule)) {
                $word = $wordPrefix;
                $foundRule = true;
                break;
            }
        }

        if(!$foundRule) {
            //verify ION finishing
            $wordPrefix = substr($word, 0, strlen($word) - 3);
            if(($this->findBigramSequence($wordPrefix) > 1) &&
                (($this->finishesInConsonant($wordPrefix, 's')) || ($this->finishesInConsonant($wordPrefix, 't')))
                && (substr($word, -3) == 'ion')) {
                    $word = $wordPrefix;
            }
            else {
                $ruleList = array('ou', 'ism', 'ate', 'iti', 'ous', 'ive', 'ize');
                foreach($ruleList as $rule) {
                    $wordPrefix = substr($word, 0, strlen($word) - strlen($rule));
                    if(($this->findBigramSequence($wordPrefix) > 1) && (substr($word, strlen($rule) * -1) == $rule)) {
                        $word = $wordPrefix;
                        break;
                    }
                }
            }
        }

        //Step 5.1 Changes the final E for the word
        $wordPrefix = substr($word, 0, strlen($word) - 1);
        $wordFinish = substr($word, -1);
        $findBigramSequenceValue = $this->findBigramSequence($wordPrefix);
        if(($findBigramSequenceValue > 1) && ($wordFinish == 'e'))
            $word = $wordPrefix;
        elseif(($findBigramSequenceValue == 1) && !($this->hasTrigramCVCSequence($wordPrefix)) && ($wordFinish == 'e'))
            $word = $wordPrefix;

        //Step 5.2 verify that word finishes in LL
        if(($this->findBigramSequence($word) > 1) && ($this->finishesInDoubleConsontant($word)) && ($this->finishesInConsonant($word, 'l')))
            $word = substr($word, 0, strlen($word) - 1);

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
    private function cleanTweets($useSuffixStemming = true) {
        //verify that tweet counter is in Memcache and corresponds to the suffix stemming
        $cleanProcess = true;
        $tweetCounterSetType = $this->getKeyFromMemcache('tweetWordCounterSet');
        if(in_array($tweetCounterSetType, array('stemming', 'nostemming'))) {
            if(($useSuffixStemming) && ($tweetCounterSetType = 'stemming'))
                $cleanProcess = false;
            elseif((!$useSuffixStemming) && ($tweetCounterSetType = 'nostemming'))
                $cleanProcess = false;
        }

        if(!$cleanProcess) {
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
                "i\'m", "i've",
                "b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w", "y", "z", "ok",
                "bit", "ly", "com", "www", "rt", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "&", '@');

            //get all tweets
            $objTweet = Tweetlab6::model()->findAll();
            foreach ($objTweet as $tweet) {
                $newTweet = array();
                $arrCleanedTweet = explode(' ', $tweet->tweet);
                foreach($arrCleanedTweet as $cleanedTweetWord) {
                    $cleanedTweetWord = strtolower(trim($cleanedTweetWord));
                    if(substr($cleanedTweetWord, 0, 4) != 'http')
                        array_push($newTweet, $cleanedTweetWord);
                }
                $newTweetNoLink = implode(' ', $newTweet);

                //step 1: delete symbols from tweets
                $cleanedTweet = str_replace(array(',', '...', '.', ':', ')', '(', '?', "'", '!', '-', ";", "#", '"', '=', '[', ']', '*', '+', '/', '\\', "$"), " ", $newTweetNoLink);
                $cleanedTweet = str_replace('&lt', ' ', $cleanedTweet);
                $cleanedTweet = str_replace('&gt', ' ', $cleanedTweet);
                $cleanedTweet = str_replace('&amp', '&', $cleanedTweet);

                //step 2: obtain tweet as array
                $arrNewtweet = array();
                $arrCleanedTweet = explode(" ", $cleanedTweet);
                foreach ($arrCleanedTweet as $cleanedTweetWord) {
                    if($useSuffixStemming)
                        $cleanedTweetWord = $this->suffixStrippingStemming($cleanedTweetWord);

                    if ($cleanedTweetWord !== '') {
                        //step 3: check if the word exists from the forbidden words to include it as the new tweet
                        if (!in_array($cleanedTweetWord, $forbiddenWords)) {
                            if (!isset($arrNewtweet[$cleanedTweetWord]))
                                $arrNewtweet[$cleanedTweetWord] = 1;
                            else
                                $arrNewtweet[$cleanedTweetWord]++;
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
            $this->setKeyToMemcache('tweetWordCounterSet', $useSuffixStemming ? 'stemming' : 'nostemming');
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
        $request = Yii::app()->request;

        //parameter to use stemming process or not
        $stemming = $request->getParam('stemming', null);

        //Step 1: Clean tweets
        $this->cleanTweets($stemming != 'true' ? false : true);

        //Step 2: Set K random centroids
        $clusterNumber = $request->getParam('k', 3); //K = 3 default if not defined
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

            //Step 3.3: calculate new centroid values
            $centroidItems = array();
            foreach ($clusteredIndexValues as $clusteredValues)
                array_push($centroidItems, $this->calculateNewCentroid($clusteredValues));

            //Step 3.4: calculate squared error criterion
            $previousError = $iterationErrorValue; //save error from previous iteration

            //cycle to every cluster to find the error sum(sum(|p-centroid|^2))
            $iterationErrorValue = 0;
            $clusterIndex = 0;
            foreach($centroidItems as $centroidObject) {
                $clusterErrorValue = 0;
                $clusteredValues = $clusteredIndexValues[$clusterIndex];
                foreach($clusteredValues as $clusterTweetIndex) {
                    $tweetToObject = isset($this->tweetWordCounter[$clusterTweetIndex]) ? $this->tweetWordCounter[$clusterTweetIndex] : array();
                    $tweetCentroidDistance = $this->tweetMinkowskiDistance($tweetToObject, $centroidObject, 2);
                    $clusterErrorValue = $clusterErrorValue + pow($tweetCentroidDistance, 2);
                }
                $iterationErrorValue = $iterationErrorValue + $clusterErrorValue;
                $clusterIndex++; //update cluster index
            }

            //save iteration error in list
            $iterationErrorList[$iterationCount] = $iterationErrorValue;

            //verify iteration to execute at least one more time
            if($iterationCount == 1)
                $previousError = $iterationErrorValue + 1;

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

            $tweetCriteria->select = 'DISTINCT hashtag';
            $tweetCriteria->group = 'hashtag';
            $hashtagCount = Tweetlab6::model()->count($tweetCriteria);

            //order centroid values
            arsort($centroidItems[$clusterCount-1]);

            $clusterTweets[$clusterCount] = array(
                'tweetList' => $tweetList,
                'hashtagCount' => $hashtagCount,
                'centroid' => json_encode($centroidItems[$clusterCount-1])
            );
            $clusterCount++;
        }

        $this->render('cluster', array(
            'clusterTweets' => $clusterTweets,
            'iterationErrorList' => $iterationErrorList
        ));
    }

    /**
     * Process suffix stripping stemming to a word
     */
    public function actionSuffix() {
        $request = Yii::app()->getRequest();
        $suffixWord = $request->getParam('word', '');
        echo $this->suffixStrippingStemming($suffixWord);
    }
}