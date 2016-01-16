<?php

/**
 * This is the model class for table "tweetlist".
 *
 * The followings are the available columns in table 'tweetlist':
 * @property integer $id
 * @property integer $sentiment
 * @property string $tid
 * @property string $date
 * @property string $hashtag
 * @property string $username
 * @property string $tweet
 * @property string $tweet2
 */
class Tweetlist extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tweetlist';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sentiment', 'numerical', 'integerOnly'=>true),
			array('tid', 'length', 'max'=>20),
			array('hashtag, username', 'length', 'max'=>100),
			array('date, tweet, tweet2', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sentiment, tid, date, hashtag, username, tweet, tweet2', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sentiment' => 'Sentiment',
			'tid' => 'Tid',
			'date' => 'Date',
			'hashtag' => 'Hashtag',
			'username' => 'Username',
			'tweet' => 'Tweet',
			'tweet2' => 'Tweet2',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('sentiment',$this->sentiment);
		$criteria->compare('tid',$this->tid,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('hashtag',$this->hashtag,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('tweet',$this->tweet,true);
		$criteria->compare('tweet2',$this->tweet2,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tweetlist the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
