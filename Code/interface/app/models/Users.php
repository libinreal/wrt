<?php

use Phalcon\Mvc\Model\Validator\Uniqueness as UniquenessValidator;
use PhpRudder\Security\Auth;

class Users extends \PhpRudder\Mvc\ModelBase implements Auth
{
	const LEVEL_MEMBER = 0;
	const LEVEL_VIP1MEMBER = 1;
	const LEVEL_VIP2MEMBER = 2;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $user_name;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $question;

    /**
     *
     * @var string
     */
    public $answer;

    /**
     *
     * @var integer
     */
    public $sex;

    /**
     *
     * @var string
     */
    public $birthday;

    /**
     *
     * @var double
     */
    public $user_money;

    /**
     *
     * @var double
     */
    public $frozen_money;

    /**
     *
     * @var integer
     */
    public $pay_points;

    /**
     *
     * @var integer
     */
    public $rank_points;

    /**
     *
     * @var integer
     */
    public $address_id;

    /**
     *
     * @var integer
     */
    public $reg_time;

    /**
     *
     * @var integer
     */
    public $last_login;

    /**
     *
     * @var string
     */
    public $last_time;

    /**
     *
     * @var string
     */
    public $last_ip;

    /**
     *
     * @var integer
     */
    public $visit_count;

    /**
     *
     * @var integer
     */
    public $user_rank;

    /**
     *
     * @var integer
     */
    public $is_special;

    /**
     *
     * @var string
     */
    public $ec_salt;

    /**
     *
     * @var string
     */
    public $salt;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var integer
     */
    public $flag;

    /**
     *
     * @var integer
     */
    public $alias;

    /**
     *
     * @var string
     */
    public $msn;

    /**
     *
     * @var string
     */
    public $qq;

    /**
     *
     * @var string
     */
    public $office_phone;

    /**
     *
     * @var string
     */
    public $home_phone;

    /**
     *
     * @var string
     */
    public $mobile_phone;

    /**
     *
     * @var integer
     */
    public $is_validated;

    /**
     *
     * @var double
     */
    public $credit_line;

    /**
     *
     * @var string
     */
    public $passwd_question;

    /**
     *
     * @var string
     */
    public $passwd_answer;

    /**
     *
     * @var string
     */
    public $weixin;

    /**
     *
     * @var string
     */
    public $companyName;

    /**
     *
     * @var string
     */
    public $companyAddress;

    /**
     *
     * @var string
     */
    public $officePhone;

    /**
     *
     * @var string
     */
    public $fax;

    /**
     *
     * @var string
     */
    public $position;

    /**
     *
     * @var string
     */
    public $projectName;

    /**
     *
     * @var string
     */
    public $projectBrief;

    /**
     *
     * @var string
     */
    public $contacts;

    /**
     *
     * @var string
     */
    public $contactsPhone;

    /**
     *
     * @var string
     */
    public $secondContacts;

    /**
     *
     * @var string
     */
    public $secondPhone;

    /**
     *
     * @var string
     */
    public $customNo;

    /**
     *
     * @var integer
     */
    public $customLevel;

    /**
     *
     * @var string
     */
    public $credit_rank;

    /**
     *
     * @var string
     */
    public $department;

    /**
     *
     * @var string
     */
    public $icon;

    /**
     *
     * @var integer
     */
    public $credits;

    /**
     *
     * @var string
     */
    public $customerAccount;

    /**
     *
     * @var string
     */
    public $customerNo;

