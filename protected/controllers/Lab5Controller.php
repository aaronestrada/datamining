<?php

class Lab5Controller extends Controller
{

    private $states = array('noun', 'adjective', 'verb', 'pronoun', 'stop');

    private $sentences = array(
        'I wish you a merry Christmas',
        'Happy Christmas to you and all your family',
        'Let this Christmas time be full of joy and love',
        'May beautiful moments and happy memories surround you with joy this Christmas',
        'Have a merry Christmas full of cheer and happiness',
        'Christmas Greetings',
        'The best of joy the best of cheer for Christmas',
        'May God bless you this Christmas',
        'Have a merry Christmas',
        'I wish you a Christmas overflowing with love and laughter',
        'May all your joyful Christmas dreams come true I love you',
        'My love you bring me so much joy I wish you the best Christmas ever this year',
        'My one Christmas wish is always to be with you',
        'I wish you great Christmas time full of love and happiness',
        'My best wishes for Christmas',
    );

    private $stateObservations = array(
        'noun' => array('one', 'christmas', 'family', 'joy', 'love', 'moments', 'memories', 'cheer', 'happiness', 'greetings', 'god', 'love', 'laughter', 'dreams', 'year', 'wish', 'time', 'wishes'),
        'adjective' => array('merry', 'happy', 'full', 'beautiful', 'best', 'overflowing', 'joyful', 'true', 'great',),
        'verb' => array('wish', 'let', 'surround', 'have', 'bless', 'come', 'love', 'bring',),
        'pronoun' => array('i', 'you'),
        'stop' => array('a', 'the', 'to', 'and', 'all', 'your', 'this', 'be', 'of', 'may', 'with', 'for', 'my', 'me', 'so', 'much', 'ever', 'only', 'is', 'always',)
    );

    private $observationsToStates = array(
        'one' => 'noun',
        'christmas' => 'noun',
        'family' => 'noun',
        'joy' => 'noun',
        'love' => array('noun', 'verb'),
        'moments' => 'noun',
        'memories' => 'noun',
        'cheer' => 'noun',
        'happiness' => 'noun',
        'greetings' => 'noun',
        'god' => 'noun',
        'laughter' => 'noun',
        'dreams' => 'noun',
        'year' => 'noun',
        'wish' => array('noun', 'verb'),
        'time' => 'noun',
        'wishes' => 'noun',
        'merry' => 'adjective',
        'happy' => 'adjective',
        'full' => 'adjective',
        'beautiful' => 'adjective',
        'best' => 'adjective',
        'overflowing' => 'adjective',
        'joyful' => 'adjective',
        'true' => 'adjective',
        'great' => 'adjective',
        'let' => 'verb',
        'surround' => 'verb',
        'have' => 'verb',
        'bless' => 'verb',
        'come' => 'verb',
        'bring' => 'verb',
        'i' => 'pronoun',
        'you' => 'pronoun',
        'a' => 'stop',
        'to' => 'stop',
        'and' => 'stop',
        'all' => 'stop',
        'your' => 'stop',
        'this' => 'stop',
        'be' => 'stop',
        'of' => 'stop',
        'may' => 'stop',
        'with' => 'stop',
        'for' => 'stop',
        'my' => 'stop',
        'me' => 'stop',
        'so' => 'stop',
        'much' => 'stop',
        'ever' => 'stop',
        'only' => 'stop',
        'is' => 'stop',
        'always' => 'stop',
        'the' => 'stop'
    );

    //global variables for alpha and viterbi lists
    private $alphaList = array();
    private $viterbiList = array();

    //global variables for alpha and viterbi calculations
    private $transitionProbabilities;
    private $observationProbabilities;
    private $initialStateProbabilities;
    private $stateList;
    private $observationList;

