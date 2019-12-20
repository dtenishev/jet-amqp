<?php

namespace jetphp\rabbitmq\util;

use jetphp\rabbitmq\core\Message;

class MessageBuilder
{

    protected $appId;
    protected $messageId;
    protected $isPersistent;
    protected $deliveryTag;
    protected $consumerTag;
    protected $redelivered;
    protected $routingKey;
    protected $priority;
    protected $correlationId;
    protected $replyTo;
    protected $body;

    protected function createMessage()
    {
        return new Message();
    }

    public function build()
    {
        $obj = $this->createMessage();
        $obj->getProperties()->setAppId($this->appId);
        $obj->getProperties()->setMessageId($this->messageId);
        $obj->getProperties()->setIsPersistent($this->isPersistent);
        $obj->getProperties()->setPriority($this->priority);
        $obj->getProperties()->setReplyTo($this->replyTo);
        $obj->getProperties()->setCorrelationId($this->correlationId);
        $obj->setDeliveryTag($this->deliveryTag);
        $obj->setConsumerTag($this->consumerTag);
        $obj->setRedelivered($this->redelivered);
        $obj->setRoutingKey($this->routingKey);
        $obj->setBody($this->body);
        return $obj;
    }

    /**
     * @param mixed $appId
     * @return MessageBuilder
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * @param mixed $messageId
     * @return MessageBuilder
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
        return $this;
    }

    /**
     * @param mixed $isPersistent
     * @return MessageBuilder
     */
    public function setIsPersistent($isPersistent)
    {
        $this->isPersistent = $isPersistent;
        return $this;
    }

    /**
     * @param mixed $deliveryTag
     * @return MessageBuilder
     */
    public function setDeliveryTag($deliveryTag)
    {
        $this->deliveryTag = $deliveryTag;
        return $this;
    }

    /**
     * @param mixed $consumerTag
     * @return MessageBuilder
     */
    public function setConsumerTag($consumerTag)
    {
        $this->consumerTag = $consumerTag;
        return $this;
    }

    /**
     * @param mixed $redelivered
     * @return MessageBuilder
     */
    public function setRedelivered($redelivered)
    {
        $this->redelivered = $redelivered;
        return $this;
    }

    /**
     * @param mixed $routingKey
     * @return MessageBuilder
     */
    public function setRoutingKey($routingKey)
    {
        $this->routingKey = $routingKey;
        return $this;
    }

    /**
     * @param mixed $priority
     * @return MessageBuilder
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @param mixed $correlationId
     * @return MessageBuilder
     */
    public function setCorrelationId($correlationId)
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    /**
     * @param mixed $replyTo
     * @return MessageBuilder
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    /**
     * @param mixed $body
     * @return MessageBuilder
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

}
