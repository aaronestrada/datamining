<?php

class Lab4_1Controller extends Controller {
    private $arrSentiment = array(
        0 => 'Negative',
        2 => 'Neutral',
        4 => 'Positive'
    );

    /**
     * Extract from original tweet and save in new field
     */
    private function extractfromtweet() {
        set_time_limit(0);
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
            "http", "http:", "bit", "ly", "com", "www", "rt", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "&");

        //get all tweets
        $maxLimit = 10000;
        $maxOffset = 1600000 / $maxLimit;

        $maxOffset = 1;
        for($i = 0; $i < $maxOffset; $i++) {
            //$objTweet = Tweetlist::model()->findAll(array('limit' => $maxLimit, 'offset' => ($i * $maxLimit)));
            $objTweet = Tweetlist::model()->findAll("tweet2 = ''");

            foreach ($objTweet as $tweet) {
                //step 1: delete symbols from tweets
                $cleanedTweet = $tweet->tweet;
                $cleanedTweet = str_replace(array(',', '...', '.', '?', "'", '!', '-', ";", "@", "#", '"', '=', '[', ']', '*', '+', '/', '\\', "$"), " ", $tweet->tweet);
                $cleanedTweet = str_replace('&lt', ' ', $cleanedTweet);
                $cleanedTweet = str_replace('&gt', ' ', $cleanedTweet);
                $cleanedTweet = str_replace('&amp', '&', $cleanedTweet);

                //step 2: obtain tweet as array
                $arrNewtweet = array();
                $arrCleanedTweet = explode(" ", $cleanedTweet);
                foreach ($arrCleanedTweet as $cleanedTweetWord) {
                    //step 3: check if the word exists from the forbidden words to include it as the new tweet
                    if (($cleanedTweetWord !== ' ') && (!in_array(strtolower($cleanedTweetWord), $forbiddenWords)))
                        array_push($arrNewtweet, $cleanedTweetWord);
                }

                //step 4: save the new tweet
                $newtweet = implode(" ", $arrNewtweet);
                $tweet->tweet2 = $newtweet;

                $tweet->save();
            }
        }
    }

    private function findAttributes($limitWords, $offset) {
        set_time_limit(0);
        //Step 1: clean tweet and save in field tweet2
        $tweetList = array();
        $attributesList = array();

        $objTweet = Tweetlist::model()->findAll(array('limit' => $limitWords, 'offset' => $offset));

        foreach ($objTweet as $tweet) {
            $tweetList[$tweet->id] = array();
            $tweetAttributes = explode(" ", $tweet->tweet2);

            foreach ($tweetAttributes as $tweetAttr) {
                $tweetWord = strtolower(trim($tweetAttr));
                if ($tweetWord != '') {
                    if (isset($attributesList[$tweetWord]))
                        $attributesList[$tweetWord]++;
                    else
                        $attributesList[$tweetWord] = 1;
                    $tweetList[$tweet->id][] = $tweetWord;
                }
            }
        }
        //Step 3: sort array from high to low
        arsort($attributesList);
        unset($objTweet);

        return array(
            $attributesList,
            $tweetList
        );
    }

    public function actionExtract() {
        //Step 1: clean tweet and save in field tweet2
        //$this->extractfromtweet();
    }

    /**
     * Get all attributes from training set
     */
    public function actionFindAttributes() {
        //Step 2: obtain attributes from tweet2
        $request = Yii::app()->getRequest();
        $limitWords = $request->getParam('limit', 10);
        $maxTweets = 1600000;

        for($i = 0; $i < $maxTweets / $limitWords; $i++) {
            list($attributesList, $tweetList) = $this->findAttributes($limitWords, $limitWords * $i);
            $objCleanTweet = new Cleantweetlist();
            $objCleanTweet->attributelist = json_encode($attributesList);
            $objCleanTweet->tweetlist = json_encode($tweetList);
            $objCleanTweet->save();
            unset($attributesList);
            unset($tweetList);
        }

        echo 'Done';
        exit;
    }

    /**
     * Get all attributes from training set
     */
    public function actionFindAttributesStep2() {
        //Step 2: obtain attributes from tweet2
        $request = Yii::app()->getRequest();
        $limitWords = $request->getParam('limit', 10);
        $maxList = 3200;
        $resultAttributeList = array();

        for($i = 1; $i < $maxList; $i++) {
            $objCleantweetList = Cleantweetlist::model()->find(array('limit' => 1, 'offset' => $i));

            $attributeList = json_decode($objCleantweetList->tweetlist, true);
            if(count($attributeList) == 0)
                echo $objCleantweetList->id . ' - ' . count($attributeList) . '<br>';

            unset($attributesList);
            unset($objCleantweetList);
        }
        echo 'Done';
        exit;
    }

    /**
     * Get all attributes from training set
     */
    public function actionFindAttributesStep3() {
        //Step 2: obtain attributes from tweet2
        $request = Yii::app()->getRequest();
        $limitWords = $request->getParam('limit', 10);
        $maxList = 3200;
        $resultAttributeList = array();

        for($i = 0; $i < $maxList; $i++) {
            $objCleantweetList = Cleantweetlist::model()->find(array('limit' => 1, 'offset' => $i));

            $attributeList = json_decode($objCleantweetList->attributelist, true);

            foreach ($attributeList as $attributeItem => $value) {
                if (isset($resultAttributeList[$attributeItem]))
                    $resultAttributeList[$attributeItem] = $resultAttributeList[$attributeItem] + $value;
                else
                    $resultAttributeList[$attributeItem] = $value;
            }
            unset($attributesList);
            unset($objCleantweetList);
        }

        foreach($resultAttributeList as $attributeItem => $value) {
            $tweetAttribute = new TweetlistAttribute();
            $tweetAttribute->attribute = $attributeItem;
            $tweetAttribute->counter = $value;
            $tweetAttribute->save();
        }
        echo 'done';
        exit;
    }

    /**
     * Construct ARFF file from a specific type
     */
    public function actionConstructArff() {
        $this->layout = false;

        //Step 1: obtain attributes from tweet2
        $request = Yii::app()->getRequest();
        $limitWords = $request->getParam('limit', 10);
        $arffType = $request->getParam('type', 0);

        list($attributesList, $arrSentiment, $mergedAttributesList) = $this->findAttributes($limitWords, $arffType);

        $tweetClassification = array();
        $objTweet = Tweetlab4::model()->findAll();

        foreach($objTweet as $tweet) {
            $tweetClassification[$tweet->id] = array();
            $tweetAttributes = explode(" ", $tweet->tweet2);

            //remove spaces to attributes of tweet
            for($i = 0; $i<count($tweetAttributes); $i++)
                $tweetAttributes[$i] = strtolower(trim($tweetAttributes[$i]));

            foreach($mergedAttributesList as $attributeItem)
                $tweetClassification[$tweet->id][] = in_array($attributeItem, $tweetAttributes) ? "'y'" : "'n'";

            $tweetClassification[$tweet->id][] = "'" . $this->arrSentiment[$tweet->sentiment] . "'";
        }

        $this->render('constructarff', array(
            'tweetClassification' => $tweetClassification,
            'attributesList' => $mergedAttributesList
        ));

    }

}

?>