    /**
     * Find possible transitions for a group of observation words
     * @return array List of possible transitions between states
     */
    private function findTransitions()
    {
        $transitionCounter = array();

        //init states counter
        foreach ($this->states as $stateItem) {
            $transitionCounter[$stateItem] = array();
            foreach ($this->states as $subStateItem)
                $transitionCounter[$stateItem][$subStateItem] = 0;
        }

        //Step 1: For each word of a sentence, verify the possible transitions from a word to another
        foreach ($this->sentences as $sentence) {
            //obtain words as a list
            $sentenceWords = explode(' ', $sentence);

            //cycle visiting all words of a sentence
            for ($i = 0; $i < count($sentenceWords); $i++) {

                //start with current state of the word and view next to sum the respective transition from the current one
                foreach ($this->states as $currentState) {
                    //Verify if word is on the current state, verify next word to sum
                    if (in_array(strtolower($sentenceWords[$i]), $this->stateObservations[$currentState])) {

                        //validate that word has a next value
                        if ($i + 1 < count($sentenceWords))
                            foreach ($this->states as $nextState)
                                if (in_array(strtolower($sentenceWords[$i + 1]), $this->stateObservations[$nextState]))
                                    //add transition counter
                                    $transitionCounter[$currentState][$nextState]++;

                    }
                }
            }
        }

        //Step 2: calculate transition probabilities
        $transitionProbabilities = array();
        $transitionPosibilities = array();

        //init transition probabilities and possibilities array
        foreach ($this->states as $stateItem) {
            $transitionProbabilities[$stateItem] = array();
            foreach ($this->states as $subStateItem)
                $transitionProbabilities[$stateItem][$subStateItem] = 0;
        }

        foreach ($transitionCounter as $transitionState => $nextTransitionValues) {
            //init transition value from a state
            $transitionPosibilities[$transitionState] = array();

            //calculate transition total value
            $transitionTotal = 0;
            foreach ($nextTransitionValues as $nextTransition => $transitionValue)
                $transitionTotal += $transitionValue;

            //calculate percentage of transitions
            foreach ($nextTransitionValues as $nextTransition => $transitionValue) {
                $transitionProbabilities[$transitionState][$nextTransition] = $transitionTotal > 0 ? $transitionValue / $transitionTotal : 0;

                //set possible transition as next
                if ($transitionProbabilities[$transitionState][$nextTransition] > 0)
                    $transitionPosibilities[$transitionState][] = $nextTransition;
            }
        }

        return array($transitionProbabilities, $transitionPosibilities);
    }


    /**
     * Find initial probabilities for states
     * @return array List of initial probabilities
     */
    private function findInitialProbabilities() {
        $initialStateCount = array();
        $initialStateProbabilities = array();
        foreach ($this->states as $state) {
            $initialStateCount[$state] = 0;
            $initialStateProbabilities[$state] = 0;
        }


        //Step 1: Obtain list of first sentences states
        foreach ($this->sentences as $sentence) {
            $initialWord = '';
            $sentenceWords = explode(' ', $sentence);

            if (count($sentenceWords) > 0)
                $initialWord = strtolower($sentenceWords[0]);

            if ($initialWord != '') {
                foreach ($this->stateObservations as $stateObservation => $stateObservationList)
                    if (in_array($initialWord, $stateObservationList))
                        //sum to counter
                        $initialStateCount[$stateObservation]++;
            }
        }

        //Step 2: Obtain probabilities of initial states
        $totalSentences = count($this->sentences);
        foreach ($initialStateCount as $initialState => $stateValue)
            $initialStateProbabilities[$initialState] = $stateValue / $totalSentences;

        return $initialStateProbabilities;
    }

    /**
     * Obtain observation probabilities
     * @return array
     */
    private function findObservationProbabilitiesValues() {
        //step 1: Init observation probabilities list
        $observationProbabilitiesValues = array();
        $observationProbabilitiesCount = array();
        foreach($this->states as $state) {
            $observationProbabilitiesValues[$state] = array();
            $observationProbabilitiesCount[$state] = array();
            foreach(array_keys($this->observationsToStates) as $observations) {
                $observationProbabilitiesValues[$state][$observations] = 0;
                $observationProbabilitiesCount[$state][$observations] = 0;
            }
        }

        //step 2: for each sentence, count the times it has a transition
        foreach($this->sentences as $sentence) {
            $sentenceWords = explode(' ', $sentence);
            foreach($sentenceWords as $sentenceWord) {
                $sentenceWord = strtolower($sentenceWord);
                $stateType = $this->observationsToStates[$sentenceWord];

                if(!is_array($stateType))
                    $observationProbabilitiesCount[$stateType][$sentenceWord]++;
                else
                    foreach($stateType as $stateItem)
                        $observationProbabilitiesCount[$stateItem][$sentenceWord]++;
            }
        }

        //step 3: calculate probabilities values
        foreach($observationProbabilitiesCount as $observationState => $stateValues) {
            $stateTotal = 0;
            foreach($stateValues as $observation => $observationValue)
                $stateTotal = $stateTotal + $observationValue;

            foreach($stateValues as $observation => $observationValue)
                $observationProbabilitiesValues[$observationState][$observation] = $stateTotal > 0 ? $observationValue / $stateTotal : 0;

        }

        return $observationProbabilitiesValues;
    }

