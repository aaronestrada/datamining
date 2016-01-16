<?php

/**
 * This is the model class for table "tweetlab6".
 *
 * The followings are the available columns in table 'tweetlab6':
 * @property integer $id
 * @property integer $sentiment
 * @property string $date
 * @property string $hashtag
 * @property string $nickname
 * @property string $tweet
 * @property string $attributelist
 */
class Tweetlab6 extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tweetlab6';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id, sentiment', 'numerical', 'integerOnly'=>true),
			array('hashtag', 'length', 'max'=>45),
			array('nickname', 'length', 'max'=>100),
			array('date, tweet, attributelist', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sentiment, date, hashtag, nickname, tweet, attributelist', 'safe', 'on'=>'search'),
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
			'date' => 'Date',
			'hashtag' => 'Hashtag',
			'nickname' => 'Nickname',
			'tweet' => 'Tweet',
			'attributelist' => 'Attributelist',
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('hashtag',$this->hashtag,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('tweet',$this->tweet,true);
		$criteria->compare('attributelist',$this->attributelist,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tweetlab6 the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
