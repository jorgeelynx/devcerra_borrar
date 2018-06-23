<?php
/**
 * Orderfiles Prestashop module
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Wiktor Koźmiński
 *  @copyright 2017-2017 Silver Rose Wiktor Koźmiński
 *  @license   LICENSE.txt
 */

namespace Orderfiles\Files;

/**
 * Model of file info
 */
class FileInfo implements \JsonSerializable
{
    private $id;
    public $hash;
    public $name;
    public $desc;
    public $author;
    public $isVisibleToCustomer;
    public $isEditableByCustomer;
    public $orderId;
    public $crTimestamp;

    /**
     * New variable to link file with order by cart id. With this approach
     * customer will be able to attach file before placing order (in cart step
     * for example)
     *
     * @since v2.1.0 - 21/09/2017
     *
     * @var integer
     */
    public $cartId;

    /**
     * Gets the value of id.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param integer $id the id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function jsonSerialize() {
        return get_object_vars($this);
    }
}
