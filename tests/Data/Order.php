<?php

namespace Yiisoft\Yii\Twig\Tests\Data;

/**
 * Class Order
 *
 * @property integer $id
 * @property integer $customer_id
 * @property integer $created_at
 * @property string $total
 */
class Order extends \yii\base\Model
{
    public $id;
    public $customer_id;
    public $created_at;
    public $total;
}