    /**
     * Save alpha calculated value in array
     * @param $index Index of alpha
     * @param $state State of alpha
     * @param $value Value to be saved
     */
    private function pushAlpha($index, $state, $value) {
        if(!isset($this->alphaList[$index]))
            $this->alphaList[$index] = array();

        $this->alphaList[$index][$state] = $value;
    }

    /**
     * Get alpha (index,state) value
     * @param $index Index to obtain
     * @param $state State to obtain
     * @return null
     */
    private function getAlpha($index, $state) {
        if(!isset($this->alphaList[$index][$state]))
            return null;
        return $this->alphaList[$index][$state];
    }

    /**
     * Get viterbi (index,state) value
     * @param $index Index to obtain
     * @param $state State to obtain
     * @return null
     */
    private function getViterbi($index, $state) {
        if(!isset($this->viterbiList[$index][$state]))
            return null;
        return $this->viterbiList[$index][$state];
    }

    /**
     * Calculate alpha value for forward algorithm
     * @param $alphaIndex Index of alpha
     * @param $alphaState State i for that alpha
     * @return int|null
     */
    private function alpha($alphaIndex, $alphaState) {
        //verify that alpha value already exists to just obtain it from the list
        $alphaValue = $this->getAlpha($alphaIndex, $alphaState);
        if($alphaValue != null)
            return $alphaValue;

        //if alpha value not found, calculate it
        $alphaValue = 0;
        $currentObservation = strtolower($this->observationList[$alphaIndex]);

        //verify case base for alpha(1,state)
        if($alphaIndex == 0)
            $alphaValue = $this->observationProbabilities[$alphaState][$currentObservation] * $this->initialStateProbabilities[$alphaState];
        else {
            /**
             * Definition for recursion in alpha in previous state
             * Alpha(index,state) = b(observation) * SUM(a(state, nextstate) * alpha(previous index, iteration state)
             */
            foreach($this->stateList as $iterationState) {
                $findPreviousAlpha = $this->getAlpha($alphaIndex - 1, $iterationState);
                $previousAlpha = $findPreviousAlpha != null ?
                    $findPreviousAlpha :
                    $this->alpha($alphaIndex - 1, $iterationState);

                $alphaValue = $alphaValue +
                    (
                        $this->transitionProbabilities[$iterationState][$alphaState] * $previousAlpha
                    );
            }

            $alphaValue = $alphaValue * $this->observationProbabilities[$alphaState][$currentObservation];
        }
        //save in alpha list values
        $this->pushAlpha($alphaIndex, $alphaState, $alphaValue);
        return $alphaValue;
    }


    /**
     * Save Viterbi values for index and state in an array
     * @param $index Index of Viterbi
     * @param $state State of Viterbi
     * @param $value Value to be saved
     */
    private function pushViterbi($index, $state, $value) {
        if(!isset($this->viterbiList[$index]))
            $this->viterbiList[$index] = array();

        $this->viterbiList[$index][$state] = $value;
    }

