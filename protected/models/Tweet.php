<?php

/**
 * This is the model class for table "tweet".
 *
 * The followings are the available columns in table 'tweet':
 * @property integer $id
 * @property integer $sentiment
 * @property string $sentiment_label
 * @property string $date
 * @property string $hashtag
 * @property string $nickname
 * @property string $tweet
 * @property string $tweet2
 * @property integer $label_id
 *
 * The followings are the available model relations:
 * @property Label $label
 */
class Tweet extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tweet';
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
			array('id, sentiment, label_id', 'numerical', 'integerOnly'=>true),
			array('sentiment_label', 'length', 'max'=>25),
			array('hashtag', 'length', 'max'=>45),
			array('nickname', 'length', 'max'=>100),
			array('date, tweet, tweet2', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sentiment, sentiment_label, date, hashtag, nickname, tweet, tweet2, label_id', 'safe', 'on'=>'search'),
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
			'label' => array(self::BELONGS_TO, 'Label', 'label_id'),
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
			'sentiment_label' => 'Sentiment Label',
			'date' => 'Date',
			'hashtag' => 'Hashtag',
			'nickname' => 'Nickname',
			'tweet' => 'Tweet',
			'tweet2' => 'Tweet2',
			'label_id' => 'Label',
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
		$criteria->compare('sentiment_label',$this->sentiment_label,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('hashtag',$this->hashtag,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('tweet',$this->tweet,true);
		$criteria->compare('tweet2',$this->tweet2,true);
		$criteria->compare('label_id',$this->label_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tweet the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
