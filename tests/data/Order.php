<?php

namespace yii\twig\tests\data;

use yii\activerecord\ActiveRecord;
use yii\db\Connection;

/**
 * Class Order
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $created_at
 * @property string $total
 */
class Order extends ActiveRecord
{
    public static $db;

    public static function getDb()
    {
        if (static::$db === null) {
            static::$db = new Connection();
            static::$db->dsn = 'sqlite::memory:';
        }
        return static::$db;
    }

    public static function setUp()
    {
        static::getDb()->createCommand(<<<SQL
CREATE TABLE "order" (
  id INTEGER NOT NULL,
  customer_id INTEGER NOT NULL,
  created_at INTEGER NOT NULL,
  total decimal(10,0) NOT NULL,
  PRIMARY KEY (id)
);
SQL
        )->execute();
    }

    public static function tableName()
    {
        return 'order';
    }

    public function getCustomer()
    {
        return $this->hasOne(Order::class, ['id' => 'customer_id']);
    }
}