    /**
     * Calculate Viterbi value for algorithm and find the path of observations
     * @param $viterbiIndex Index of Viterbi
     * @param $viterbiState State of Viterbi
     * @return int Value of calculated Viterbi
     */
    private function viterbi($viterbiIndex, $viterbiState) {
        //verify that viterbi value already exists to just obtain it from the list
        $viterbiValue = $this->getViterbi($viterbiIndex, $viterbiState);
        if($viterbiValue != null)
            return $viterbiValue;

        //if not found, calculate it
        $viterbiValues = array();
        $currentObservation = strtolower($this->observationList[$viterbiIndex]);

        //verify case base for alpha(1,state)
        if($viterbiIndex == 0)
            $viterbiValue = $this->observationProbabilities[$viterbiState][$currentObservation] * $this->initialStateProbabilities[$viterbiState];
        else {
            /**
             * Definition for recursion in viterbi in previous state
             * Viterbi(index,state) = MAX(b(observation) * a(state, nextstate) * alpha(previous index, iteration state))
             */
            foreach($this->stateList as $iterationState) {
                $findPreviousViterbi = $this->getViterbi($viterbiIndex - 1, $iterationState);
                $previousViterbi = $findPreviousViterbi != null ?
                    $findPreviousViterbi :
                    $this->viterbi($viterbiIndex - 1, $iterationState);

                $viterbiValue =
                    (
                        $this->transitionProbabilities[$iterationState][$viterbiState] * $previousViterbi
                        * $this->observationProbabilities[$viterbiState][$currentObservation]
                    );

                array_push($viterbiValues, $viterbiValue);
            }
            asort($viterbiValues);
            $viterbiValue = array_pop($viterbiValues);
        }

        //save in Viterbi list values
        $this->pushViterbi($viterbiIndex, $viterbiState, $viterbiValue);
        return $viterbiValue;
    }

    /**
     * Show transitions in screen
     */
    public function actionFindTransitions()
    {
        list($transitionProbabilities, $transitionPossibilities) = $this->findTransitions();

        $this->render('findtransitions', array(
            'transitionProbabilities' => $transitionProbabilities,
            'states' => $this->states
        ));
    }

    /**
     * Calculate transition observations probabilities
     */
    public function actionSentenceTransitions()
    {
        $stateObservationProbabilities = $this->findObservationProbabilitiesValues();

        $this->render('observationprobabilities', array(
            'observationList' => array_keys($this->observationsToStates),
            'stateObservationProbabilities' => $stateObservationProbabilities,
        ));
    }

    /**
     * Find probabilities to enter to an initial state
     */
    public function actionInitialProbabilities()
    {
        $initialStateProbabilities = $this->findInitialProbabilities();
        $this->render('initialprobabilities', array(
            'initialStateProbabilities' => $initialStateProbabilities
        ));
    }

    /**
     * Obtain values for total probability with Forward algorithm and path with Viterbi algorithm
     * @param $observations List of observations
     * @param $transitionProbabilities Transition probability list
     * @param $initialStateProbabilities Initial state probabilities
     * @param $observationProbabilities Observation probabilities
     * @param $states List of possible states
     * @return array ($totalProbabilityValue, $transitionPathText)
     */
    private function findForwardAlgorithm($observations, $transitionProbabilities, $initialStateProbabilities, $observationProbabilities, $states) {
        $observationList = explode(' ', $observations);
        $observationIndex = count($observationList) - 1;

        //Save values in global variables for performance issues in recursion
        $this->transitionProbabilities = $transitionProbabilities;
        $this->observationProbabilities = $observationProbabilities;
        $this->initialStateProbabilities = $initialStateProbabilities;
        $this->stateList = $states;
        $this->observationList = $observationList;

        $totalProbabilityValue = 0;
        foreach($states as $stateItem) {
            //calculate alpha values and sum up to obtain total probability
            $alphaValue = $this->alpha($observationIndex, $stateItem);
            $totalProbabilityValue = $totalProbabilityValue + $alphaValue;

            //calculate Viterbi values with algorithm
            $this->viterbi($observationIndex, $stateItem);
        }

        //print_r($this->viterbiList); exit;

        //get transition path for Viterbi algorithm
        $transitionPath = array();
        foreach($this->viterbiList as $viterbiLevel) {
            $viterbiLevelList = $viterbiLevel;
            asort($viterbiLevelList); //order level of Viterbi to obtain last value with array_pop
            $stateTransitions = array_keys($viterbiLevelList); //keys for transitions

            $stateTransitionValue = array_pop($viterbiLevelList); //obtain from transition keys

            //verify that probability exists to insert into list, otherwise is null
            $transitionState = $stateTransitionValue > 0 ? array_pop($stateTransitions) : null;
            array_push($transitionPath, $transitionState);
        }

        //return value or probability and an array with the path
        return array($totalProbabilityValue, $transitionPath);
    }


