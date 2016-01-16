<?php

class Lab3Controller extends Controller
{

    /**
     * Extract from original tweet
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
            "http", "bit", "ly", "com", "www", "rt", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "&");

        //get all tweets
        $objTweet = Tweet::model()->findAll();
        foreach ($objTweet as $tweet) {
            //step 1: delete symbols from tweets
            $cleanedTweet = str_replace(array(',', '...', '.', '?', "'", '!', '-', ':', '(', ')',";", "@", "#", '"', '=', '[', ']', '*', '+', '/', '\\', "$"), " ", $tweet->tweet);
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
     * Get counter of words from tweet and save as attributes
     */
    public function actionMostfrequentword() {
        $this->layout = false;

        //step 1: clean tweet from words
        $this->extractfromtweet();

        //step 2: get all tweets
        $wordlist = array();
        $objTweet = Tweet::model()->findAll('label_id IS NULL');
        foreach ($objTweet as $tweet) {
            //step 3: get list of words on each tweet
            $arrTweetWords = explode(" ", $tweet->tweet2);
            foreach ($arrTweetWords as $tweetWord) {
                $tweetWord = strtolower(trim($tweetWord));

                //get counter for each word
                if ($tweetWord != '') {
                    if (isset($wordlist[$tweetWord]))
                        $wordlist[$tweetWord]++;
                    else
                        $wordlist[$tweetWord] = 1;
                }
            }
        }

        //sort array from high to low
        arsort($wordlist);

        //step 4: delete all attributes and save again
        Tweetattribute::model()->deleteAll();

        //save attributes in table
        $totalCounter = 0;
        foreach ($wordlist as $key => $value) {
            $totalCounter+=$value;
            $tweetAttribute = new Tweetattribute();
            $tweetAttribute->word = $key;
            $tweetAttribute->counter = $value;
            $tweetAttribute->save();
        }

        $this->render('getcounter', array('wordlist' => $wordlist, 'totalCounter' => $totalCounter));
    }

    /**
     * Get attributes associated to tweets
     */
    public function actionGetattributes() {
        $this->layout = false;

        $request = Yii::app()->getRequest();

        //step 1: get list of attributes
        $arrAttributes = array();
        $objAttributes = Tweetattribute::model()->findAll(array('order' => 'counter DESC', 'limit' => $request->getParam('limit'), 'offset' => $request->getParam('offset')));
        foreach ($objAttributes as $attribute)
            $arrAttributes[] = $attribute->word;

        //step 2: get all tweets
        $catalogWordList = array();
        $objTweet = Tweet::model()->findAll('label_id IS NULL');
        foreach ($objTweet as $tweet) {

            //create tweet classification
            $wordCatalog = array('id' => $tweet->id);
            foreach ($arrAttributes as $attribute)
                $wordCatalog[$attribute] = false;

            $arrWord = explode(" ", strtolower($tweet->tweet2));
            foreach ($arrWord as $word) {
                if (in_array($word, $arrAttributes))
                    $wordCatalog[$word] = true;
            }

            array_push($catalogWordList, $wordCatalog);
        }

        $tweetList = array();
        foreach ($catalogWordList as $catalogitem) :
            //$arrRow = array($catalogitem['id']);
            $arrRow = array();
            foreach ($arrAttributes as $attributeitem) :
                $arrRow[] = $catalogitem[$attributeitem] == true ? "'y'" : "'n'";
                if($catalogitem[$attributeitem] == true) $includeTweet = true;
            endforeach;
            $tweetList[] = $arrRow;
        endforeach;

        $this->render('getattributes', array(
            'attributelist' => $arrAttributes,
            'cataloglist' => $catalogWordList,
            'list' => $tweetList
        ));
    }

    private function actionSetLabelWeight() {
        $objLabels = Label::model()->findAll(array('order' => 'name ASC'));
        foreach($objLabels as $label) {
            $labelCounter = 0;

            $labelList = explode(" ", $label->name);
            foreach($labelList as $labelItem) {
                $criteria = new CDbCriteria();
                $criteria->condition = 'word = :word';
                $criteria->params = array(':word' => strtolower($labelItem));

                $objAttribute = Tweetattribute::model()->findAll($criteria);
                foreach ($objAttribute as $attributeItem)
                    $labelCounter += $attributeItem->counter;
            }

            echo strtolower($label->name) . ' ' . $labelCounter . '<br>';
            $label->weight = $labelCounter;
            $label->save();
        }
    }

    /**
     * Set label for each tweet based on label definitions
     */
    public function actionSetLabels2() {
        //step 0: set label weight based on attribute counting
        //$this->actionSetLabelWeight();

        //step 1: get all labels
        $arrLabelList = array();
        $objLabels = Label::model()->findAll(array('order' => 'weight DESC'));
        $objTweet = Tweet::model()->findAll('label_id IS NULL');
        $arrTweetList = array();

        foreach($objLabels as $label) {
            $labelList = explode(" ", $label->name);

            foreach($objTweet as $tweet) {
                $arrTweetWords = explode(" ", strtolower($tweet->tweet2));
                asort($arrTweetWords);

                $wordCounter = 0;
                foreach($labelList as $labelItem)
                    if(in_array(strtolower($labelItem), $arrTweetWords))
                        $wordCounter++;

                if($wordCounter == count($labelList))
                    $arrTweetList[$tweet->id][$label->id] = $wordCounter;
            }
        }

        //step 2: get all tweets and set label
        foreach ($objTweet as $tweet) {
            if(isset($arrTweetList[$tweet->id])) {
                if(count($arrTweetList[$tweet->id]) > 0) {
                    asort($arrTweetList[$tweet->id]);
                    $label_id = end(array_keys($arrTweetList[$tweet->id]));
                    $tweet->label_id = $label_id;
                    $tweet->save();
                }
            }
        } //foreach

        //step 3: match tweets with labels
        print_r($arrTweetList); exit;
    }


    /**
     * Set label for each tweet based on label definitions
     */
    public function actionSetLabels() {
        //step 0: set label weight based on attribute counting
        //$this->actionSetLabelWeight();

        //step 1: get all labels
        $arrLabelList = array();
        $objLabels = Label::model()->findAll(array('order' => 'weight DESC'));
        $objTweet = Tweet::model()->findAll('label_id IS NULL');
        $arrTweetList = array();

        foreach($objLabels as $label) {
            $labelList = explode(" ", $label->name);

            foreach($objTweet as $tweet) {
                $arrTweetWords = explode(" ", strtolower($tweet->tweet2));
                asort($arrTweetWords);

                $wordCounter = 0;
                foreach($labelList as $labelItem)
                    if(in_array(strtolower($labelItem), $arrTweetWords))
                        $wordCounter++;

                if($wordCounter > 0)
                    $arrTweetList[$tweet->id][$label->id] = $wordCounter;
            }
        }

        //step 2: get all tweets and set label
        foreach ($objTweet as $tweet) {
            if(isset($arrTweetList[$tweet->id])) {
                if(count($arrTweetList[$tweet->id]) > 0) {
                    asort($arrTweetList[$tweet->id]);
                    $label_id = end(array_keys($arrTweetList[$tweet->id]));
                    $tweet->label_id = $label_id;
                    $tweet->save();
                }
            }
        } //foreach

        print_r($arrTweetList); exit;

        //step 3: match tweets with labels
    }


}

?>