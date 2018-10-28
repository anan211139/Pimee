<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/dialogflow/v2/intent.proto

namespace Google\Cloud\Dialogflow\V2;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * The basic card message. Useful for displaying information.
 *
 * Generated from protobuf message <code>google.cloud.dialogflow.v2.Intent.Message.BasicCard</code>
 */
class Intent_Message_BasicCard extends \Google\Protobuf\Internal\Message
{
    /**
     * Optional. The title of the card.
     *
     * Generated from protobuf field <code>string title = 1;</code>
     */
    private $title = '';
    /**
     * Optional. The subtitle of the card.
     *
     * Generated from protobuf field <code>string subtitle = 2;</code>
     */
    private $subtitle = '';
    /**
     * Required, unless image is present. The body text of the card.
     *
     * Generated from protobuf field <code>string formatted_text = 3;</code>
     */
    private $formatted_text = '';
    /**
     * Optional. The image for the card.
     *
     * Generated from protobuf field <code>.google.cloud.dialogflow.v2.Intent.Message.Image image = 4;</code>
     */
    private $image = null;
    /**
     * Optional. The collection of card buttons.
     *
     * Generated from protobuf field <code>repeated .google.cloud.dialogflow.v2.Intent.Message.BasicCard.Button buttons = 5;</code>
     */
    private $buttons;

    public function __construct() {
        \GPBMetadata\Google\Cloud\Dialogflow\V2\Intent::initOnce();
        parent::__construct();
    }

    /**
     * Optional. The title of the card.
     *
     * Generated from protobuf field <code>string title = 1;</code>
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Optional. The title of the card.
     *
     * Generated from protobuf field <code>string title = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setTitle($var)
    {
        GPBUtil::checkString($var, True);
        $this->title = $var;

        return $this;
    }

    /**
     * Optional. The subtitle of the card.
     *
     * Generated from protobuf field <code>string subtitle = 2;</code>
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Optional. The subtitle of the card.
     *
     * Generated from protobuf field <code>string subtitle = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setSubtitle($var)
    {
        GPBUtil::checkString($var, True);
        $this->subtitle = $var;

        return $this;
    }

    /**
     * Required, unless image is present. The body text of the card.
     *
     * Generated from protobuf field <code>string formatted_text = 3;</code>
     * @return string
     */
    public function getFormattedText()
    {
        return $this->formatted_text;
    }

    /**
     * Required, unless image is present. The body text of the card.
     *
     * Generated from protobuf field <code>string formatted_text = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setFormattedText($var)
    {
        GPBUtil::checkString($var, True);
        $this->formatted_text = $var;

        return $this;
    }

    /**
     * Optional. The image for the card.
     *
     * Generated from protobuf field <code>.google.cloud.dialogflow.v2.Intent.Message.Image image = 4;</code>
     * @return \Google\Cloud\Dialogflow\V2\Intent_Message_Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Optional. The image for the card.
     *
     * Generated from protobuf field <code>.google.cloud.dialogflow.v2.Intent.Message.Image image = 4;</code>
     * @param \Google\Cloud\Dialogflow\V2\Intent_Message_Image $var
     * @return $this
     */
    public function setImage($var)
    {
        GPBUtil::checkMessage($var, \Google\Cloud\Dialogflow\V2\Intent_Message_Image::class);
        $this->image = $var;

        return $this;
    }

    /**
     * Optional. The collection of card buttons.
     *
     * Generated from protobuf field <code>repeated .google.cloud.dialogflow.v2.Intent.Message.BasicCard.Button buttons = 5;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * Optional. The collection of card buttons.
     *
     * Generated from protobuf field <code>repeated .google.cloud.dialogflow.v2.Intent.Message.BasicCard.Button buttons = 5;</code>
     * @param \Google\Cloud\Dialogflow\V2\Intent_Message_BasicCard_Button[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setButtons($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Cloud\Dialogflow\V2\Intent_Message_BasicCard_Button::class);
        $this->buttons = $arr;

        return $this;
    }

}

