<?php

class Lab4Controller extends Controller {
    private $arrSentiment = array(
        0 => 'Negative',
        2 => 'Neutral',
        4 => 'Positive'
    );

    /**
     * Extract from original tweet and save in new field
     */
    private function extractfromtweet() {
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
        $objTweet = Tweetlab4::model()->findAll();
        foreach ($objTweet as $tweet) {
            //step 1: delete symbols from tweets
            $cleanedTweet = $tweet->tweet;
            $cleanedTweet = str_replace(array(',', '...', '.', '?', "'", '!', '-',";", "@", "#", '"', '=', '[', ']', '*', '+', '/', '\\', "$"), " ", $tweet->tweet);
            $cleanedTweet = str_replace('&lt', ' ', $cleanedTweet);
            $cleanedTweet = str_replace('&gt', ' ', $cleanedTweet);
            $cleanedTweet = str_replace('&amp', '&', $cleanedTweet);

            //step 2: obtain tweet as array
            $arrNewtweet = array();
            $arrCleanedTweet = explode(" ", $cleanedTweet);
            foreach ($arrCleanedTweet as $cleanedTweetWord) {
                //step 3: check if the word exists from the forbidden words to include it as the new tweet
                if(($cleanedTweetWord !== ' ') && (!in_array(strtolower($cleanedTweetWord), $forbiddenWords)))
                    array_push($arrNewtweet, $cleanedTweetWord);
            }

            //step 4: save the new tweet
            $newtweet = implode(" ", $arrNewtweet);
            $tweet->tweet2 = $newtweet;
            $tweet->save();
        }
    }


    /**
     * Find attributes depending on a type
     * @param $limitWords
     * @param $type With positive / negative words = 1, From the training set = 0
     * @return array
     */
    private function findAttributes($limitWords, $type) {
        //Step 1: clean tweet and save in field tweet2
        $this->extractfromtweet();
        $attributesList = array();

        //Step 2: obtain attributes from tweet2
        if($type == 1) {
            $objTweet = Tweetlab4::model()->findAll();
            foreach ($objTweet as $tweet) {
                $tweetAttributes = explode(" ", $tweet->tweet2);

                foreach ($tweetAttributes as $tweetAttr) {
                    $tweetWord = strtolower(trim($tweetAttr));
                    if ($tweetWord != '') {
                        $objWordCount = Word::model()->count('word = :word', array(':word' => $tweetWord));
                        if ($objWordCount > 0) {
                            if (isset($attributesList[$tweetWord]))
                                $attributesList[$tweetWord]++;
                            else
                                $attributesList[$tweetWord] = 1;
                        }
                    }
                }
            }
            //Step 3: sort array from high to low
            arsort($attributesList);

            $mergedAttributesList = array();

            $counter = 1;
            foreach ($attributesList as $attribute => $value) {
                if(!in_array($attribute, $mergedAttributesList))
                    array_push($mergedAttributesList, $attribute);

                $counter++;
                if($counter > $limitWords)
                    break;
            }
        }
        else {
            foreach ($this->arrSentiment as $sentiment => $label_sentiment) {
                $attributesList[$sentiment] = array();

                $objTweet = Tweetlab4::model()->findAll('sentiment = :sentiment', array(':sentiment' => $sentiment));
                foreach ($objTweet as $tweet) {
                    $tweetAttributes = explode(" ", $tweet->tweet2);

                    foreach ($tweetAttributes as $tweetAttr) {
                        $tweetWord = strtolower(trim($tweetAttr));
                        if ($tweetWord != '') {
                            if ($type == 1) {
                                $objWordCount = Word::model()->count('word = :word', array(':word' => $tweetWord));
                                if ($objWordCount > 0) {
                                    if (isset($attributesList[$sentiment][$tweetWord]))
                                        $attributesList[$sentiment][$tweetWord]++;
                                    else
                                        $attributesList[$sentiment][$tweetWord] = 1;
                                }
                            } else {
                                if (isset($attributesList[$sentiment][$tweetWord]))
                                    $attributesList[$sentiment][$tweetWord]++;
                                else
                                    $attributesList[$sentiment][$tweetWord] = 1;
                            }
                        }
                    }
                }
                //Step 3: sort array from high to low
                arsort($attributesList[$sentiment]);
            }
            $mergedAttributesList = array();
            foreach($this->arrSentiment as $sentiment => $label_sentiment) {
                $counter = 1;
                foreach ($attributesList[$sentiment] as $attribute => $value) {
                    if(!in_array($attribute, $mergedAttributesList))
                        array_push($mergedAttributesList, $attribute);

                    $counter++;
                    if($counter > $limitWords)
                        break;
                }
            }

        }

        return array(
            $attributesList,
            $this->arrSentiment,
            $mergedAttributesList
        );
    }

    /**
     * Get all attributes from training set
     */
    public function actionFindAttributes() {
        //Step 1: clean tweet and save in field tweet2
        $this->extractfromtweet();

        //Step 2: obtain attributes from tweet2
        $request = Yii::app()->getRequest();
        $limitWords = $request->getParam('limit', 10);
        list($attributesList, $arrSentiment, $mergedAttributesList) = $this->findAttributes($limitWords, 0);

        $this->render('findattributes', array(
            'attributesList' => $attributesList,
            'sentimentsList' => $arrSentiment,
            'mergedAttributesList' => $mergedAttributesList,
            'limitWords' => $limitWords
        ));
    }

    /**
     * Get all attributes from positive / negative words
     */
    public function actionFindallAttributes() {
        //Step 1: clean tweet and save in field tweet2
        $this->extractfromtweet();

        //Step 2: obtain attributes from tweet2
        $request = Yii::app()->getRequest();
        $limitWords = $request->getParam('limit', 10);
        list($attributesList, $arrSentiment, $mergedAttributesList) = $this->findAttributes($limitWords, 1);

        $this->render('findallattributes', array(
            'attributesList' => $attributesList,
            'limitWords' => $limitWords
        ));
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