    /**
     * Validations and business logic
     */
    public function validation()
    {
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'account',
            'message' => '用户名不能为空！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'password',
            'message' => '密码不能为空！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'contacts',
            'message' => '请填写联系人姓名！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'companyName',
            'message' => '请填写您的单位名称！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'companyAddress',
            'message' => '请填写您的单位地址！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'officePhone',
            'message' => '请填写您的办公电话！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'fax',
            'message' => '请填写您的传真！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'position',
            'message' => '请填写您的职位信息！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'department',
            'message' => '请填写您的所在的部门！'
        )));
        $this->validate(new \Phalcon\Mvc\Model\Validator\PresenceOf(array(
            'field' => 'telephone',
            'message' => '联系人手机号码不能为空！'
        )));
        $this->validate(new UniquenessValidator(array(
            'field' => 'account',
            'message' => '用户名已经被使用，请重新再试！'
        )));
        $this->validate(new UniquenessValidator(array(
            'field' => 'telephone',
            'message' => '手机号码已经被其他用户使用，请重新再试！'
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'user_id' => 'id',
            'email' => 'email',
            'user_name' => 'account',
            'password' => 'password',
            'question' => 'question',
            'answer' => 'answer',
            'sex' => 'gender',
            'birthday' => 'birthday',
            'user_money' => 'user_money',
            'frozen_money' => 'frozen_money',
            'pay_points' => 'pay_points',
            'rank_points' => 'rank_points',
            'address_id' => 'addressId',
            'reg_time' => 'createAt',
            'last_login' => 'last_login',
            'last_time' => 'last_time',
            'last_ip' => 'last_ip',
            'visit_count' => 'visit_count',
            'user_rank' => 'user_rank',
            'is_special' => 'is_special',
            'ec_salt' => 'ec_salt',
            'salt' => 'salt',
            'parent_id' => 'parent_id',
            'flag' => 'flag',
            'alias' => 'alias',
            'msn' => 'msn',
            'qq' => 'qq',
            'office_phone' => 'office_phone',
            'home_phone' => 'home_phone',
            'mobile_phone' => 'mobile_phone',
            'is_validated' => 'is_validated',
            'credit_line' => 'credit_line',
            'passwd_question' => 'passwd_question',
            'passwd_answer' => 'passwd_answer',
            'weixin' => 'weixin',
            'companyName' => 'companyName',
            'companyAddress' => 'companyAddress',
            'officePhone' => 'officePhone',
            'fax' => 'fax',
            'position' => 'position',
            'projectName' => 'projectName',
            'projectBrief' => 'projectBrief',
            'contacts' => 'contacts',
            'contactsPhone' => 'telephone',
            'secondContacts' => 'secondContacts',
            'secondPhone' => 'secondPhone',
            'customNo' => 'customNo',
            'customLevel' => 'customLevel',
            'credit_rank' => 'creditLevel',
            'department' => 'department',
            'icon' => 'icon',
			'credits' => 'credits',
            'customerAccount' => 'customerAccount',
            'customerNo' => 'customerNo',
        	'bill_amount_history' => 'billAmountHistory', 
        	'bill_amount_valid' => 'billAmountValid', 
        	'cash_amount_history' => 'cashAmountHistory', 
        	'cash_amount_valid' => 'cashAmountValid', 
        	'inv_payee' => 'invPayee', 
        	'inv_bank_name' => 'invBankName', 
        	'inv_bank_account' => 'invBankAccount', 
        	'inv_bank_address' => 'invBankAddress', 
        	'inv_tel' => 'invTel', 
        	'inv_fax' => 'invFax', 
        );
    }

    public function initialize() {
    	$attributes = array(
    			'question',
    			'answer',
    			'user_money',
    			'frozen_money',
    			'pay_points',
    			'rank_points',
    			'last_login',
    			'last_time',
    			'last_ip',
    			'visit_count',
    			'user_rank',
    			'is_special',
    			'ec_salt',
    			'salt',
    			'parent_id',
    			'flag',
    			'msn',
    			'office_phone',
    			'home_phone',
    			'mobile_phone',
    			'is_validated',
    			'credit_line',
    			'passwd_question',
    			'passwd_answer',
    	);
		$this->skipAttributes($attributes);
    }

    public function beforeCreate() {
    	//Set the creation date
    	$this->createAt = time();
    	//删除标识
    	$this->alias = 0;
    }

    public function getId() {
    	return $this->userId;
    }

    public function getName() {
    	return $this->account;
    }

}