    /**
     * Show probability and transitions calculations
     */
    public function actionFindForward() {
        list($transitionProbabilities, $transitionPossibilities) = $this->findTransitions();
        $initialStateProbabilities = $this->findInitialProbabilities();
        $observationProbabilities = $this->findObservationProbabilitiesValues();
        $states = $this->states;

        $appRequest = Yii::app()->getRequest();
        $sentence = $appRequest->getParam('sentence', '');

        $totalProbabilityValue = 0;
        $transitionPath = array();
        $sentenceWords = array();

        if($sentence != '') {
            //calculate process
            list($totalProbabilityValue, $transitionPath) = $this->findForwardAlgorithm($sentence, $transitionProbabilities, $initialStateProbabilities, $observationProbabilities, $states);
            $sentenceWords = explode(' ', $sentence);
        }

        //simulate alpha calculation
        /*for($observationIndex = count($sentenceWords) - 1; $observationIndex >= 0; $observationIndex--) {
            $observedWord = strtolower($sentenceWords[$observationIndex]);

            for($stateNumber = 0; $stateNumber < count($states); $stateNumber++) {
                $fromState = $states[$stateNumber];
                echo 'Alpha '. ($observationIndex + 1) . ' (' . $fromState . ') = ';
                if($observationIndex > 0) {
                    //if($observationProbabilities[$fromState][$observedWord] > 0) {
                        echo 'b(' . $fromState . ')(' . $observedWord . ') = ' . $observationProbabilities[$fromState][$observedWord] . '* [<br>';

                        $arrMult = array();
                        for ($transitionStateNumber = 0; $transitionStateNumber < count($states); $transitionStateNumber++) {
                            $transitionState = $states[$transitionStateNumber];
                            array_push($arrMult, '&nbsp;&nbsp;a(' . $transitionState . ',' . $fromState . ') = ' . $transitionProbabilities[$transitionState][$fromState] . ' * Alpha ' . ($observationIndex) . ' (' . $transitionState . ')');
                        }
                        echo implode(' + <br>', $arrMult);
                        echo '<br>]<br><br>';
                    //}
                    //else echo '0 <br>';

                }
                else {

                    echo 'b(' . $fromState . ')(' . $observedWord . ') = ' . $observationProbabilities[$fromState][$observedWord] . ' * ';
                    echo $initialStateProbabilities[$fromState] . ' = ';
                    echo $observationProbabilities[$fromState][$observedWord] * $initialStateProbabilities[$fromState];
                    echo '<br>';
                }

            }
            echo '<br>';
        }*/

        $this->render('findforward', array(
            'sentence' => $sentence,
            'totalProbabilityValue' => $totalProbabilityValue,
            'transitionPath' => $transitionPath,
            'sentenceWords' => $sentenceWords
        ));

    }

    /**
     * Simulation for low / high pressure Hidden Markov Model
     */
    public function actionLowHighPressure() {
        $initialStateProbabilities = array(
            'low' => 0.5,
            'high' => 0.5
        );

        $transitionProbabilities = array(
            'low' => array(
                'low' => 0.6,
                'high' => 0.4
            ),
            'high' => array(
                'low' => 0.7,
                'high' => 0.3
            )
        );

        $observationProbabilities = array(
            'low' => array(
                'sun' => 0.2,
                'cloud' => 0.4,
                'rain' => 0.4
            ),
            'high' => array(
                'sun' => 0.6,
                'cloud' => 0.1,
                'rain' => 0.3
            )
        );

        $states = array('low', 'high');
        $observations = 'sun sun sun';

        list($totalProbabilityValue, $transitionPath) = $this->findForwardAlgorithm($observations, $transitionProbabilities, $initialStateProbabilities, $observationProbabilities, $states);
        echo 'TOTAL PROBABILITY: ' . number_format($totalProbabilityValue * 100, 4) . '%<br>*****<br>';
        print_r($transitionPath);
    }

}
